<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssistPlansAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.assist-plans.index', [
            'plans' => Plan::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function edit(Plan $plan): View
    {
        return view('admin.assist-plans.edit', [
            'plan' => $plan,
            'features' => Plan::FEATURES,
            'featureLabels' => Plan::FEATURE_LABELS,
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'price_ngn' => 'required|integer|min:0',
            'price_usd' => 'required|numeric|min:0',
            'usage_period' => ['required', Rule::in(['weekly', 'monthly'])],
            'sort_order' => 'required|integer|min:0|max:999',
            'is_active' => 'required|in:0,1',
            'is_featured' => 'required|in:0,1',
            'limits' => 'array',
            'limits.*' => 'nullable|string',
        ]);

        if ($plan->slug === 'free') {
            $data['price_ngn'] = 0;
            $data['price_usd'] = 0;
        }

        $limits = [];
        foreach (Plan::FEATURES as $feature) {
            $raw = $data['limits'][$feature] ?? '';
            if ($raw === '' || $raw === null) {
                continue;
            }
            if (strtolower((string) $raw) === 'unlimited' || $raw === 'null') {
                $limits[$feature] = null;
            } else {
                $limits[$feature] = max(0, (int) $raw);
            }
        }

        if ((bool) (int) $data['is_featured']) {
            Plan::where('id', '!=', $plan->id)->update(['is_featured' => false]);
        }

        $plan->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price_ngn' => (int) $data['price_ngn'],
            'price_usd' => $data['price_usd'],
            'usage_period' => $data['usage_period'],
            'sort_order' => (int) $data['sort_order'],
            'is_active' => (bool) (int) $data['is_active'],
            'is_featured' => (bool) (int) $data['is_featured'],
            'limits' => $limits,
        ]);

        return redirect()
            ->route('admin.assist.plans')
            ->with('status', "Plan “{$plan->name}” saved.");
    }
}
