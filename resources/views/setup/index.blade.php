@php use Illuminate\Support\Str; @endphp
@extends('layouts.assist')

@section('title', 'Setup')

@section('content')
<section class="assist-section" style="padding-top: 100px;">
    <div class="assist-container" style="max-width: 720px;">
        <span class="assist-eyebrow">Installer</span>
        <h1 class="assist-h2 mb-4">Assist setup</h1>
        <p class="assist-text-muted mb-8">
            Configure database, email, CheckoutPay, and your admin account. Runs migrations and seeds plans on this server.
        </p>

        <div class="glass-panel" style="padding: 24px; border-radius: 16px; margin-bottom: 24px;">
            <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px;">Requirements</h2>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px;">
                @foreach ($requirements as $req)
                    <li style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
                        <span style="color: {{ $req['ok'] ? 'var(--success)' : 'var(--error)' }};">{{ $req['ok'] ? '✓' : '✗' }}</span>
                        {{ $req['label'] }}
                    </li>
                @endforeach
            </ul>
            @if (!$vendorPresent)
                <button type="button" id="run-composer-btn" class="assist-btn assist-btn-outline mt-4">Run Composer install</button>
                <p id="composer-result" class="assist-text-muted" style="font-size: 12px; margin-top: 8px;"></p>
            @endif
        </div>

        @if ($errors->has('install'))
            <div class="assist-alert assist-alert-error mb-4">{{ $errors->first('install') }}</div>
        @endif

        <form method="POST" action="{{ route('assist.setup.install') }}" class="glass-panel assist-auth-card" style="max-width: 100%;">
            @csrf

            <h2 style="font-size: 14px; font-weight: 700; margin-bottom: 16px;">1. Database</h2>
            <x-assist.input label="Host" name="db_host" :value="old('db_host', '127.0.0.1')" required />
            <x-assist.input label="Port" name="db_port" :value="old('db_port', '3306')" required />
            <x-assist.input label="Database name" name="db_database" :value="old('db_database')" required />
            <x-assist.input label="Username" name="db_username" :value="old('db_username')" required />
            <x-assist.input label="Password" name="db_password" type="password" />
            <button type="button" id="test-db-btn" class="assist-btn assist-btn-outline assist-btn-block mb-4">Test connection</button>
            <p id="test-db-result" class="assist-text-muted" style="font-size: 12px; min-height: 20px; margin-bottom: 16px;"></p>

            <h2 style="font-size: 14px; font-weight: 700; margin: 24px 0 16px;">2. Application</h2>
            <x-assist.input label="Site URL" name="app_url" type="url" :value="old('app_url', url('/'))" required />
            <x-assist.input label="Assist app key (shared with desktop)" name="assist_app_key" :value="old('assist_app_key', Str::random(32))" required />
            <x-assist.input label="Support email" name="support_email" type="email" :value="old('support_email', 'support@assist.app')" />
            <x-assist.input label="Download URL" name="download_url" :value="old('download_url', '#download')" />

            <h2 style="font-size: 14px; font-weight: 700; margin: 24px 0 16px;">3. Email (SMTP)</h2>
            <x-assist.input label="Mailer" name="mail_mailer" :value="old('mail_mailer', 'smtp')" />
            <x-assist.input label="SMTP host" name="mail_host" :value="old('mail_host')" />
            <x-assist.input label="SMTP port" name="mail_port" :value="old('mail_port', '587')" />
            <x-assist.input label="SMTP username" name="mail_username" :value="old('mail_username')" />
            <x-assist.input label="SMTP password" name="mail_password" type="password" />
            <x-assist.input label="Encryption" name="mail_encryption" :value="old('mail_encryption', 'tls')" />
            <x-assist.input label="From address" name="mail_from_address" type="email" :value="old('mail_from_address')" />
            <x-assist.input label="From name" name="mail_from_name" :value="old('mail_from_name', 'Assist')" />

            <h2 style="font-size: 14px; font-weight: 700; margin: 24px 0 16px;">4. CheckoutPay</h2>
            <x-assist.input label="API base URL" name="checkout_base_url" type="url" :value="old('checkout_base_url', 'https://check-outpay.com/api/v1')" />
            <x-assist.input label="API key (X-API-Key)" name="checkout_api_key" :value="old('checkout_api_key')" />
            <x-assist.input label="Webhook URL (approve in CheckoutPay dashboard)" name="checkout_webhook_url" type="url" :value="old('checkout_webhook_url', $defaultWebhookUrl)" />
            <x-assist.input label="Developer program partner ID (optional)" name="checkout_dev_program_partner_id" :value="old('checkout_dev_program_partner_id')" />

            <h2 style="font-size: 14px; font-weight: 700; margin: 24px 0 16px;">5. Admin account</h2>
            <x-assist.input label="Admin name" name="admin_name" :value="old('admin_name', 'Admin')" required />
            <x-assist.input label="Admin email" name="admin_email" type="email" :value="old('admin_email')" required />
            <x-assist.input label="Admin password" name="admin_password" type="password" required />
            <x-assist.input label="Confirm password" name="admin_password_confirmation" type="password" required />

            <label class="assist-checkbox-row">
                <input type="checkbox" name="seed_test_user" value="1" @checked(old('seed_test_user'))>
                Seed test user (test@assist.app) — dev only
            </label>

            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block mt-4" {{ $requirementsMet && $vendorPresent ? '' : 'disabled' }}>
                Install (migrate + seed plans + create admin)
            </button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('test-db-btn')?.addEventListener('click', async () => {
  const form = document.querySelector('form');
  const fd = new FormData(form);
  const result = document.getElementById('test-db-result');
  result.textContent = 'Testing…';
  try {
    const res = await fetch('{{ route('assist.setup.test-database') }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'Accept': 'application/json' },
      body: fd,
    });
    const data = await res.json();
    result.textContent = data.message;
    result.style.color = data.ok ? 'var(--success)' : 'var(--error)';
  } catch (e) {
    result.textContent = 'Request failed.';
    result.style.color = 'var(--error)';
  }
});
document.getElementById('run-composer-btn')?.addEventListener('click', async () => {
  const el = document.getElementById('composer-result');
  el.textContent = 'Running composer…';
  try {
    const res = await fetch('{{ route('assist.setup.composer') }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'Accept': 'application/json' },
    });
    const data = await res.json();
    el.textContent = data.message + (data.output ? '\n' + data.output.slice(0, 500) : '');
    el.style.color = data.ok ? 'var(--success)' : 'var(--error)';
    if (data.ok) location.reload();
  } catch (e) {
    el.textContent = 'Request failed.';
  }
});
</script>
@endpush
