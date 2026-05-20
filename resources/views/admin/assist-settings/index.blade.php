@extends('layouts.admin')

@section('title', 'Payments')
@section('page_title', 'Payments & billing')

@section('content')
@include('admin.partials.payment-gateways', [
    'payment' => $payment,
    'gatewaySaveRoute' => route('admin.assist.settings.gateway'),
    'checkoutSaveRoute' => route('admin.assist.settings.checkout'),
    'paystackSaveRoute' => route('admin.assist.settings.paystack'),
])

@include('admin.partials.desktop-app-build', ['app' => $app])

<div class="assist-admin-card glass-panel">
    <h2>Application</h2>
    <form method="POST" action="{{ route('admin.assist.settings.app') }}" class="assist-admin-form-grid" style="max-width: 640px;">
        @csrf
        <x-assist.input label="Site URL" name="app_url" type="url" :value="old('app_url', $app['url'])" required />
        <x-assist.input label="Assist app key (desktop)" name="assist_app_key" type="password"
            placeholder="{{ $app['assist_app_key_set'] ? 'Leave blank to keep current key' : 'Min 16 characters' }}" />
        <x-assist.input label="Support email" name="support_email" type="email" :value="old('support_email', $app['support_email'])" />
        <x-assist.input label="Download URL fallback" name="download_url" :value="old('download_url', $app['download_url'])" />
        <div>
            <button type="submit" class="assist-btn assist-btn-primary">Save application settings</button>
        </div>
    </form>
</div>
@endsection
