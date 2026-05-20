@extends('layouts.assist')

@section('content')
@php
    $i = $intro ?? [];
@endphp
<section class="assist-section" style="padding-top: 120px;">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">{{ $i['eyebrow'] ?? 'Pricing' }}</span>
        <h1 class="assist-h2">{{ $i['heading'] ?? 'Simple, transparent plans.' }}</h1>
        <p class="assist-text-muted" style="max-width: 36rem; margin: 0 auto;">
            {{ $i['subheading'] ?? 'Pay in Naira (Nigeria) or USD (international). Plans cap automated runs — not your creativity in Resolve.' }}
        </p>
        @auth
            <form method="get" action="{{ route('assist.pricing') }}" class="assist-pricing-voucher" style="max-width: 22rem; margin: 24px auto 0; display: flex; gap: 8px;">
                <input type="text" name="voucher" class="assist-input" placeholder="Voucher code" value="{{ request('voucher') }}" style="flex: 1;">
                <button type="submit" class="assist-btn assist-btn-outline assist-btn-sm">Apply</button>
            </form>
            @error('billing')
                <p class="assist-error" style="margin-top: 8px;">{{ $message }}</p>
            @enderror
        @endauth
    </div>
    <div class="assist-container assist-grid-3">
        @foreach ($plans as $plan)
            @php
                $highlight = (bool) $plan->is_featured;
                $isFree = $plan->slug === 'free';
                $voucherQ = request('voucher') ? ['voucher' => request('voucher')] : [];
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
                        @php
                            $ngnGateways = $paymentGateways->gatewaysForCurrency('ngn');
                            $usdGateways = $paymentGateways->gatewaysForCurrency('usd');
                        @endphp
                        @if (count($ngnGateways) > 0)
                            <p class="assist-text-muted" style="font-size: 12px; margin-bottom: 8px;">Pay in NGN</p>
                            @foreach ($ngnGateways as $gw)
                                <a href="{{ route('assist.billing.upgrade', array_merge(['plan' => $plan->slug, 'currency' => 'ngn', 'gateway' => $gw], $voucherQ)) }}"
                                   class="assist-btn {{ ($highlight && $loop->first) ? 'assist-btn-primary' : 'assist-btn-outline' }} assist-btn-block"
                                   @if($loop->index > 0) style="margin-top:8px;" @endif>
                                    {{ $paymentGateways->gatewayLabel($gw) }}
                                    @if ($paymentGateways->gatewayDescription($gw))
                                        <span style="font-weight: 400; opacity: 0.85;"> — {{ $paymentGateways->gatewayDescription($gw) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                        @if (count($usdGateways) > 0)
                            <p class="assist-text-muted" style="font-size: 12px; margin: 12px 0 8px;">Pay in USD</p>
                            @foreach ($usdGateways as $gw)
                                <a href="{{ route('assist.billing.upgrade', array_merge(['plan' => $plan->slug, 'currency' => 'usd', 'gateway' => $gw], $voucherQ)) }}"
                                   class="assist-btn assist-btn-outline assist-btn-block"
                                   @if($loop->index > 0) style="margin-top:8px;" @endif>
                                    {{ $paymentGateways->gatewayLabel($gw) }}
                                    @if ($paymentGateways->gatewayDescription($gw))
                                        <span style="font-weight: 400; opacity: 0.85;"> — {{ $paymentGateways->gatewayDescription($gw) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                        @if (count($ngnGateways) === 0 && count($usdGateways) === 0)
                            <p class="assist-text-muted" style="font-size: 13px;">Payments unavailable — contact support.</p>
                        @endif
                    @endif
                @else
                    <a href="{{ route('assist.register') }}" class="assist-btn {{ $highlight ? 'assist-btn-primary' : 'assist-btn-outline' }} assist-btn-block">Get Started</a>
                @endauth
            </div>
        @endforeach
    </div>
</section>
@endsection
