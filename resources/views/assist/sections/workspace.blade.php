@php
    $tabs = [
        'plan' => 'Plan',
        'execution' => 'Execution',
        'story' => 'Story Graph',
        'ai' => 'AI Treatment',
    ];
@endphp
<section class="assist-section">
    <div class="assist-container text-center mb-8">
        <span class="assist-eyebrow">Preparation</span>
        <h2 class="assist-h2 mb-4">Preproduction Workspace</h2>
        <p class="assist-lead" style="max-width: 36rem; margin: 0 auto;">
            Where stories are born before the first frame is captured. A unified ecosystem for your creative blueprints.
        </p>
    </div>
    <div class="assist-container">
        <div class="glass-panel" style="border-radius: 2.5rem; overflow: hidden;">
            <div class="assist-workspace-tabs" data-assist-tabs>
                @foreach ($tabs as $id => $label)
                    <button type="button" data-assist-tab="{{ $id }}" class="assist-workspace-tab {{ $loop->first ? 'active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div style="padding: 40px; min-height: 400px;">
                <div data-assist-panel="plan" class="assist-tab-panel">
                    <div class="assist-grid-2" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                        <div>
                            <p class="assist-eyebrow" style="text-align: left;">Creative Brief</p>
                            <div class="glass-panel" style="padding: 24px; border-radius: 16px; min-height: 176px; font-style: italic; color: var(--on-surface-variant); font-size: 14px;">
                                "A cinematic exploration of urban loneliness versus digital connectivity. Use deep shadows and neon flares to emphasize contrast..."
                            </div>
                        </div>
                        <div>
                            <p class="assist-eyebrow" style="text-align: left;">Shot List</p>
                            @foreach ([['01', 'CU Eyes Tracking', '35mm'], ['02', 'Wide Drone Est.', '24mm'], ['03', 'Profile Shallow', '85mm']] as $shot)
                                <div class="glass-panel flex justify-between items-center" style="padding: 12px; border-radius: 12px; margin-bottom: 8px; font-size: 11px;">
                                    <span>{{ $shot[0] }}. {{ $shot[1] }}</span>
                                    <span style="color: var(--secondary); font-weight: 700; font-size: 9px; text-transform: uppercase;">{{ $shot[2] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div data-assist-panel="story" class="assist-tab-panel" hidden>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 24px; padding: 24px 0;">
                        @foreach ([['ACT I', 'The Invitation'], ['INCITING', 'The Discovery'], ['MIDPOINT', 'Conflict Escalates'], ['CLIMAX', 'Final Confrontation']] as $act)
                            <div class="glass-panel" style="padding: 20px; border-radius: 16px;">
                                <div class="assist-eyebrow" style="margin-bottom: 8px;">{{ $act[0] }}</div>
                                <div style="font-weight: 600; font-size: 14px;">{{ $act[1] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div data-assist-panel="execution" class="assist-tab-panel" hidden>
                    <p class="assist-text-muted text-center" style="padding: 48px;">Execution checklist — wrap reports, gear inventory, and shoot-day progress sync.</p>
                </div>
                <div data-assist-panel="ai" class="assist-tab-panel" hidden>
                    <p class="assist-text-muted text-center" style="padding: 48px;">Production intelligence — treatment options and shot ideas from your creative brief.</p>
                </div>
            </div>
        </div>
    </div>
</section>
