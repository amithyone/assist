<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\UserPlan;

class PaymentActivationService
{
    public function __construct(
        protected VoucherService $vouchers
    ) {}

    public function approve(Payment $payment, array $extraPayload = []): Payment
    {
        if ($payment->status === 'approved') {
            return $payment;
        }

        $payment->update([
            'status' => 'approved',
            'approved_at' => now(),
            'checkout_payload' => array_merge($payment->checkout_payload ?? [], $extraPayload),
        ]);

        $plan = Plan::where('slug', $payment->plan_slug)->where('is_active', true)->first();
        if (! $plan) {
            return $payment;
        }

        $user = $payment->user;
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

        $this->vouchers->recordRedemption($payment->voucher_code);

        return $payment;
    }
}
