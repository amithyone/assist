<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssistUsageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\AssistPlanSeeder::class);
    }

    public function test_usage_check_respects_free_limits(): void
    {
        $plan = Plan::where('slug', 'free')->first();
        $user = User::factory()->create();
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/assist/usage/check', [
            'feature' => 'timelines',
            'units' => 1,
        ]);

        $response->assertOk()->assertJson(['allowed' => true]);
    }
}
