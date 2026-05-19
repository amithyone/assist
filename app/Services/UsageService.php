<?php

namespace App\Services;

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

        $counter = $user->usageForPeriod();
        $used = $counter->getCountFor($feature);
        $remaining = max(0, $limit - $used);
        $allowed = ($used + $units) <= $limit;

        return [
            'allowed' => $allowed,
            'remaining' => $remaining,
            'limit' => $limit,
            'used' => $used,
            'message' => $allowed
                ? 'OK'
                : "Monthly limit reached for {$feature}. Upgrade your plan.",
        ];
    }

    public function recordEvent(User $user, array $payload): UsageEvent
    {
        $feature = $payload['feature'] ?? 'unknown';
        $units = (int) ($payload['units'] ?? 1);
        $incrementCounter = (bool) ($payload['increment_counter'] ?? true);

        $event = $this->activityService->createFromPayload($user, $payload);

        if ($incrementCounter && $units > 0 && in_array($feature, [
            'timelines', 'transcribe_clips', 'reel_clones', 'beat_edits',
        ], true)) {
            $counter = $user->usageForPeriod();
            $counter->incrementFeature($feature, $units);
        }

        return $event;
    }

    public function limitsSnapshot(User $user): array
    {
        $plan = $user->currentPlan();
        $counter = $user->usageForPeriod();
        $features = ['timelines', 'transcribe_clips', 'reel_clones', 'beat_edits'];
        $out = [
            'plan' => $plan ? ['slug' => $plan->slug, 'name' => $plan->name] : null,
            'period' => $counter->period,
            'features' => [],
        ];

        foreach ($features as $feature) {
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
