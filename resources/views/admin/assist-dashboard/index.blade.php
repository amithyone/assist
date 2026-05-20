<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assist Admin</title>
    <link rel="stylesheet" href="{{ asset('assist/assist-site.css') }}">
</head>
<body style="background: #0f172a; color: #e2e8f0; padding: 40px;">
@include('admin.partials.nav')
<div class="admin-wrap" style="max-width: 960px; margin: 0 auto;">
    <h1 style="font-size: 28px; margin-bottom: 24px;">Admin overview</h1>
    <p style="margin-bottom: 24px;"><a href="{{ route('admin.assist.downloads') }}" style="color: #818cf8;">Upload macOS app for public download →</a></p>
    @if (session('status'))
        <p style="color: #34d399; margin-bottom: 16px;">{{ session('status') }}</p>
    @endif
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div class="glass-panel" style="padding: 20px;"><p style="font-size: 12px; opacity: .7;">Users</p><p style="font-size: 32px; font-weight: 700;">{{ $userCount }}</p></div>
        <div class="glass-panel" style="padding: 20px;"><p style="font-size: 12px; opacity: .7;">Approved payments</p><p style="font-size: 32px; font-weight: 700;">{{ $paymentCount }}</p></div>
    </div>
    <h2 style="font-size: 18px; margin-bottom: 12px;">Plans</h2>
    <ul style="margin-bottom: 32px;">
        @foreach ($plans as $plan)
            <li>{{ $plan->name }} — ₦{{ number_format($plan->price_ngn ?? 0) }} / ${{ number_format($plan->price_usd ?? 0, 2) }} ({{ $plan->usage_period }})</li>
        @endforeach
    </ul>
    <h2 style="font-size: 18px; margin-bottom: 12px;">Recent payments</h2>
    <table style="width: 100%; font-size: 14px;">
        <thead><tr><th>User</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
            @forelse ($recentPayments as $p)
                <tr>
                    <td>{{ $p->user?->email }}</td>
                    <td>{{ $p->plan_slug }}</td>
                    <td>{{ $p->currency }} {{ $p->amount }}</td>
                    <td>{{ $p->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
