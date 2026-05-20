<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AssistAdminSeeder extends Seeder
{
    /**
     * @param  array{name: string, email: string, password: string}  $admin
     */
    public function run(array $admin = []): void
    {
        $userClass = config('auth.providers.users.model', User::class);

        $user = $userClass::updateOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $admin['name'],
                'password' => Hash::make($admin['password']),
                'is_admin' => true,
                'marketing_opt_in' => false,
            ]
        );

        $free = Plan::where('slug', 'free')->first();
        if ($free) {
            UserPlan::updateOrCreate(
                ['user_id' => $user->id, 'plan_id' => $free->id],
                ['status' => 'active', 'starts_at' => now(), 'ends_at' => null]
            );
        }
    }
}
