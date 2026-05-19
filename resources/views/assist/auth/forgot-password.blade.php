@extends('layouts.assist')
@section('title', 'Forgot password')
@section('content')
<div class="assist-auth-wrap">
    <div class="glass-panel assist-auth-card">
        <img src="{{ asset('assist/assist-logo.png') }}" alt="Assist" class="assist-auth-logo">

        <h1>Reset password</h1>
        <p class="assist-auth-sub">We will email you a reset link</p>
        <x-assist.alert />
        <form method="POST" action="{{ route('assist.password.email') }}">
            @csrf
            <x-assist.input label="Email" name="email" type="email" required />
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Send reset link</button>
        </form>
        <p class="assist-auth-links"><a href="{{ route('assist.login') }}">Back to log in</a></p>
    </div>
</div>

@endsection
