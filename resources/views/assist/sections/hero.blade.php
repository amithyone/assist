@php
    $s = $sections['hero'] ?? [];
    $badge = $s['badge'] ?? '✦ Music Video Cuts & story-first workflows';
    $heading = $s['heading'] ?? 'Love shooting. Enjoy the edit again.';
    $lead = $s['lead'] ?? 'Assist works inside DaVinci Resolve to build your first timeline — music video cuts, beat-synced montages, reel clones, and dialogue-led assemblies — so you stay in the director\'s chair. We speed up the process; you own the creative call.';
    $ctaPrimary = $s['cta_primary_label'] ?? 'Download for Mac (Apple Silicon)';
    $hasDownloads = count(app(\App\Services\AssistAppReleaseService::class)->availableDownloads()) > 0;
    $ctaSecondary = $s['cta_secondary_label'] ?? 'See how it works';
    $ctaSecondaryUrl = $s['cta_secondary_url'] ?? route('assist.docs');
    if (is_string($ctaSecondaryUrl) && str_starts_with($ctaSecondaryUrl, '/')) {
        $ctaSecondaryUrl = url($ctaSecondaryUrl);
    }
    $heroImage = $s['image_url'] ?? null;
    $heroAlt = $s['image_alt'] ?? 'Assist editor interface';
    $fallbackHero = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCQw2ppT0RaRpV-C2RPi8_3Ox6d1Pw1y5QMvocjHax5VzkGLbM12UpUF7MDQqFUIX6XrI0ULafBzXpQTeLFlqSefdQxzwFD8Y7XSgymW3roxQ1nHCpH81DsCw8jFz0O30pbQse_Cc-vfw0nmMkKzMN1DDu4kk59ofUHGkgYNAgJK5Jwc-n7a61IrBE9tSY3n5VrdGDDdhl683zBgCfyvAetTN9mZtLuEwokGO33HIM8T0p8pWGRsHaBXEmq85dvaqPrbtFMm6ReHA';
@endphp
<header class="assist-hero hero-gradient" id="download">
    <div class="assist-container">
        <span class="assist-hero-badge glass-panel">{{ $badge }}</span>
        <h1 class="assist-h1 mb-4">{{ $heading }}</h1>
        <p class="assist-lead" style="max-width: 44rem; margin: 0 auto;">{{ $lead }}</p>
        <div class="assist-hero-actions">
            @if ($hasDownloads)
                <x-assist.downloads :primary-label="$ctaPrimary" />
            @else
                <a href="{{ route('assist.register') }}" class="assist-btn assist-btn-primary">{{ $ctaPrimary }}</a>
            @endif
            <a href="{{ $ctaSecondaryUrl }}" class="assist-btn assist-btn-outline">{{ $ctaSecondary }}</a>
        </div>
    </div>
    <div class="assist-container" style="margin-top: 64px;">
        <div class="glass-panel" style="padding: 12px; border-radius: 24px; max-width: 72rem; margin: 0 auto;">
            <img
                src="{{ $heroImage ?: $fallbackHero }}"
                alt="{{ $heroAlt }}"
                style="width: 100%; border-radius: 16px; display: block;"
                loading="lazy"
                @if ($heroImage) data-fallback="{{ $fallbackHero }}" onerror="this.onerror=null;this.src=this.dataset.fallback;this.removeAttribute('data-fallback');" @else referrerpolicy="no-referrer" @endif
            >
        </div>
    </div>
</header>
