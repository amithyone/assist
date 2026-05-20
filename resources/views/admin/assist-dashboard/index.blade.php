@extends('layouts.admin')

@section('title', 'Overview')
@section('page_title', 'Overview')

@section('content')
<div class="assist-admin-stats">
    <div class="assist-admin-stat glass-panel">
        <p class="assist-admin-stat-label">Total users</p>
        <p class="assist-admin-stat-value">{{ number_format($userCount) }}</p>
    </div>
    <div class="assist-admin-stat glass-panel">
        <p class="assist-admin-stat-label">Approved payments</p>
        <p class="assist-admin-stat-value">{{ number_format($paymentCount) }}</p>
    </div>
    <div class="assist-admin-stat glass-panel">
        <p class="assist-admin-stat-label">Active plans</p>
        <p class="assist-admin-stat-value">{{ $plans->count() }}</p>
    </div>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Quick links</h2>
    <div class="flex gap-4" style="flex-wrap: wrap;">
        <a href="{{ route('admin.assist.users') }}" class="assist-btn assist-btn-outline">Manage users</a>
        <a href="{{ route('admin.assist.settings') }}" class="assist-btn assist-btn-primary">Payment setup</a>
        <a href="{{ route('admin.assist.downloads') }}" class="assist-btn assist-btn-outline">App release</a>
    </div>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Plans</h2>
    <div class="assist-admin-table-wrap">
        <table class="assist-admin-table">
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>NGN</th>
                    <th>USD</th>
                    <th>Period</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plans as $plan)
                    <tr>
                        <td><strong>{{ $plan->name }}</strong> <span class="assist-text-muted">({{ $plan->slug }})</span></td>
                        <td>₦{{ number_format($plan->price_ngn ?? 0) }}</td>
                        <td>${{ number_format($plan->price_usd ?? 0, 2) }}</td>
                        <td>{{ $plan->usage_period }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Recent payments</h2>
    <div class="assist-admin-table-wrap">
        <table class="assist-admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentPayments as $p)
                    <tr>
                        <td>{{ $p->user?->email ?? '—' }}</td>
                        <td>{{ $p->plan_slug }}</td>
                        <td>{{ strtoupper($p->currency) }} {{ number_format($p->amount, 2) }}</td>
                        <td><span class="assist-admin-badge assist-admin-badge-muted">{{ $p->gateway ?? 'checkoutpay' }}</span></td>
                        <td>
                            <span class="assist-admin-badge {{ $p->status === 'approved' ? 'assist-admin-badge-success' : 'assist-admin-badge-muted' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="assist-text-muted">{{ $p->created_at?->format('M j, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="assist-text-muted">No payments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
