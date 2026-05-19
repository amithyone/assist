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
                'limits' => [
                    'timelines' => 10,
                    'transcribe_clips' => 60,
                    'reel_clones' => 5,
                    'beat_edits' => 10,
                ],
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'is_active' => true,
                'limits' => [
                    'timelines' => null,
                    'transcribe_clips' => null,
                    'reel_clones' => null,
                    'beat_edits' => null,
                ],
            ]
        );
    }
}
