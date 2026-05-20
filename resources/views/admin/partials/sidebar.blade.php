@php
    $route = request()->route()?->getName() ?? '';
    $nav = [
        ['route' => 'admin.assist.dashboard', 'label' => 'Overview', 'icon' => '◉'],
        ['route' => 'admin.assist.site-pages', 'label' => 'Site content', 'icon' => '✎'],
        ['route' => 'admin.assist.users', 'label' => 'Users', 'icon' => '◎'],
        ['route' => 'admin.assist.settings', 'label' => 'Payments', 'icon' => '₦'],
        ['route' => 'admin.assist.downloads', 'label' => 'App release', 'icon' => '↓'],
        ['route' => 'admin.assist.activity', 'label' => 'Activity', 'icon' => '≡'],
        ['route' => 'admin.assist.system', 'label' => 'System', 'icon' => '⚙'],
    ];
@endphp
<aside class="assist-admin-sidebar glass-panel">
    <a href="{{ route('admin.assist.dashboard') }}" class="assist-admin-brand">
        <img src="{{ asset('assist/assist-logo.png') }}" alt="Assist">
        <span>Assist</span>
    </a>
    <nav class="assist-admin-nav">
        @foreach ($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="assist-admin-nav-link {{ ($route === $item['route'] || str_starts_with($route, $item['route'].'.')) ? 'active' : '' }}">
                <span class="assist-admin-nav-icon" aria-hidden="true">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
    <div class="assist-admin-sidebar-foot">
        <a href="{{ route('assist.dashboard') }}" class="assist-admin-nav-link">
            <span class="assist-admin-nav-icon">↗</span>
            User dashboard
        </a>
    </div>
</aside>
