<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    public function __construct(
        protected PaymentActivationService $activation
    ) {}

    public function initializeTransaction(User $user, Plan $plan, string $currency = 'ngn'): Payment
    {
        $currency = strtolower($currency);
        if ($currency !== 'ngn') {
            throw new RuntimeException('Paystack billing is available for NGN only.');
        }

        $amount = $plan->priceForCurrency($currency);
        if ($amount <= 0) {
            throw new RuntimeException('This plan does not require payment.');
        }

        $reference = 'assist-'.$user->id.'-'.$plan->slug.'-'.time();
        $callback = rtrim(config('app.url'), '/').'/billing/payment/'.$reference;

        $response = $this->request('POST', '/transaction/initialize', [
            'email' => $user->email,
            'amount' => (int) round($amount * 100),
            'currency' => 'NGN',
            'reference' => $reference,
            'callback_url' => $callback,
            'metadata' => [
                'user_id' => $user->id,
                'plan_slug' => $plan->slug,
                'custom_fields' => [
                    [
                        'display_name' => 'Plan',
                        'variable_name' => 'plan',
                        'value' => $plan->name,
                    ],
                ],
            ],
        ]);

        if (! ($response['status'] ?? false)) {
            throw new RuntimeException($response['message'] ?? 'Paystack initialize failed.');
        }

        $data = $response['data'] ?? [];

        return Payment::create([
            'user_id' => $user->id,
            'plan_slug' => $plan->slug,
            'gateway' => 'paystack',
            'transaction_id' => $data['reference'] ?? $reference,
            'external_reference' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'checkout_payload' => $data,
        ]);
    }

    public function verifyAndActivate(Payment $payment): Payment
    {
        if ($payment->status === 'approved') {
            return $payment;
        }

        $response = $this->request('GET', '/transaction/verify/'.urlencode($payment->transaction_id));
        $data = $response['data'] ?? [];

        if (($data['status'] ?? '') === 'success') {
            return $this->activation->approve($payment, ['verify' => $data]);
        }

        return $payment;
    }

    public function handleWebhook(array $payload): ?Payment
    {
        if (($payload['event'] ?? '') !== 'charge.success') {
            return null;
        }

        $data = $payload['data'] ?? [];
        $reference = $data['reference'] ?? null;
        if (! $reference) {
            return null;
        }

        $payment = Payment::where('transaction_id', $reference)->where('gateway', 'paystack')->first();
        if (! $payment || $payment->status === 'approved') {
            return $payment;
        }

        return $this->activation->approve($payment, ['webhook' => $payload]);
    }

    public function verifyWebhookSignature(string $rawBody, ?string $signature): bool
    {
        $secret = config('assist.paystack.secret_key');
        if (! $secret || ! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $rawBody, $secret), $signature);
    }

    protected function request(string $method, string $path, array $body = []): array
    {
        $secret = config('assist.paystack.secret_key');
        if (! $secret) {
            throw new RuntimeException('Paystack secret key is not configured.');
        }

        $url = 'https://api.paystack.co'.$path;
        $http = Http::withToken($secret)
            ->acceptJson()
            ->timeout(30);

        $response = match (strtoupper($method)) {
            'GET' => $http->get($url),
            default => $http->post($url, $body),
        };

        if (! $response->successful()) {
            throw new RuntimeException('Paystack HTTP '.$response->status().': '.$response->body());
        }

        return $response->json() ?? [];
    }
}
