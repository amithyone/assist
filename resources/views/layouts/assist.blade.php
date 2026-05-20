<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-assist.seo :seo="$seo ?? []" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/assist.css', 'resources/js/assist.js'])
    @else
        <link rel="stylesheet" href="{{ asset('assist/assist-site.css') }}">
    @endif
    @stack('head')
</head>
<body class="assist-body">
<div class="assist-page">
    <x-assist.nav />
    <main class="assist-main">
        @if (session('status'))
            <div class="assist-container" style="padding-top: 80px;">
                <div class="assist-alert assist-alert-success">{{ session('status') }}</div>
            </div>
        @endif
        @yield('content')
    </main>
    <x-assist.footer />
</div>
<script>
document.querySelectorAll('[data-assist-tabs]').forEach((root) => {
  const buttons = root.querySelectorAll('[data-assist-tab]');
  const host = root.closest('.glass-panel') || root.parentElement;
  const panels = host ? host.querySelectorAll('[data-assist-panel]') : [];
  buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-assist-tab');
      buttons.forEach((b) => {
        const on = b === btn;
        b.classList.toggle('active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      panels.forEach((p) => {
        p.hidden = p.getAttribute('data-assist-panel') !== id;
      });
    });
  });
});
</script>
@stack('scripts')
</body>
</html>
