<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\Voucher;
use RuntimeException;

class PaymentGatewayManager
{
    public const GATEWAY_CHECKOUT = 'checkoutpay';

    public const GATEWAY_PAYSTACK = 'paystack';

    public function __construct(
        protected CheckoutPayService $checkoutPay,
        protected PaystackService $paystack
    ) {}

    /**
     * @return list<string>
     */
    public function allGatewayIds(): array
    {
        return array_keys(config('assist.payment.gateways', []));
    }

    /**
     * Gateways turned on in admin (may be unconfigured).
     *
     * @return list<string>
     */
    public function enabledGatewayIds(): array
    {
        $enabled = config('assist.payment.enabled_gateways', []);
        $known = $this->allGatewayIds();
        $enabled = array_values(array_intersect($enabled, $known));

        return $enabled !== [] ? $enabled : [self::GATEWAY_CHECKOUT, self::GATEWAY_PAYSTACK];
    }

    /**
     * Enabled gateways that have API keys set.
     *
     * @return list<string>
     */
    public function configuredGatewayIds(): array
    {
        return array_values(array_filter(
            $this->enabledGatewayIds(),
            fn (string $id) => $this->isConfigured($id)
        ));
    }

    public function defaultGateway(): string
    {
        $default = (string) config('assist.payment.default_gateway', self::GATEWAY_CHECKOUT);
        $configured = $this->configuredGatewayIds();

        if (in_array($default, $configured, true)) {
            return $default;
        }

        return $configured[0] ?? self::GATEWAY_CHECKOUT;
    }

    public function gatewayLabel(string $gateway): string
    {
        $meta = config("assist.payment.gateways.{$gateway}", []);

        return (string) ($meta['label'] ?? ucfirst($gateway));
    }

    public function gatewayDescription(string $gateway): string
    {
        $meta = config("assist.payment.gateways.{$gateway}", []);

        return (string) ($meta['description'] ?? '');
    }

    /**
     * Configured + enabled gateways valid for a billing currency.
     *
     * @return list<string>
     */
    public function gatewaysForCurrency(string $currency): array
    {
        $currency = strtolower($currency);

        return array_values(array_filter(
            $this->configuredGatewayIds(),
            function (string $gateway) use ($currency) {
                $currencies = config("assist.payment.gateways.{$gateway}.currencies", []);

                return in_array($currency, $currencies, true);
            }
        ));
    }

    public function gatewayForCurrency(string $currency): string
    {
        return $this->resolveGateway($currency, null);
    }

    public function resolveGateway(string $currency, ?string $preferred): string
    {
        $available = $this->gatewaysForCurrency($currency);

        if ($available === []) {
            throw new RuntimeException(
                'No payment gateway is configured for '.strtoupper($currency).'. '
                .'Enable and configure a gateway in Admin → Payments.'
            );
        }

        $preferred = $preferred ? strtolower(trim($preferred)) : null;

        if ($preferred && in_array($preferred, $available, true)) {
            return $preferred;
        }

        $default = $this->defaultGateway();
        if (in_array($default, $available, true)) {
            return $default;
        }

        return $available[0];
    }

    public function createPayment(
        User $user,
        Plan $plan,
        string $currency = 'ngn',
        ?Voucher $voucher = null,
        ?array $priceBreakdown = null,
        ?string $gateway = null,
    ): Payment {
        $currency = strtolower($currency);
        $gateway = $this->resolveGateway($currency, $gateway);
        $this->assertConfigured($gateway);

        return match ($gateway) {
            self::GATEWAY_PAYSTACK => $this->paystack->initializeTransaction(
                $user,
                $plan,
                $currency,
                $voucher,
                $priceBreakdown
            ),
            default => $this->checkoutPay->createPaymentRequest(
                $user,
                $plan,
                $currency,
                $voucher,
                $priceBreakdown
            ),
        };
    }

    public function syncPaymentStatus(Payment $payment): Payment
    {
        if ($payment->status === 'approved') {
            return $payment;
        }

        return match ($payment->gateway) {
            self::GATEWAY_PAYSTACK => $this->paystack->verifyAndActivate($payment),
            default => $this->syncCheckoutPay($payment),
        };
    }

    protected function syncCheckoutPay(Payment $payment): Payment
    {
        try {
            $status = $this->checkoutPay->getPaymentStatus($payment->transaction_id);
            if (($status['data']['status'] ?? '') === 'approved') {
                $this->checkoutPay->activatePlanFromWebhook([
                    'event' => 'payment.approved',
                    'transaction_id' => $payment->transaction_id,
                    'service' => 'assist-plan:'.$payment->plan_slug,
                ]);

                return $payment->fresh();
            }
        } catch (\Throwable) {
            // optional poll
        }

        return $payment;
    }

    public function isConfigured(string $gateway): bool
    {
        return match ($gateway) {
            self::GATEWAY_PAYSTACK => (bool) config('assist.paystack.secret_key'),
            self::GATEWAY_CHECKOUT => (bool) config('assist.checkout.api_key'),
            default => false,
        };
    }

    public function assertConfigured(string $gateway): void
    {
        if (! in_array($gateway, $this->enabledGatewayIds(), true)) {
            throw new RuntimeException(
                ucfirst($gateway).' is disabled. Enable it in Admin → Payments.'
            );
        }

        if (! $this->isConfigured($gateway)) {
            throw new RuntimeException(
                ucfirst($gateway).' is not configured. Add API keys in Admin → Payments.'
            );
        }
    }

    /**
     * @return array<string, array{label: string, description: string, enabled: bool, configured: bool, currencies: list<string>}>
     */
    public function adminGatewayStatus(): array
    {
        $out = [];
        foreach ($this->allGatewayIds() as $id) {
            $meta = config("assist.payment.gateways.{$id}", []);
            $out[$id] = [
                'label' => (string) ($meta['label'] ?? $id),
                'description' => (string) ($meta['description'] ?? ''),
                'enabled' => in_array($id, $this->enabledGatewayIds(), true),
                'configured' => $this->isConfigured($id),
                'currencies' => $meta['currencies'] ?? [],
            ];
        }

        return $out;
    }
}
