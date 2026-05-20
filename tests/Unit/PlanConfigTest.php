<?php

namespace Tests\Unit;

use App\Models\Plan;
use PHPUnit\Framework\TestCase;

class PlanConfigTest extends TestCase
{
    public function test_plan_features_list_includes_new_meters(): void
    {
        $this->assertContains('music_video_cuts', Plan::FEATURES);
        $this->assertContains('ai_edits', Plan::FEATURES);
        $this->assertContains('preproduction', Plan::FEATURES);
    }

    public function test_marketing_features_unlimited_label(): void
    {
        $plan = new Plan([
            'slug' => 'pro',
            'name' => 'Pro',
            'usage_period' => 'monthly',
            'limits' => [
                'preproduction' => null,
                'reel_clones' => 10,
                'beat_edits' => 10,
            ],
        ]);

        $lines = $plan->marketingFeatures();
        $this->assertNotEmpty($lines);
        $this->assertStringContainsString('Unlimited', implode(' ', $lines));
    }
}
