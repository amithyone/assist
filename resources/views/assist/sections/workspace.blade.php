@php
    $s = $sections['workspace'] ?? [];
    $eyebrow = $s['eyebrow'] ?? 'Preparation';
    $heading = $s['heading'] ?? 'Preproduction Workspace';
    $lead = $s['lead'] ?? 'Where stories are born before the first frame is captured. A unified ecosystem for your creative blueprints.';
    $tabs = [
        'plan' => 'Plan',
        'execution' => 'Execution',
        'story' => 'Story Graph',
        'ai' => 'AI Treatment',
    ];
    $shots = [
        ['01', 'CU Eyes Tracking', '35mm'],
        ['02', 'Wide Drone Est.', '24mm'],
        ['03', 'Profile Shallow', '85mm'],
    ];
    $storyBeats = [
        ['ACT I', 'The Invitation', '0'],
        ['INCITING', 'The Discovery', '1'],
        ['MIDPOINT', 'Conflict Escalates', '2'],
        ['CLIMAX', 'Final Confrontation', '3'],
    ];
    $treatments = [
        ['A', 'Neon isolation', 'High contrast, cool shadows, sparse dialogue'],
        ['B', 'Warm documentary', 'Handheld intimacy, natural light arcs'],
        ['C', 'Hybrid pulse', 'Performance cuts on downbeats, lyrical b-roll'],
    ];
@endphp
<section class="assist-section assist-workspace-section">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">{{ $eyebrow }}</span>
        <h2 class="assist-h2 mb-4">{{ $heading }}</h2>
        <p class="assist-lead assist-workspace-lead">{{ $lead }}</p>
    </div>
    <div class="assist-container">
        <div class="assist-workspace-shell glass-panel">
            <div class="assist-workspace-tablist" data-workspace-tabs role="tablist" aria-label="Preproduction workspace">
                @foreach ($tabs as $id => $label)
                    <button
                        type="button"
                        role="tab"
                        id="workspace-tab-{{ $id }}"
                        aria-controls="workspace-panel-{{ $id }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        data-workspace-tab="{{ $id }}"
                        class="assist-workspace-tab {{ $loop->first ? 'is-active' : '' }}"
                    >{{ $label }}</button>
                @endforeach
            </div>

            <div class="assist-workspace-body">
                {{-- Plan --}}
                <div
                    id="workspace-panel-plan"
                    role="tabpanel"
                    aria-labelledby="workspace-tab-plan"
                    data-workspace-panel="plan"
                    class="assist-workspace-panel is-active"
                >
                    <div class="assist-plan-grid">
                        <div class="assist-plan-col">
                            <p class="assist-plan-label">Creative Brief</p>
                            <div class="assist-plan-brief glass-panel">
                                "A cinematic exploration of urban loneliness versus digital connectivity. Use deep shadows and neon flares to emphasize contrast…"
                            </div>
                        </div>
                        <div class="assist-plan-col">
                            <p class="assist-plan-label">Storyboard</p>
                            <div class="assist-plan-storyboard glass-panel">
                                <div class="assist-plan-storyboard-inner" aria-hidden="true"></div>
                            </div>
                        </div>
                        <div class="assist-plan-col">
                            <p class="assist-plan-label">Moodboard</p>
                            <div class="assist-moodboard-swatches">
                                <span style="background:#000965"></span>
                                <span style="background:#5e6ad2"></span>
                                <span style="background:#9d50bb"></span>
                                <span style="background:#131315"></span>
                            </div>
                            <p class="assist-moodboard-tag">Atmospheric / Cyber / Noir</p>
                        </div>
                        <div class="assist-plan-col">
                            <p class="assist-plan-label">Shot List</p>
                            <ul class="assist-shot-list">
                                @foreach ($shots as $shot)
                                    <li class="assist-shot-item glass-panel">
                                        <span class="assist-shot-name">{{ $shot[0] }}. {{ $shot[1] }}</span>
                                        <span class="assist-shot-lens">{{ $shot[2] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Execution --}}
                <div
                    id="workspace-panel-execution"
                    role="tabpanel"
                    aria-labelledby="workspace-tab-execution"
                    data-workspace-panel="execution"
                    class="assist-workspace-panel"
                    hidden
                >
                    <div class="assist-execution-grid">
                        @foreach ([['Gear check', 'Cameras, lenses, batteries', 92], ['Location recce', 'Light plot + sound', 78], ['Shoot-day sync', 'Call sheet + dailies', 45], ['Wrap report', 'Notes for post', 20]] as $i => $task)
                            <div class="assist-execution-card glass-panel" style="--exec-delay: {{ $i * 0.08 }}s">
                                <div class="assist-execution-card-head">
                                    <span class="assist-execution-title">{{ $task[0] }}</span>
                                    <span class="assist-execution-pct">{{ $task[2] }}%</span>
                                </div>
                                <p class="assist-text-muted">{{ $task[1] }}</p>
                                <div class="assist-execution-bar"><span style="width: {{ $task[2] }}%"></span></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Story Graph --}}
                <div
                    id="workspace-panel-story"
                    role="tabpanel"
                    aria-labelledby="workspace-tab-story"
                    data-workspace-panel="story"
                    class="assist-workspace-panel"
                    hidden
                >
                    <div class="assist-story-stage">
                        <div class="assist-story-stage-glow" aria-hidden="true"></div>
                        <svg class="assist-story-stage-svg" viewBox="0 0 800 400" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="assistStoryGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#5e6ad2" />
                                    <stop offset="100%" stop-color="#9d50bb" />
                                </linearGradient>
                            </defs>
                            <path d="M80 200 Q 240 120 400 220 T 720 280" fill="none" stroke="url(#assistStoryGrad)" stroke-width="2" stroke-dasharray="8 4" opacity="0.35" />
                        </svg>
                        <div class="assist-story-beats">
                            @foreach ($storyBeats as $beat)
                                <div class="assist-story-beat glass-panel assist-story-beat--offset-{{ $beat[2] }}">
                                    <span class="assist-story-beat-act">{{ $beat[0] }}</span>
                                    <span class="assist-story-beat-title">{{ $beat[1] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- AI Treatment --}}
                <div
                    id="workspace-panel-ai"
                    role="tabpanel"
                    aria-labelledby="workspace-tab-ai"
                    data-workspace-panel="ai"
                    class="assist-workspace-panel"
                    hidden
                >
                    <div class="assist-treatment-grid">
                        @foreach ($treatments as $i => $treatment)
                            <article class="assist-treatment-card glass-panel" style="--treat-delay: {{ $i * 0.1 }}s">
                                <span class="assist-treatment-badge">Option {{ $treatment[0] }}</span>
                                <h4 class="assist-treatment-title">{{ $treatment[1] }}</h4>
                                <p class="assist-text-muted">{{ $treatment[2] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
  document.querySelectorAll('[data-workspace-tabs]').forEach(function (tablist) {
    var tabs = Array.from(tablist.querySelectorAll('[data-workspace-tab]'));
    var shell = tablist.closest('.assist-workspace-shell');
    var body = shell && shell.querySelector('.assist-workspace-body');
    if (!body) return;
    var panels = Array.from(body.querySelectorAll('[data-workspace-panel]'));

    function showPanel(id) {
      tabs.forEach(function (t) {
        var on = t.getAttribute('data-workspace-tab') === id;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      panels.forEach(function (p) {
        var on = p.getAttribute('data-workspace-panel') === id;
        p.classList.toggle('is-active', on);
        p.hidden = !on;
      });
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        showPanel(tab.getAttribute('data-workspace-tab'));
      });
    });
  });
})();
</script>
@endpush
