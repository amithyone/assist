@extends('layouts.admin')

@section('title', 'Pricing & plans')
@section('page_title', 'Pricing & plans')

@section('content')
<div class="assist-admin-card glass-panel mb-4" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
    <p class="assist-text-muted mb-0">Edit prices, feature limits, and which plan is highlighted on the public pricing page.</p>
    <div class="flex gap-3" style="flex-wrap: wrap;">
        <a href="{{ route('assist.pricing') }}" class="assist-btn assist-btn-outline" target="_blank" rel="noopener">View pricing page</a>
        <a href="{{ route('admin.assist.vouchers') }}" class="assist-btn assist-btn-outline">Manage vouchers</a>
    </div>
</div>

<div class="assist-admin-card glass-panel">
    <div class="assist-admin-table-wrap">
        <table class="assist-admin-table">
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>NGN / mo</th>
                    <th>USD / mo</th>
                    <th>Period</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plans as $plan)
                    <tr>
                        <td>
                            <strong>{{ $plan->name }}</strong>
                            <span class="assist-text-muted">({{ $plan->slug }})</span>
                            @if ($plan->is_featured)
                                <span class="assist-admin-badge assist-admin-badge-success" style="margin-left: 6px;">Featured</span>
                            @endif
                        </td>
                        <td>₦{{ number_format($plan->price_ngn ?? 0) }}</td>
                        <td>${{ number_format($plan->price_usd ?? 0, 2) }}</td>
                        <td>{{ $plan->usage_period }}</td>
                        <td>{{ $plan->sort_order }}</td>
                        <td>
                            @if ($plan->is_active)
                                <span class="assist-admin-badge assist-admin-badge-success">Active</span>
                            @else
                                <span class="assist-admin-badge assist-admin-badge-muted">Hidden</span>
                            @endif
                        </td>
                        <td><a href="{{ route('admin.assist.plans.edit', $plan) }}" class="assist-btn assist-btn-ghost assist-btn-sm">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
