<section class="assist-section">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">Intelligent Workflow</span>
        <h2 class="assist-h2 mb-4">Powerful Editing Engine</h2>
        <p class="assist-lead" style="max-width: 36rem; margin: 0 auto;">
            Assist reads your raw rushes, transcribes clips, and crafts a narrative-first timeline directly in DaVinci Resolve.
        </p>
    </div>
    <div class="assist-container assist-grid-2" style="align-items: center;">
        <div>
            <p class="assist-eyebrow" style="text-align: left;">Active Intelligence</p>
            <div class="glass-panel" style="padding: 16px; border-radius: 12px; margin-bottom: 12px; display: flex; gap: 16px; align-items: center;">
                <div class="assist-icon-box assist-icon-box-primary">🎙</div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">Auto-Transcription</div>
                    <div class="assist-text-muted">Dialogue-aware Interview-Led edits in Resolve.</div>
                </div>
            </div>
            <div class="glass-panel" style="padding: 16px; border-radius: 12px; display: flex; gap: 16px; align-items: center;">
                <div class="assist-icon-box assist-icon-box-secondary">🎬</div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">Smart Scene Detection</div>
                    <div class="assist-text-muted">Bins by project type — wedding, doc, podcast, and more.</div>
                </div>
            </div>
            <a href="{{ route('assist.register') }}" class="assist-btn assist-btn-primary assist-btn-block mt-4">Execute First Assembly</a>
        </div>
        <div class="glass-panel" style="padding: 32px; border-radius: 16px;">
            <p class="assist-eyebrow" style="text-align: left;">Project type</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px;">
                @foreach (['Wedding', 'Documentary', 'Commercial', 'Social'] as $type)
                    <div style="padding: 16px; text-align: center; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); color: var(--on-surface-variant);">{{ $type }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>
