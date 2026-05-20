@php
    $s = $sections['editing_engine'] ?? [];
    $eyebrow = $s['eyebrow'] ?? 'Intelligent Workflow';
    $heading = $s['heading'] ?? 'Powerful Editing Engine';
    $lead = $s['lead'] ?? 'Assist reads your rushes, understands project type, and delivers a real timeline in Resolve — not a locked export. Tweak every cut, swap clips, and rebuild layers whenever you want.';
    $items = $s['items'] ?? [
        ['icon' => '🎙', 'title' => 'Auto-Transcription', 'text' => 'Dialogue-aware Interview-Led edits in Resolve.'],
        ['icon' => '🎬', 'title' => 'Music Video Cuts', 'text' => 'Multi-layer performance + b-roll timelines synced to your track.'],
        ['icon' => '📁', 'title' => 'Smart Scene Detection', 'text' => 'Bins by project type — music video, wedding, doc, podcast, and more.'],
    ];
    $ctaLabel = $s['cta_label'] ?? 'Execute First Assembly';
    $projectTypes = $s['project_types'] ?? ['Music Video', 'Wedding', 'Documentary', 'Interview'];
@endphp
<section class="assist-section">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">{{ $eyebrow }}</span>
        <h2 class="assist-h2 mb-4">{{ $heading }}</h2>
        <p class="assist-lead" style="max-width: 40rem; margin: 0 auto;">{{ $lead }}</p>
    </div>
    <div class="assist-container assist-grid-2" style="align-items: center;">
        <div>
            <p class="assist-eyebrow" style="text-align: left;">Active Intelligence</p>
            @foreach ($items as $item)
                <div class="glass-panel" style="padding: 16px; border-radius: 12px; margin-bottom: 12px; display: flex; gap: 16px; align-items: center;">
                    <div class="assist-icon-box assist-icon-box-{{ $loop->iteration % 2 ? 'primary' : 'secondary' }}">{{ $item['icon'] ?? '✦' }}</div>
                    <div>
                        <div style="font-weight: 600; font-size: 14px;">{{ $item['title'] ?? '' }}</div>
                        <div class="assist-text-muted">{{ $item['text'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
            <a href="{{ route('assist.register') }}" class="assist-btn assist-btn-primary assist-btn-block mt-4">{{ $ctaLabel }}</a>
        </div>
        <div class="glass-panel" style="padding: 32px; border-radius: 16px;">
            <p class="assist-eyebrow" style="text-align: left;">Project type</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px;">
                @foreach ($projectTypes as $type)
                    <div style="padding: 16px; text-align: center; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); color: var(--on-surface-variant);">{{ $type }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>
