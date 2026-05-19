<section class="assist-section">
    <div class="assist-container assist-grid-2">
        <div class="glass-panel assist-feature-card">
            <span class="new-badge" style="position: absolute; top: 32px; right: 32px;">New</span>
            <div class="assist-icon-box assist-icon-box-secondary">📋</div>
            <h3>Reels Cloner</h3>
            <p class="assist-text-muted" style="line-height: 1.6;">
                Drop a reference reel and Assist will analyze rhythm, pacing, and color science to match your footage to the vibe.
            </p>
            <div class="glass-panel" style="margin-top: 40px; height: 224px; border-radius: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(18,18,22,0.4);">
                <span style="font-size: 32px; margin-bottom: 16px; opacity: 0.5;">☁</span>
                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: var(--on-surface-variant);">Drop MP4 or link here</span>
            </div>
        </div>
        <div class="glass-panel assist-feature-card">
            <span class="new-badge" style="position: absolute; top: 32px; right: 32px;">New</span>
            <div class="assist-icon-box assist-icon-box-primary">🎵</div>
            <h3>Beat Edit</h3>
            <p class="assist-text-muted" style="line-height: 1.6;">
                Automated music-sync montage creation. Detects transients and harmonic shifts to place cuts with surgical precision.
            </p>
            <div style="margin-top: 40px; display: flex; align-items: flex-end; gap: 8px; height: 224px; padding: 0 24px;">
                @foreach ([20, 60, 90, 40, 70, 30, 85, 40] as $h)
                    <div style="flex: 1; height: {{ $h }}%; background: {{ $h > 80 ? 'var(--primary)' : 'rgba(94,106,210,0.3)' }}; border-radius: 8px 8px 0 0;"></div>
                @endforeach
            </div>
        </div>
    </div>
</section>
