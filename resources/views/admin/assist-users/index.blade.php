@extends('layouts.admin')

@section('title', 'Users')
@section('page_title', 'User management')

@section('content')
<div class="assist-admin-card glass-panel">
    <h2>All users</h2>
    <p class="assist-text-muted mb-4">Edit profile, role, plan, or reset password.</p>

    <div class="assist-admin-table-wrap">
        <table class="assist-admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Plan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong><br>
                            <span class="assist-text-muted">{{ $user->email }}</span>
                        </td>
                        <td>
                            @if ($user->is_admin ?? false)
                                <span class="assist-admin-badge assist-admin-badge-admin">Admin</span>
                            @else
                                <span class="assist-admin-badge assist-admin-badge-user">Member</span>
                            @endif
                        </td>
                        <td>{{ $user->currentPlan()?->name ?? '—' }}</td>
                        <td>
                            <button type="button" class="assist-btn assist-btn-ghost"
                                    onclick="document.getElementById('user-panel-{{ $user->id }}').hidden = false">
                                Edit
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding: 0; border: none;">
                            <div id="user-panel-{{ $user->id }}" class="assist-admin-user-panel" hidden>
                                <form method="POST" action="{{ route('admin.assist.users.update', $user) }}" class="assist-admin-form-grid assist-admin-form-grid-2">
                                    @csrf
                                    @method('PUT')
                                    <x-assist.input label="Name" name="name" :value="$user->name" required />
                                    <x-assist.input label="Email" name="email" type="email" :value="$user->email" required />
                                    <div class="assist-field">
                                        <label>Role</label>
                                        <select name="is_admin" class="assist-select">
                                            <option value="0" @selected(!($user->is_admin ?? false))>Member</option>
                                            <option value="1" @selected($user->is_admin ?? false)>Admin</option>
                                        </select>
                                    </div>
                                    <div class="assist-field">
                                        <label>Plan</label>
                                        <select name="plan_slug" class="assist-select">
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->slug }}" @selected($user->currentPlan()?->slug === $plan->slug)>{{ $plan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="grid-column: 1 / -1;">
                                        <button type="submit" class="assist-btn assist-btn-primary">Save user</button>
                                        <button type="button" class="assist-btn assist-btn-ghost"
                                                onclick="document.getElementById('user-panel-{{ $user->id }}').hidden = true">Cancel</button>
                                    </div>
                                </form>

                                <p class="assist-admin-section-title">Reset password</p>
                                <form method="POST" action="{{ route('admin.assist.users.password', $user) }}" class="assist-admin-form-grid assist-admin-form-grid-2">
                                    @csrf
                                    @method('PUT')
                                    <x-assist.input label="New password" name="password" type="password" required />
                                    <x-assist.input label="Confirm password" name="password_confirmation" type="password" required />
                                    <div>
                                        <button type="submit" class="assist-btn assist-btn-outline">Update password</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Your account</h2>
    <form method="POST" action="{{ route('admin.assist.account.password') }}" class="assist-admin-form-grid assist-admin-form-grid-2" style="max-width: 520px;">
        @csrf
        @method('PUT')
        <x-assist.input label="Current password" name="current_password" type="password" required />
        <div></div>
        <x-assist.input label="New password" name="password" type="password" required />
        <x-assist.input label="Confirm new password" name="password_confirmation" type="password" required />
        <div>
            <button type="submit" class="assist-btn assist-btn-primary">Change my password</button>
        </div>
    </form>
</div>
@endsection
