<?php

namespace App\Http\Controllers\Api\Assist;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlan;
use App\Services\UsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, UsageService $usage): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'youtube' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'marketing_opt_in' => 'boolean',
        ]);

        $userClass = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userClass::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'youtube' => $data['youtube'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'marketing_opt_in' => (bool) ($data['marketing_opt_in'] ?? false),
        ]);

        $plan = Plan::where('slug', config('assist.default_plan_slug', 'free'))->firstOrFail();
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $token = $user->createToken('assist-desktop')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userPayload($user),
            'limits' => $usage->limitsSnapshot($user),
        ], 201);
    }

    public function login(Request $request, UsageService $usage): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $userClass = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userClass::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('assist-desktop')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userPayload($user),
            'limits' => $usage->limitsSnapshot($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['success' => true]);
    }

    public function me(Request $request, UsageService $usage): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $this->userPayload($user),
            'limits' => $usage->limitsSnapshot($user),
        ]);
    }

    protected function userPayload($user): array
    {
        $plan = method_exists($user, 'currentPlan') ? $user->currentPlan() : null;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'youtube' => $user->youtube ?? null,
            'instagram' => $user->instagram ?? null,
            'marketing_opt_in' => (bool) ($user->marketing_opt_in ?? false),
            'plan' => $plan ? ['slug' => $plan->slug, 'name' => $plan->name] : null,
        ];
    }
}
