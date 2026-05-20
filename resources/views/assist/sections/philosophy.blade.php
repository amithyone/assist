@php
    $s = $sections['philosophy'] ?? [];
    $eyebrow = $s['eyebrow'] ?? 'How we think about editing';
    $heading = $s['heading'] ?? 'We are not here to edit for you.';
    $lead = $s['lead'] ?? 'Assist handles the repetitive lift — syncing bins, laying down a first pass, matching a reference reel, cutting on the beat — so you can spend your energy on pacing, story, and the choices only you can make.';
    $pills = $s['pills'] ?? ['You approve every timeline', 'Full control in Resolve', 'Refine, replace, or rebuild anytime', 'AI suggests — you decide'];
@endphp
<section class="assist-section" style="padding-top: 0;">
    <div class="assist-container text-center" style="max-width: 48rem;">
        <span class="assist-eyebrow">{{ $eyebrow }}</span>
        <h2 class="assist-h2 mb-4">{{ $heading }}</h2>
        <p class="assist-lead" style="margin: 0 auto;">{{ $lead }}</p>
        <div class="assist-philosophy-pills" style="margin-top: 32px; display: flex; flex-wrap: wrap; gap: 12px; justify-content: center;">
            @foreach ($pills as $pill)
                <span class="glass-panel" style="padding: 10px 18px; border-radius: 999px; font-size: 13px; color: var(--on-surface-variant);">{{ $pill }}</span>
            @endforeach
        </div>
    </div>
</section>
