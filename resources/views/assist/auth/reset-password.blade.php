@extends('layouts.assist')
@section('title', 'Reset password')
@section('content')
<div class="assist-auth-wrap">
    <div class="glass-panel assist-auth-card">
        <img src="{{ asset('assist/assist-logo.png') }}" alt="Assist" class="assist-auth-logo">

        <h1>Choose a new password</h1>
        <p class="assist-auth-sub">Enter your new password below</p>
        <x-assist.alert />
        <form method="POST" action="{{ route('assist.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <x-assist.input label="Email" name="email" type="email" :value="$email" required />
            <x-assist.input label="Password" name="password" type="password" required />
            <x-assist.input label="Confirm password" name="password_confirmation" type="password" required />
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Reset password</button>
        </form>
    </div>
</div>

@endsection
