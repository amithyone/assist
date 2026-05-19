@php
    $route = request()->route()?->getName() ?? '';
@endphp
<nav class="assist-nav">
    <a href="{{ route('assist.home') }}" class="assist-nav-brand">
        <img src="{{ asset('assist/assist-logo.png') }}" alt="Amithyone Media — Assist">
        <span>Assist</span>
    </a>
    <div class="assist-nav-links">
        <a href="{{ route('assist.home') }}" class="{{ $route === 'assist.home' ? 'active' : '' }}">Features</a>
        <a href="{{ route('assist.pricing') }}" class="{{ $route === 'assist.pricing' ? 'active' : '' }}">Pricing</a>
        <a href="{{ config('assist.download_url', '#download') }}" id="download">Download</a>
        <a href="{{ route('assist.docs') }}" class="{{ $route === 'assist.docs' ? 'active' : '' }}">Docs</a>
    </div>
    <div class="assist-nav-actions">
        @auth
            <span class="assist-nav-user">{{ auth()->user()->name }}</span>
            <a href="{{ route('assist.dashboard') }}" class="assist-btn assist-btn-ghost">Dashboard</a>
            <form method="POST" action="{{ route('assist.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="assist-btn assist-btn-ghost">Log out</button>
            </form>
        @else
            <a href="{{ route('assist.login') }}" class="assist-btn assist-btn-ghost">Log in</a>
            <a href="{{ route('assist.register') }}" class="assist-btn assist-btn-primary">Get Started</a>
        @endauth
    </div>
</nav>
