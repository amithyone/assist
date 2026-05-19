@extends('layouts.assist')

@section('title', 'Pricing')

@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">Pricing</span>
        <h1 class="assist-h2">Simple, transparent plans.</h1>
    </div>
    <div class="assist-container assist-grid-3">
        @foreach ($cards as $card)
            <div class="glass-panel assist-pricing-card {{ ($card['highlight'] ?? false) ? 'highlight' : '' }}" style="position: relative;">
                @if (!empty($card['badge']))
                    <span class="new-badge" style="position: absolute; top: 16px; right: 16px;">{{ $card['badge'] }}</span>
                @endif
                <div>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">{{ $card['name'] }}</h3>
                    <div class="assist-pricing-price">
                        {{ $card['price'] }}
                        @if (!empty($card['period']))
                            <span class="assist-text-muted" style="font-size: 16px;">{{ $card['period'] }}</span>
                        @endif
                    </div>
                    <p class="assist-text-muted" style="margin-top: 12px;">{{ $card['description'] }}</p>
                </div>
                <ul class="assist-pricing-features">
                    @foreach ($card['features'] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
                <a href="{{ $card['cta_url'] }}" class="assist-btn {{ ($card['highlight'] ?? false) ? 'assist-btn-primary' : 'assist-btn-outline' }} assist-btn-block">
                    {{ $card['cta_label'] ?? 'Get Started' }}
                </a>
            </div>
        @endforeach
    </div>
    <div class="assist-container" style="margin-top: 80px;">
        <div class="glass-panel" style="padding: 48px; border-radius: 3.5rem;">
            <div class="assist-grid-2 items-center">
                <div>
                    <h2 class="assist-h2 mb-4">Need a custom solution?</h2>
                    <p class="assist-text-muted">Volume discounts and custom deployments for post-production houses and educational institutions.</p>
                </div>
                <div style="text-align: right;">
                    <a href="mailto:{{ config('assist.support_email') }}" class="assist-btn assist-btn-outline">Contact Sales</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
