<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\UsageCounter;
use App\Models\UsageEvent;
use App\Models\User;
use Illuminate\Support\Str;

class UsageService
{
    public function __construct(
        protected ActivityService $activityService
    ) {}

    public function check(User $user, string $feature, int $units = 1): array
    {
        if ($user->is_admin ?? false) {
            return [
                'allowed' => true,
                'remaining' => null,
                'limit' => null,
                'message' => 'Admin unlimited',
            ];
        }

        $plan = $user->currentPlan();
        if (! $plan) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'limit' => 0,
                'message' => 'No active plan.',
            ];
        }

        $limit = $plan->limitFor($feature);
        if ($limit === null) {
            return [
                'allowed' => true,
                'remaining' => null,
                'limit' => null,
                'message' => 'Unlimited',
            ];
        }

        $counter = $user->usageForPeriod($user->usagePeriodKey($plan));
        $used = $counter->getCountFor($feature);
        $remaining = max(0, $limit - $used);
        $allowed = ($used + $units) <= $limit;
        $periodLabel = $plan->usage_period === 'weekly' ? 'Weekly' : 'Monthly';

        return [
            'allowed' => $allowed,
            'remaining' => $remaining,
            'limit' => $limit,
            'used' => $used,
            'period' => $counter->period,
            'period_type' => $plan->usage_period,
            'message' => $allowed
                ? 'OK'
                : "{$periodLabel} limit reached for {$feature}. Upgrade your plan.",
        ];
    }

    public function recordEvent(User $user, array $payload): UsageEvent
    {
        $feature = $payload['feature'] ?? 'unknown';
        $units = (int) ($payload['units'] ?? 1);
        $incrementCounter = (bool) ($payload['increment_counter'] ?? true);

        $event = $this->activityService->createFromPayload($user, $payload);

        if ($incrementCounter && $units > 0 && in_array($feature, UsageCounter::COUNTABLE_FEATURES, true)) {
            $plan = $user->currentPlan();
            $counter = $user->usageForPeriod($user->usagePeriodKey($plan));
            $counter->incrementFeature($feature, $units);
        }

        return $event;
    }

    public function limitsSnapshot(User $user): array
    {
        $plan = $user->currentPlan();
        $userPlan = $user->currentUserPlan();
        $counter = $user->usageForPeriod($user->usagePeriodKey($plan));

        $out = [
            'plan' => $plan ? [
                'slug' => $plan->slug,
                'name' => $plan->name,
                'usage_period' => $plan->usage_period,
                'price_ngn' => $plan->price_ngn,
                'price_usd' => $plan->price_usd,
            ] : null,
            'subscription' => $userPlan ? [
                'starts_at' => $userPlan->starts_at?->toIso8601String(),
                'ends_at' => $userPlan->ends_at?->toIso8601String(),
                'status' => $userPlan->status,
            ] : null,
            'period' => $counter->period,
            'period_type' => $plan?->usage_period ?? 'monthly',
            'features' => [],
        ];

        foreach (Plan::FEATURES as $feature) {
            $limit = $plan?->limitFor($feature);
            $used = $counter->getCountFor($feature);
            $out['features'][$feature] = [
                'used' => $used,
                'limit' => $limit,
                'remaining' => $limit === null ? null : max(0, $limit - $used),
            ];
        }

        return $out;
    }

    public static function truncateExcerpt(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }
        $max = (int) config('assist.excerpt_max_length', 500);

        return Str::limit($text, $max);
    }
}
