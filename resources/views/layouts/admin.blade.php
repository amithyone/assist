<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Assist</title>
    <link rel="stylesheet" href="{{ asset('assist/assist-site.css') }}">
    @stack('head')
</head>
<body class="assist-body assist-admin-body">
<div class="assist-admin">
    @include('admin.partials.sidebar')
    <div class="assist-admin-main">
        <header class="assist-admin-topbar">
            <div>
                <p class="assist-admin-topbar-eyebrow">Assist Admin</p>
                <h1 class="assist-admin-topbar-title">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="assist-admin-topbar-actions">
                <span class="assist-admin-user-chip">{{ auth()->user()->name }}</span>
                <a href="{{ route('assist.home') }}" class="assist-btn assist-btn-ghost" target="_blank" rel="noopener">View site</a>
                <form method="POST" action="{{ route('assist.logout') }}">
                    @csrf
                    <button type="submit" class="assist-btn assist-btn-ghost">Log out</button>
                </form>
            </div>
        </header>
        <div class="assist-admin-content">
            @include('admin.partials.alerts')
            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
