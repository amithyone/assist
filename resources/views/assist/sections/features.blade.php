@php
    $intro = $sections['features_intro'] ?? [];
    $spot = $sections['feature_spotlight'] ?? [];
    $cards = [
        'feature_reels' => $sections['feature_reels'] ?? [],
        'feature_beat' => $sections['feature_beat'] ?? [],
        'feature_ai' => $sections['feature_ai'] ?? [],
    ];
    $bottom = [
        'feature_prepro' => $sections['feature_prepro'] ?? [],
        'feature_transcription' => $sections['feature_transcription'] ?? [],
    ];
    $spotImage = $spot['image_url'] ?? null;
@endphp
<section class="assist-section" id="features">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">{{ $intro['eyebrow'] ?? 'Features' }}</span>
        <h2 class="assist-h2 mb-4">{{ $intro['heading'] ?? 'A faster path to your first cut.' }}</h2>
        <p class="assist-lead" style="max-width: 40rem; margin: 0 auto;">
            {{ $intro['lead'] ?? 'From music videos to documentaries, each tool gives you a strong starting timeline inside DaVinci Resolve — then you shape the final edit your way.' }}
        </p>
    </div>

    <div class="assist-container mb-6">
        <div class="glass-panel assist-feature-card assist-feature-card--hero">
            @if (!empty($spot['badge']))
                <span class="new-badge" style="position: absolute; top: 32px; right: 32px;">{{ $spot['badge'] }}</span>
            @endif
            <div class="assist-feature-hero-grid">
                <div>
                    <div class="assist-icon-box assist-icon-box-primary" style="width: 56px; height: 56px; font-size: 28px;">{{ $spot['icon'] ?? '🎬' }}</div>
                    <h3>{{ $spot['heading'] ?? 'Music Video Cuts' }}</h3>
                    <p class="assist-text-muted" style="line-height: 1.7; max-width: 36rem;">
                        {{ $spot['body'] ?? '' }}
                    </p>
                    @if (!empty($spot['bullets']))
                        <ul class="assist-feature-bullets assist-text-muted">
                            @foreach ($spot['bullets'] as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @if ($spotImage)
                    <div class="glass-panel" style="padding: 12px; border-radius: 1.5rem; overflow: hidden;">
                        <img src="{{ $spotImage }}" alt="{{ $spot['image_alt'] ?? 'Music Video Cuts preview' }}" style="width: 100%; border-radius: 12px; display: block;">
                    </div>
                @else
                    <div class="assist-mvc-preview glass-panel" aria-hidden="true">
                        <div class="assist-mvc-track"><span>V1</span><div class="assist-mvc-clips" style="width: 72%;"></div></div>
                        <div class="assist-mvc-track"><span>V2</span><div class="assist-mvc-clips" style="width: 58%; margin-left: 8%;"></div></div>
                        <div class="assist-mvc-track"><span>V3</span><div class="assist-mvc-clips assist-mvc-broll" style="width: 35%; margin-left: 20%;"></div></div>
                        <div class="assist-mvc-audio"><span>A1</span><div class="assist-mvc-wave"></div></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="assist-container assist-grid-3">
        @foreach ($cards as $key => $card)
            <div class="glass-panel assist-feature-card">
                <div class="assist-icon-box assist-icon-box-{{ $loop->iteration % 2 ? 'secondary' : 'primary' }}">{{ $card['icon'] ?? '✦' }}</div>
                <h3>{{ $card['heading'] ?? '' }}</h3>
                <p class="assist-text-muted" style="line-height: 1.6;">{{ $card['body'] ?? '' }}</p>
                @if ($key === 'feature_beat')
                    <div style="margin-top: 24px; display: flex; align-items: flex-end; gap: 6px; height: 80px;">
                        @foreach ([20, 60, 90, 40, 70, 30, 85, 40] as $h)
                            <div style="flex: 1; height: {{ $h }}%; background: {{ $h > 80 ? 'var(--primary)' : 'rgba(94,106,210,0.3)' }}; border-radius: 6px 6px 0 0;"></div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="assist-container assist-grid-2" style="margin-top: 24px;">
        @foreach ($bottom as $card)
            <div class="glass-panel assist-feature-card">
                <div class="assist-icon-box assist-icon-box-{{ $loop->iteration % 2 ? 'secondary' : 'primary' }}">{{ $card['icon'] ?? '✦' }}</div>
                <h3>{{ $card['heading'] ?? '' }}</h3>
                <p class="assist-text-muted" style="line-height: 1.6;">{{ $card['body'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</section>
