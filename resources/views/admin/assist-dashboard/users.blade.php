<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assist Users</title>
    <link rel="stylesheet" href="{{ asset('assist/assist-site.css') }}">
</head>
<body style="background: #0f172a; color: #e2e8f0; padding: 40px;">
@include('admin.partials.nav')
<div style="max-width: 1100px; margin: 0 auto;">
    <h1 style="font-size: 28px; margin-bottom: 24px;">Users & subscriptions</h1>
    @if (session('status'))
        <p style="color: #34d399; margin-bottom: 16px;">{{ session('status') }}</p>
    @endif
    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 1px solid #334155;">
                <th style="padding: 8px;">Email</th>
                <th>Plan</th>
                <th>Admin</th>
                <th>Change plan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr style="border-bottom: 1px solid #1e293b;">
                    <td style="padding: 8px;">{{ $user->email }}</td>
                    <td>{{ $user->currentPlan()?->name ?? '—' }}</td>
                    <td>{{ ($user->is_admin ?? false) ? 'Yes' : 'No' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.assist.users.plan') }}" style="display: flex; gap: 8px;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <select name="plan_slug" style="padding: 6px; border-radius: 6px;">
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->slug }}" @selected($user->currentPlan()?->slug === $plan->slug)>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" style="padding: 6px 12px; border-radius: 6px;">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 16px;">{{ $users->links() }}</div>
</div>
</body>
</html>
