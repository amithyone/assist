<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assist System — Admin</title>
    <link rel="stylesheet" href="{{ asset('assist/assist-site.css') }}">
    <style>
        body { padding: 24px; }
        .admin-wrap { max-width: 900px; margin: 0 auto; }
        .admin-card { padding: 24px; border-radius: 16px; margin-bottom: 24px; }
        pre { background: rgba(0,0,0,0.3); padding: 16px; border-radius: 8px; font-size: 12px; overflow-x: auto; white-space: pre-wrap; }
        .admin-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
        .admin-nav { margin-bottom: 24px; }
        .admin-nav a { margin-right: 16px; color: var(--primary); font-size: 14px; }
    </style>
</head>
<body class="assist-body">
<div class="admin-wrap">
    @include('admin.partials.nav')

    <h1 class="assist-h2 mb-4">Assist system</h1>

    @if (session('status'))
        <div class="glass-panel admin-card assist-alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="glass-panel admin-card assist-alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="glass-panel admin-card">
        <p class="assist-stat-label">Installation</p>
        <p class="assist-stat-value">{{ $installed ? 'Installed' : 'Not installed' }}</p>
        @if ($installed)
            <p class="assist-text-muted" style="margin-top: 8px; font-size: 12px;">Lock file: {{ $lockFile }}</p>
        @else
            <p class="assist-text-muted" style="margin-top: 8px;">
                <a href="{{ route('assist.setup.index') }}">Open setup wizard</a>
            </p>
        @endif
    </div>

    <div class="glass-panel admin-card">
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Database credentials</h2>
        <form method="POST" action="{{ route('admin.assist.system.database') }}">
            @csrf
            <div class="assist-field">
                <label>Host</label>
                <input class="assist-input" name="db_host" value="{{ old('db_host', env('DB_HOST', '127.0.0.1')) }}" required>
            </div>
            <div class="assist-field">
                <label>Port</label>
                <input class="assist-input" name="db_port" value="{{ old('db_port', env('DB_PORT', '3306')) }}" required>
            </div>
            <div class="assist-field">
                <label>Database</label>
                <input class="assist-input" name="db_database" value="{{ old('db_database', env('DB_DATABASE')) }}" required>
            </div>
            <div class="assist-field">
                <label>Username</label>
                <input class="assist-input" name="db_username" value="{{ old('db_username', env('DB_USERNAME')) }}" required>
            </div>
            <div class="assist-field">
                <label>Password</label>
                <input class="assist-input" name="db_password" type="password" placeholder="Leave blank to keep current">
            </div>
            <button type="submit" class="assist-btn assist-btn-outline">Save to .env</button>
        </form>
    </div>

    <div class="glass-panel admin-card">
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Migrations</h2>
        @if ($migrationStatus['ok'])
            <pre>{{ $migrationStatus['output'] }}</pre>
        @else
            <p class="assist-alert-error">{{ $migrationStatus['output'] }}</p>
        @endif
        @if (session('migrate_output'))
            <pre style="margin-top: 12px;">{{ session('migrate_output') }}</pre>
        @endif
        <form method="POST" action="{{ route('admin.assist.system.migrate') }}" class="admin-actions" onsubmit="return confirm('Run pending migrations?');">
            @csrf
            <label class="assist-checkbox-row">
                <input type="checkbox" name="confirm" value="1" required> I understand this updates the database schema
            </label>
            <button type="submit" class="assist-btn assist-btn-primary">Run migrations</button>
        </form>
    </div>

    <div class="glass-panel admin-card">
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Seeders</h2>
        @if (session('seed_output'))
            <pre>{{ session('seed_output') }}</pre>
        @endif
        <form method="POST" action="{{ route('admin.assist.system.seed') }}" class="admin-actions" onsubmit="return confirm('Run Assist seeders?');">
            @csrf
            <label class="assist-checkbox-row">
                <input type="checkbox" name="confirm" value="1" required> Confirm seed
            </label>
            <label class="assist-checkbox-row">
                <input type="checkbox" name="include_test_user" value="1"> Include test user
            </label>
            <button type="submit" class="assist-btn assist-btn-outline">Run AssistPlanSeeder</button>
        </form>
    </div>
</div>
</body>
</html>
