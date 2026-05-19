@extends('layouts.assist')
@section('title', 'Register')
@section('content')
<div class="assist-auth-wrap">
    <div class="glass-panel assist-auth-card">
        <img src="{{ asset('assist/assist-logo.png') }}" alt="Assist" class="assist-auth-logo">

        <h1>Create account</h1>
        <p class="assist-auth-sub">Start with the free plan — upgrade anytime</p>
        <x-assist.alert />
        <form method="POST" action="{{ route('assist.register') }}">
            @csrf
            <x-assist.input label="Name" name="name" required />
            <x-assist.input label="Email" name="email" type="email" required />
            <x-assist.input label="Password" name="password" type="password" required />
            <x-assist.input label="Confirm password" name="password_confirmation" type="password" required />
            <x-assist.input label="YouTube (optional)" name="youtube" />
            <x-assist.input label="Instagram (optional)" name="instagram" />
            <label class="assist-checkbox-row">
                <input type="checkbox" name="marketing_opt_in" value="1" @checked(old('marketing_opt_in'))>
                Send me product updates
            </label>
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Create account</button>
        </form>
        <p class="assist-auth-links">Already have an account? <a href="{{ route('assist.login') }}">Log in</a></p>
    </div>
</div>

@endsection
