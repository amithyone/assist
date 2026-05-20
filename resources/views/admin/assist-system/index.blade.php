@extends('layouts.admin')

@section('title', 'System')
@section('page_title', 'System')

@section('content')
<div class="assist-admin-stats">
    <div class="assist-admin-stat glass-panel">
        <p class="assist-admin-stat-label">Installation</p>
        <p class="assist-admin-stat-value" style="font-size: 1.25rem;">{{ $installed ? 'Ready' : 'Pending' }}</p>
        @if ($installed)
            <p class="assist-text-muted" style="font-size: 11px; margin-top: 8px;">{{ basename($lockFile) }}</p>
        @else
            <a href="{{ route('assist.setup.index') }}" class="assist-btn assist-btn-outline mt-4">Open setup wizard</a>
        @endif
    </div>
</div>

@include('admin.partials.payment-gateways', ['payment' => $payment])

<div class="assist-admin-card glass-panel">
    <h2>Database credentials</h2>
    <form method="POST" action="{{ route('admin.assist.system.database') }}" class="assist-admin-form-grid assist-admin-form-grid-2" style="max-width: 640px;">
        @csrf
        <x-assist.input label="Host" name="db_host" :value="old('db_host', env('DB_HOST', '127.0.0.1'))" required />
        <x-assist.input label="Port" name="db_port" :value="old('db_port', env('DB_PORT', '3306'))" required />
        <x-assist.input label="Database" name="db_database" :value="old('db_database', env('DB_DATABASE'))" required />
        <x-assist.input label="Username" name="db_username" :value="old('db_username', env('DB_USERNAME'))" required />
        <x-assist.input label="Password" name="db_password" type="password" placeholder="Leave blank to keep" />
        <div>
            <button type="submit" class="assist-btn assist-btn-primary">Save to .env</button>
        </div>
    </form>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Migrations</h2>
    @if (session('migrate_output'))
        <pre style="background: rgba(0,0,0,0.3); padding: 16px; border-radius: 8px; font-size: 12px; overflow-x: auto; margin-bottom: 16px;">{{ session('migrate_output') }}</pre>
    @endif
    <form method="POST" action="{{ route('admin.assist.system.migrate') }}" onsubmit="return confirm('Run pending migrations?');">
        @csrf
        <label class="assist-checkbox-row">
            <input type="checkbox" name="confirm" value="1" required>
            I want to run migrations
        </label>
        <button type="submit" class="assist-btn assist-btn-outline mt-4">Run migrations</button>
    </form>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Seeders</h2>
    @if (session('seed_output'))
        <pre style="background: rgba(0,0,0,0.3); padding: 16px; border-radius: 8px; font-size: 12px; margin-bottom: 16px;">{{ session('seed_output') }}</pre>
    @endif
    <form method="POST" action="{{ route('admin.assist.system.seed') }}" onsubmit="return confirm('Run plan seeders?');">
        @csrf
        <label class="assist-checkbox-row">
            <input type="checkbox" name="confirm" value="1" required>
            I want to run seeders
        </label>
        <label class="assist-checkbox-row">
            <input type="checkbox" name="include_test_user" value="1">
            Include test user (test@assist.app)
        </label>
        <button type="submit" class="assist-btn assist-btn-outline mt-4">Run seeders</button>
    </form>
</div>

@if (!empty($migrationStatus['output']))
<div class="assist-admin-card glass-panel">
    <h2>Migration status</h2>
    <pre style="background: rgba(0,0,0,0.3); padding: 16px; border-radius: 8px; font-size: 11px; overflow-x: auto; white-space: pre-wrap;">{{ $migrationStatus['output'] }}</pre>
</div>
@endif
@endsection
