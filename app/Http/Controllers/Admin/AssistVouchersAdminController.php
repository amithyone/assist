<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssistVouchersAdminController extends Controller
{
    public function __construct(
        protected VoucherService $vouchers
    ) {}

    public function index(): View
    {
        return view('admin.assist-vouchers.index', [
            'vouchers' => Voucher::orderByDesc('id')->get(),
            'plans' => Plan::where('is_active', true)->where('slug', '!=', 'free')->orderBy('sort_order')->get(),
            'discountTypes' => [
                Voucher::DISCOUNT_PERCENT => 'Percentage off',
                Voucher::DISCOUNT_FIXED_NGN => 'Fixed amount off (NGN)',
                Voucher::DISCOUNT_FIXED_USD => 'Fixed amount off (USD)',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['code'] = $this->vouchers->normalizeCode($data['code']);
        $data['redemption_count'] = 0;

        Voucher::create($data);

        return back()->with('status', "Voucher {$data['code']} created.");
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $data = $this->validated($request, $voucher);
        $data['code'] = $this->vouchers->normalizeCode($data['code']);
        $voucher->update($data);

        return back()->with('status', "Voucher {$voucher->code} updated.");
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $code = $voucher->code;
        $voucher->delete();

        return back()->with('status', "Voucher {$code} deleted.");
    }

    protected function validated(Request $request, ?Voucher $voucher = null): array
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('vouchers', 'code')->ignore($voucher?->id),
            ],
            'label' => 'nullable|string|max:255',
            'discount_type' => [
                'required',
                Rule::in([
                    Voucher::DISCOUNT_PERCENT,
                    Voucher::DISCOUNT_FIXED_NGN,
                    Voucher::DISCOUNT_FIXED_USD,
                ]),
            ],
            'discount_value' => 'required|numeric|min:0',
            'plan_slug' => 'nullable|string|exists:plans,slug',
            'max_redemptions' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|in:0,1',
        ]);

        if ($data['discount_type'] === Voucher::DISCOUNT_PERCENT) {
            $request->validate(['discount_value' => 'max:100']);
        }

        return [
            'code' => $data['code'],
            'label' => $data['label'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'plan_slug' => $data['plan_slug'] ?: null,
            'max_redemptions' => $data['max_redemptions'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => (bool) (int) $data['is_active'],
        ];
    }
}
