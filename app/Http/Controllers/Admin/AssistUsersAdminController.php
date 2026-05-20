<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AssistUsersAdminController extends Controller
{
    public function index(): View
    {
        $users = User::with(['userPlans.plan'])->orderByDesc('id')->paginate(20);

        return view('admin.assist-users.index', [
            'users' => $users,
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin' => 'required|in:0,1',
            'plan_slug' => 'required|string|exists:plans,slug',
        ]);

        $isAdmin = (bool) (int) $data['is_admin'];

        if ($user->id === auth()->id() && ! $isAdmin) {
            return back()->withErrors(['is_admin' => 'You cannot remove your own admin role.']);
        }

        if (($user->is_admin ?? false) && ! $isAdmin) {
            $otherAdmins = User::where('is_admin', true)->where('id', '!=', $user->id)->count();
            if ($otherAdmins < 1) {
                return back()->withErrors(['is_admin' => 'At least one admin account is required.']);
            }
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $isAdmin,
        ]);

        $plan = Plan::where('slug', $data['plan_slug'])->firstOrFail();
        $user->userPlans()->where('status', 'active')->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $plan->slug === 'free' ? null : now()->addMonth(),
        ]);

        return back()->with('status', "Updated {$user->email}.");
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => $data['password']]);

        return back()->with('status', "Password updated for {$user->email}.");
    }

    public function updateOwnPassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => $data['password']]);

        return back()->with('status', 'Your password has been changed.');
    }
}
