<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\Voucher;
use RuntimeException;

class PaymentGatewayManager
{
    public function __construct(
        protected CheckoutPayService $checkoutPay,
        protected PaystackService $paystack
    ) {}

    public function defaultGateway(): string
    {
        return config('assist.payment.default_gateway', 'checkoutpay');
    }

    public function gatewayForCurrency(string $currency): string
    {
        $gateway = $this->defaultGateway();
        if ($gateway === 'paystack' && strtolower($currency) !== 'ngn') {
            return 'checkoutpay';
        }

        return $gateway;
    }

    public function createPayment(
        User $user,
        Plan $plan,
        string $currency = 'ngn',
        ?Voucher $voucher = null,
        ?array $priceBreakdown = null,
    ): Payment {
        $currency = strtolower($currency);
        $gateway = $this->gatewayForCurrency($currency);

        return match ($gateway) {
            'paystack' => $this->paystack->initializeTransaction($user, $plan, $currency, $voucher, $priceBreakdown),
            default => $this->checkoutPay->createPaymentRequest($user, $plan, $currency, $voucher, $priceBreakdown),
        };
    }

    public function syncPaymentStatus(Payment $payment): Payment
    {
        if ($payment->status === 'approved') {
            return $payment;
        }

        return match ($payment->gateway) {
            'paystack' => $this->paystack->verifyAndActivate($payment),
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
            'paystack' => (bool) config('assist.paystack.secret_key'),
            'checkoutpay' => (bool) config('assist.checkout.api_key'),
            default => false,
        };
    }

    public function assertConfigured(string $gateway): void
    {
        if (! $this->isConfigured($gateway)) {
            throw new RuntimeException(ucfirst($gateway).' is not configured. Add keys in Admin → System → Payment gateways.');
        }
    }
}
