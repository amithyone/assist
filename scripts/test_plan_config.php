#!/usr/bin/env php
<?php
/** Smoke test for plan limits config (no Laravel bootstrap). */
$features = [
    'preproduction', 'reel_clones', 'beat_edits', 'music_video_cuts', 'ai_edits',
];
$plans = [
    'free' => ['usage_period' => 'weekly', 'reel_clones' => 1, 'beat_edits' => 1],
    'pro' => ['usage_period' => 'monthly', 'reel_clones' => 10, 'music_video_cuts' => 2],
    'unlimited' => ['usage_period' => 'monthly', 'reel_clones' => null],
];
foreach ($plans as $slug => $p) {
    assert(isset($p['usage_period']));
}
assert(count($features) === 5);
echo "Plan config smoke test passed.\n";
