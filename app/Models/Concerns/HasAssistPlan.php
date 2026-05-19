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

    public function usageForPeriod(?string $period = null): UsageCounter
    {
        $period = $period ?? now()->format('Y-m');

        return $this->usageCounters()->firstOrCreate(
            ['period' => $period],
            [
                'timelines' => 0,
                'transcribe_clips' => 0,
                'reel_clones' => 0,
                'beat_edits' => 0,
            ]
        );
    }
}
