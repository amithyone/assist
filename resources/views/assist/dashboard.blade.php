@extends('layouts.assist')
@section('title', 'Dashboard')
@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <div class="assist-container">
        <span class="assist-eyebrow">Account</span>
        <h1 class="assist-h2 mb-4">Hello, {{ auth()->user()->name }}</h1>
        <div class="assist-dash-grid">
            <div class="glass-panel assist-stat">
                <p class="assist-stat-label">Current plan</p>
                <p class="assist-stat-value">{{ $plan?->name ?? 'None' }}</p>
            </div>
            <div class="glass-panel assist-stat">
                <p class="assist-stat-label">Billing period</p>
                <p class="assist-stat-value">{{ $limits['period'] ?? now()->format('Y-m') }}</p>
            </div>
        </div>
        @if (!empty($limits['features']))
            <h2 style="font-size: 1.25rem; margin: 32px 0 16px;">Usage this month</h2>
            <div class="assist-dash-grid">
                @foreach ($limits['features'] as $feature => $data)
                    <div class="glass-panel assist-stat">
                        <p class="assist-stat-label">{{ str_replace('_', ' ', $feature) }}</p>
                        <p class="assist-stat-value">
                            {{ $data['used'] }}
                            @if ($data['limit'] !== null)
                                / {{ $data['limit'] }}
                            @else
                                <span class="assist-text-muted">(unlimited)</span>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
        <div style="margin-top: 48px; display: flex; flex-wrap: gap: 16px;">
            <a href="{{ config('assist.download_url') }}" class="assist-btn assist-btn-primary">Download Assist for Mac</a>
            <a href="{{ route('assist.pricing') }}" class="assist-btn assist-btn-outline">Upgrade plan</a>
        </div>
    </div>
</section>
@endsection
