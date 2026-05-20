<?php

namespace App\Models\Concerns;

use App\Models\Plan;
use App\Models\UsageCounter;
use App\Models\UserPlan;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAssistPlan
{
    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    public function usageCounters(): HasMany
    {
        return $this->hasMany(UsageCounter::class);
    }

    public function usageEvents(): HasMany
    {
        return $this->hasMany(\App\Models\UsageEvent::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function currentPlan(): ?Plan
    {
        $userPlan = $this->userPlans()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('id')
            ->first();

        return $userPlan?->plan;
    }

    public function currentUserPlan(): ?UserPlan
    {
        return $this->userPlans()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function usagePeriodKey(?Plan $plan = null): string
    {
        $plan = $plan ?? $this->currentPlan();
        if ($plan && $plan->usage_period === 'weekly') {
            return now()->format('o-\WW');
        }

        return now()->format('Y-m');
    }

    public function usageForPeriod(?string $period = null): UsageCounter
    {
        $period = $period ?? $this->usagePeriodKey();

        return $this->usageCounters()->firstOrCreate(
            ['period' => $period],
            array_fill_keys(UsageCounter::COUNTABLE_FEATURES, 0)
        );
    }
}
