@extends('layouts.assist')
@section('title', 'Dashboard')
@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <div class="assist-container">
        <span class="assist-eyebrow">Account</span>
        <h1 class="assist-h2 mb-4">Hello, {{ auth()->user()->name }}</h1>
        @if (auth()->user()->is_admin ?? false)
            <p class="assist-text-muted mb-4"><a href="{{ route('admin.assist.dashboard') }}">Open admin dashboard</a></p>
        @endif
        <div class="assist-dash-grid">
            <div class="glass-panel assist-stat">
                <p class="assist-stat-label">Current plan</p>
                <p class="assist-stat-value">{{ $limits['plan']['name'] ?? 'None' }}</p>
            </div>
            <div class="glass-panel assist-stat">
                <p class="assist-stat-label">Usage period</p>
                <p class="assist-stat-value">{{ $limits['period'] ?? '—' }} ({{ $limits['period_type'] ?? 'monthly' }})</p>
            </div>
            @if (!empty($limits['subscription']['ends_at']))
                <div class="glass-panel assist-stat">
                    <p class="assist-stat-label">Renews / ends</p>
                    <p class="assist-stat-value" style="font-size: 1rem;">{{ \Carbon\Carbon::parse($limits['subscription']['ends_at'])->format('M j, Y') }}</p>
                </div>
            @endif
        </div>
        @if (!empty($limits['features']))
            <h2 style="font-size: 1.25rem; margin: 32px 0 16px;">Usage this {{ ($limits['period_type'] ?? 'monthly') === 'weekly' ? 'week' : 'month' }}</h2>
            <div class="assist-dash-grid">
                @foreach ($limits['features'] as $feature => $data)
                    <div class="glass-panel assist-stat">
                        <p class="assist-stat-label">{{ str_replace('_', ' ', ucwords($feature, '_')) }}</p>
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
        @php $assistDownloads = app(\App\Services\AssistAppReleaseService::class)->availableDownloads(); @endphp
        @if (count($assistDownloads) > 0)
            <div style="margin-top: 48px;">
                <h2 style="font-size: 1.25rem; margin-bottom: 16px;">Download Assist</h2>
                <x-assist.downloads layout="list" />
            </div>
        @endif
        @if (($limits['plan']['slug'] ?? 'free') !== 'unlimited')
            <div style="margin-top: 24px;">
                <a href="{{ route('assist.pricing') }}" class="assist-btn assist-btn-outline">Upgrade plan</a>
            </div>
        @endif
    </div>
</section>
@endsection
