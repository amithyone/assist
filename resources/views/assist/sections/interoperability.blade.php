@php
    $s = $sections['interoperability'] ?? [];
    $eyebrow = $s['eyebrow'] ?? 'Interoperability';
    $heading = $s['heading'] ?? 'The .assistproject Package.';
    $lead = $s['lead'] ?? 'Carry briefs, shot lists, and story graphs from prep into post — so automated timelines respect the story you planned.';
    $bridgeTitle = $s['bridge_title'] ?? 'Bridge to Director';
    $bridgeText = $s['bridge_text'] ?? 'Compile your preproduction graph into the edit engine.';
@endphp
<section class="assist-section">
    <div class="assist-container">
        <div class="glass-panel" style="padding: 48px; border-radius: 3.5rem; display: flex; flex-wrap: wrap; gap: 48px; align-items: center;">
            <div style="flex: 1; min-width: 280px;">
                <span class="assist-eyebrow" style="color: var(--secondary);">{{ $eyebrow }}</span>
                <h2 class="assist-h2 mb-4">{{ $heading }}</h2>
                <p class="assist-lead">{{ $lead }}</p>
                <div class="glass-panel" style="margin-top: 32px; padding: 24px; border-radius: 24px; display: flex; gap: 24px; align-items: center;">
                    <div class="assist-icon-box assist-icon-box-primary" style="width: 64px; height: 64px; font-size: 28px;">🎬</div>
                    <div>
                        <div style="font-size: 20px; font-weight: 600;">{{ $bridgeTitle }}</div>
                        <p class="assist-text-muted">{{ $bridgeText }}</p>
                    </div>
                </div>
            </div>
            <div style="flex: 1; display: flex; justify-content: center; min-width: 280px;">
                @if (!empty($s['image_url']))
                    <img src="{{ $s['image_url'] }}" alt="{{ $s['image_alt'] ?? 'Assist project package' }}" style="max-width: 280px; border-radius: 24px;">
                @else
                    <div class="glass-panel" style="width: 280px; height: 280px; border-radius: 50%; border: 2px dashed rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 64px;">📦</div>
                @endif
            </div>
        </div>
    </div>
</section>
