<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
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

}
