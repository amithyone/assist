<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CheckoutPayService
{
    public function createPaymentRequest(User $user, Plan $plan, string $currency = 'ngn'): Payment
    {
        $currency = strtolower($currency);
        $amount = $plan->priceForCurrency($currency);
        if ($amount <= 0) {
            throw new RuntimeException('This plan does not require payment.');
        }

        $externalRef = 'assist-'.$user->id.'-'.$plan->slug.'-'.time();
        $payload = [
            'name' => $user->name,
            'payer_name' => $user->name,
            'amount' => $amount,
            'service' => 'assist-plan:'.$plan->slug,
            'webhook_url' => config('assist.checkout.webhook_url'),
        ];

        $partnerId = config('assist.checkout.dev_program_partner_id');
        if ($partnerId) {
            $payload['developer_program_partner_business_id'] = (int) $partnerId;
        }

        $response = $this->request('POST', '/payment-request', $payload);
        if (! ($response['success'] ?? false)) {
            throw new RuntimeException($response['message'] ?? 'CheckoutPay payment request failed.');
        }

        $data = $response['data'] ?? [];

        return Payment::create([
            'user_id' => $user->id,
            'plan_slug' => $plan->slug,
            'transaction_id' => $data['transaction_id'] ?? ('pending-'.uniqid()),
            'external_reference' => $externalRef,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'checkout_payload' => $data,
        ]);
    }

    public function getPaymentStatus(string $transactionId): array
    {
        return $this->request('GET', '/payment/'.urlencode($transactionId));
    }

    public function activatePlanFromWebhook(array $webhook): ?Payment
    {
        if (($webhook['event'] ?? '') !== 'payment.approved') {
            return null;
        }

        $transactionId = $webhook['transaction_id'] ?? null;
        if (! $transactionId) {
            return null;
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();
        if (! $payment) {
            $service = $webhook['service'] ?? '';
            if (str_starts_with($service, 'assist-plan:')) {
                $slug = substr($service, strlen('assist-plan:'));
                $payment = Payment::where('plan_slug', $slug)
                    ->where('status', 'pending')
                    ->latest('id')
                    ->first();
            }
        }

        if (! $payment || $payment->status === 'approved') {
            if ($payment && $payment->status === 'approved') {
                return $payment;
            }

            return null;
        }

        $payment->update([
            'status' => 'approved',
            'approved_at' => now(),
            'checkout_payload' => array_merge($payment->checkout_payload ?? [], ['webhook' => $webhook]),
        ]);

        $plan = Plan::where('slug', $payment->plan_slug)->where('is_active', true)->first();
        if (! $plan) {
            return $payment;
        }

        $user = $payment->user;
        $user->userPlans()->where('status', 'active')->update(['status' => 'cancelled', 'ends_at' => now()]);

        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        return $payment;
    }

    protected function request(string $method, string $path, array $body = []): array
    {
        $base = rtrim(config('assist.checkout.base_url'), '/');
        $apiKey = config('assist.checkout.api_key');
        if (! $apiKey) {
            throw new RuntimeException('CheckoutPay API key is not configured.');
        }

        $url = $base.$path;
        $http = Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(30);

        $response = match (strtoupper($method)) {
            'GET' => $http->get($url),
            'PATCH' => $http->patch($url, $body),
            default => $http->post($url, $body),
        };

        if (! $response->successful()) {
            throw new RuntimeException('CheckoutPay HTTP '.$response->status().': '.$response->body());
        }

        return $response->json() ?? [];
    }
}
