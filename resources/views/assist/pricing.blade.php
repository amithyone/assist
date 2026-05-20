@extends('layouts.assist')

@section('title', 'Pricing')

@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">Pricing</span>
        <h1 class="assist-h2">Simple, transparent plans.</h1>
        <p class="assist-text-muted">Pay in Naira (Nigeria) or USD (international).</p>
    </div>
    <div class="assist-container assist-grid-3">
        @foreach ($plans as $plan)
            @php
                $highlight = $plan->slug === 'pro';
                $isFree = $plan->slug === 'free';
            @endphp
            <div class="glass-panel assist-pricing-card {{ $highlight ? 'highlight' : '' }}" style="position: relative;">
                @if ($highlight)
                    <span class="new-badge" style="position: absolute; top: 16px; right: 16px;">Most Popular</span>
                @endif
                <div>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">{{ $plan->name }}</h3>
                    <div class="assist-pricing-price">
                        @if ($isFree)
                            Free
                        @else
                            ₦{{ number_format($plan->price_ngn ?? 0) }}
                            <span class="assist-text-muted" style="font-size: 16px;">/ ${{ number_format($plan->price_usd ?? 0, 0) }}</span>
                            <span class="assist-text-muted" style="font-size: 14px;">/mo</span>
                        @endif
                    </div>
                    <p class="assist-text-muted" style="margin-top: 12px;">{{ $plan->description }}</p>
                </div>
                <ul class="assist-pricing-features">
                    @foreach ($plan->marketingFeatures() as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
                @auth
                    @if ($isFree)
                        <a href="{{ route('assist.dashboard') }}" class="assist-btn assist-btn-outline assist-btn-block">Current</a>
                    @else
                        <a href="{{ route('assist.billing.upgrade', ['plan' => $plan->slug, 'currency' => 'ngn']) }}" class="assist-btn {{ $highlight ? 'assist-btn-primary' : 'assist-btn-outline' }} assist-btn-block">Upgrade (NGN)</a>
                        <a href="{{ route('assist.billing.upgrade', ['plan' => $plan->slug, 'currency' => 'usd']) }}" class="assist-btn assist-btn-outline assist-btn-block" style="margin-top:8px;">Upgrade (USD)</a>
                    @endif
                @else
                    <a href="{{ route('assist.register') }}" class="assist-btn {{ $highlight ? 'assist-btn-primary' : 'assist-btn-outline' }} assist-btn-block">Get Started</a>
                @endauth
            </div>
        @endforeach
    </div>
</section>
@endsection
