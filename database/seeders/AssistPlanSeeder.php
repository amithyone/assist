<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class AssistPlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'is_active' => true,
                'usage_period' => 'weekly',
                'price_ngn' => 0,
                'price_usd' => 0,
                'sort_order' => 1,
                'description' => 'Try every workflow — including Music Video Cuts — with friendly weekly limits.',
                'limits' => [
                    'preproduction' => null,
                    'reel_clones' => 1,
                    'beat_edits' => 1,
                    'music_video_cuts' => 1,
                    'ai_edits' => 1,
                    'timelines' => null,
                    'transcribe_clips' => null,
                ],
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'is_active' => true,
                'is_featured' => true,
                'usage_period' => 'monthly',
                'price_ngn' => 5000,
                'price_usd' => 5.00,
                'sort_order' => 2,
                'description' => 'More Music Video Cuts, Reels, Beat Edit, and AI Editor runs each month.',
                'limits' => [
                    'preproduction' => null,
                    'reel_clones' => 10,
                    'beat_edits' => 10,
                    'music_video_cuts' => 2,
                    'ai_edits' => 5,
                    'timelines' => null,
                    'transcribe_clips' => null,
                ],
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'unlimited'],
            [
                'name' => 'Unlimited',
                'is_active' => true,
                'usage_period' => 'monthly',
                'price_ngn' => 30000,
                'price_usd' => 30.00,
                'sort_order' => 3,
                'description' => 'Full access to every Assist workflow with no usage caps.',
                'limits' => [
                    'preproduction' => null,
                    'reel_clones' => null,
                    'beat_edits' => null,
                    'music_video_cuts' => null,
                    'ai_edits' => null,
                    'timelines' => null,
                    'transcribe_clips' => null,
                ],
            ]
        );
    }
}
