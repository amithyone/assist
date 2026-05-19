@extends('layouts.assist')
@section('title', 'Log in')
@section('content')
<div class="assist-auth-wrap">
    <div class="glass-panel assist-auth-card">
        <img src="{{ asset('assist/assist-logo.png') }}" alt="Assist" class="assist-auth-logo">

        <h1>Welcome back</h1>
        <p class="assist-auth-sub">Sign in to your Assist account</p>
        <x-assist.alert />
        <form method="POST" action="{{ route('assist.login') }}">
            @csrf
            <x-assist.input label="Email" name="email" type="email" required />
            <x-assist.input label="Password" name="password" type="password" required />
            <label class="assist-checkbox-row">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Log in</button>
        </form>
        <p class="assist-auth-links">
            <a href="{{ route('assist.password.request') }}">Forgot password?</a><br>
            No account? <a href="{{ route('assist.register') }}">Register</a>
        </p>
    </div>
</div>

@endsection
