<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\SiteContentService;
use Illuminate\View\View;

class AssistPageController extends Controller
{
    public function __construct(
        protected SiteContentService $content
    ) {}

    public function home(): View
    {
        return view('assist.home', $this->content->homeViewData());
    }

    public function pricing(): View
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $cms = $this->content->pageViewData('pricing');

        return view('assist.pricing', array_merge($cms, ['plans' => $plans]));
    }

    public function docs(): View
    {
        return view('assist.docs', $this->content->pageViewData('docs'));
    }

    public function privacy(): View
    {
        return view('assist.privacy', $this->content->pageViewData('privacy'));
    }

    public function terms(): View
    {
        return view('assist.terms', $this->content->pageViewData('terms'));
    }
}
