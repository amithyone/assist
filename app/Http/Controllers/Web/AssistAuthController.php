<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlan;
use App\Services\UsageService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssistAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('assist.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('assist.dashboard'));
    }

    public function showRegister(): View
    {
        return view('assist.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
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

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('assist.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('assist.home');
    }

    public function showForgotPassword(): View
    {
        return view('assist.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('assist.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email')),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('assist.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function dashboard(UsageService $usage): View
    {
        $user = Auth::user();
        $plan = method_exists($user, 'currentPlan') ? $user->currentPlan() : null;

        return view('assist.dashboard', [
            'plan' => $plan,
            'limits' => $usage->limitsSnapshot($user),
        ]);
    }
}
