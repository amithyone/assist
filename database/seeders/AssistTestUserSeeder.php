<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AssistTestUserSeeder extends Seeder
{
    /** Same credentials as Assist desktop dev login (license_client.py). */
    public const TEST_EMAIL = 'test@assist.app';

    public const TEST_PASSWORD = 'assist123';

    public function run(): void
    {
        $this->call(AssistPlanSeeder::class);

        $userClass = config('auth.providers.users.model', User::class);
        $user = $userClass::updateOrCreate(
            ['email' => self::TEST_EMAIL],
            [
                'name' => 'Assist Test User',
                'password' => Hash::make(self::TEST_PASSWORD),
                'marketing_opt_in' => false,
            ]
        );

        $plan = Plan::where('slug', 'free')->firstOrFail();
        UserPlan::updateOrCreate(
            ['user_id' => $user->id, 'plan_id' => $plan->id],
            ['status' => 'active', 'starts_at' => now()]
        );
    }
}
