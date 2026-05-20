<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssistPageController extends Controller
{
    public function home(): View
    {
        return view('assist.home');
    }

    public function pricing(): View
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('assist.pricing', [
            'plans' => $plans,
        ]);
    }

    public function docs(): View
    {
        return view('assist.docs');
    }
}
