<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\UsageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistDashboardAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.assist-dashboard.index', [
            'userCount' => User::count(),
            'paymentCount' => Payment::where('status', 'approved')->count(),
            'recentPayments' => Payment::with('user')->latest()->limit(10)->get(),
            'plans' => Plan::orderBy('sort_order')->get(),
        ]);
    }

    public function users(UsageService $usage): View
    {
        $users = User::with(['userPlans.plan'])->orderByDesc('id')->paginate(25);

        return view('admin.assist-dashboard.users', [
            'users' => $users,
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function updateUserPlan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'plan_slug' => 'required|string|exists:plans,slug',
        ]);

        $plan = Plan::where('slug', $data['plan_slug'])->firstOrFail();
        $user = User::findOrFail($data['user_id']);

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

        return back()->with('status', "Updated {$user->email} to {$plan->name}.");
    }
}
