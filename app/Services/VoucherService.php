<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use App\Models\Voucher;
use RuntimeException;

class VoucherService
{
    public function normalizeCode(?string $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return $code !== '' ? $code : null;
    }

    public function findValid(string $code, User $user, Plan $plan, string $currency): Voucher
    {
        $normalized = $this->normalizeCode($code);
        if (! $normalized) {
            throw new RuntimeException('Enter a voucher code.');
        }

        $voucher = Voucher::where('code', $normalized)->first();
        if (! $voucher) {
            throw new RuntimeException('This voucher code is not valid.');
        }

        $this->assertApplicable($voucher, $plan, $currency);

        return $voucher;
    }

    public function assertApplicable(Voucher $voucher, Plan $plan, string $currency): void
    {
        $currency = strtolower($currency);

        if (! $voucher->is_active) {
            throw new RuntimeException('This voucher is no longer active.');
        }

        if ($voucher->starts_at && $voucher->starts_at->isFuture()) {
            throw new RuntimeException('This voucher is not active yet.');
        }

        if ($voucher->expires_at && $voucher->expires_at->isPast()) {
            throw new RuntimeException('This voucher has expired.');
        }

        if ($voucher->max_redemptions !== null && $voucher->redemption_count >= $voucher->max_redemptions) {
            throw new RuntimeException('This voucher has reached its redemption limit.');
        }

        if ($voucher->plan_slug && $voucher->plan_slug !== $plan->slug) {
            throw new RuntimeException('This voucher does not apply to the selected plan.');
        }

        if ($plan->slug === 'free' || $plan->priceForCurrency($currency) <= 0) {
            throw new RuntimeException('This plan does not require payment.');
        }

        if ($voucher->discount_type === Voucher::DISCOUNT_FIXED_NGN && $currency !== 'ngn') {
            throw new RuntimeException('This voucher only applies to NGN checkout.');
        }

        if ($voucher->discount_type === Voucher::DISCOUNT_FIXED_USD && $currency !== 'usd') {
            throw new RuntimeException('This voucher only applies to USD checkout.');
        }
    }

    /**
     * @return array{final: float, discount: float, original: float}
     */
    public function priceBreakdown(float $original, Voucher $voucher, string $currency): array
    {
        $currency = strtolower($currency);
        $original = max(0.0, $original);
        $discount = 0.0;

        if ($voucher->discount_type === Voucher::DISCOUNT_PERCENT) {
            $pct = min(100.0, max(0.0, (float) $voucher->discount_value));
            $discount = round($original * ($pct / 100), 2);
        } elseif ($voucher->discount_type === Voucher::DISCOUNT_FIXED_NGN && $currency === 'ngn') {
            $discount = min($original, (float) $voucher->discount_value);
        } elseif ($voucher->discount_type === Voucher::DISCOUNT_FIXED_USD && $currency === 'usd') {
            $discount = min($original, (float) $voucher->discount_value);
        }

        $discount = min($original, max(0.0, $discount));
        $final = max(0.0, round($original - $discount, 2));

        if ($final > 0 && $final < 1) {
            $final = 1.0;
            $discount = max(0.0, round($original - $final, 2));
        }

        return [
            'final' => $final,
            'discount' => $discount,
            'original' => $original,
        ];
    }

    public function recordRedemption(?string $code): void
    {
        $normalized = $this->normalizeCode($code);
        if (! $normalized) {
            return;
        }

        Voucher::where('code', $normalized)->increment('redemption_count');
    }
}
