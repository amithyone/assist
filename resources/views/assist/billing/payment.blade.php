@extends('layouts.assist')
@section('title', 'Complete payment')
@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <div class="assist-container" style="max-width: 560px;">
        <span class="assist-eyebrow">{{ $payment->gateway === 'paystack' ? 'Paystack' : 'CheckoutPay' }}</span>
        <h1 class="assist-h2 mb-4">Pay for {{ $plan?->name ?? $payment->plan_slug }}</h1>

        @if ($payment->status === 'approved')
            <div class="assist-alert assist-alert-success mb-4">Payment approved. Your plan is active.</div>
            <a href="{{ route('assist.dashboard') }}" class="assist-btn assist-btn-primary">Go to dashboard</a>
        @elseif ($payment->gateway === 'paystack')
            <p class="assist-text-muted mb-4">Complete payment securely with Paystack (card, bank, USSD).</p>
            <div class="glass-panel" style="padding: 24px; border-radius: 16px;">
                <p><strong>Amount:</strong> ₦{{ number_format($payment->amount, 2) }}</p>
                <p><strong>Reference:</strong> {{ $payment->transaction_id }}</p>
            </div>
            @if (!empty($bank['authorization_url']))
                <a href="{{ $bank['authorization_url'] }}" class="assist-btn assist-btn-primary assist-btn-block mt-4">Pay with Paystack</a>
            @endif
            <p class="assist-text-muted mt-4" style="font-size: 13px;">After payment you will return here automatically.</p>
            <a href="{{ route('assist.dashboard') }}" class="assist-btn assist-btn-outline mt-4">Back to dashboard</a>
        @else
            <p class="assist-text-muted mb-4">Transfer the exact amount to the account below. Your plan activates automatically when payment is approved.</p>
            <div class="glass-panel" style="padding: 24px; border-radius: 16px;">
                <p><strong>Amount:</strong> {{ $payment->currency === 'usd' ? '$' : '₦' }}{{ number_format($payment->amount, 2) }}</p>
                <p><strong>Bank:</strong> {{ $bank['bank_name'] ?? '—' }}</p>
                <p><strong>Account number:</strong> {{ $bank['account_number'] ?? '—' }}</p>
                <p><strong>Account name:</strong> {{ $bank['account_name'] ?? '—' }}</p>
                <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                @if (!empty($bank['expires_at']))
                    <p class="assist-text-muted" style="font-size: 12px;">Expires: {{ $bank['expires_at'] }}</p>
                @endif
            </div>
            <p class="assist-text-muted mt-4" style="font-size: 13px;">Refresh this page after you pay. Webhook approval may take a minute.</p>
            <a href="{{ route('assist.dashboard') }}" class="assist-btn assist-btn-outline mt-4">Back to dashboard</a>
        @endif
    </div>
</section>
@endsection
