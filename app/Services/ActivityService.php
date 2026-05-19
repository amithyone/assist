<?php

namespace App\Services;

use App\Models\UsageEvent;
use App\Models\User;
use Carbon\Carbon;

class ActivityService
{
    public function createFromPayload(User $user, array $payload): UsageEvent
    {
        $clientId = $payload['client_event_id'] ?? null;
        if ($clientId) {
            $existing = UsageEvent::where('client_event_id', $clientId)->first();
            if ($existing) {
                return $existing;
            }
        }

        $content = $payload['content_summary'] ?? [];
        if (isset($content['transcript_excerpt'])) {
            $content['transcript_excerpt'] = UsageService::truncateExcerpt($content['transcript_excerpt']);
        }

        $occurredAt = isset($payload['occurred_at'])
            ? Carbon::parse($payload['occurred_at'])
            : now();

        return UsageEvent::create([
            'user_id' => $user->id,
            'client_event_id' => $clientId,
            'feature' => $payload['feature'] ?? 'unknown',
            'event' => $payload['event'] ?? 'activity',
            'status' => $payload['status'] ?? 'success',
            'units' => (int) ($payload['units'] ?? 1),
            'project_type' => $payload['project_type'] ?? null,
            'app_version' => $payload['app_version'] ?? config('assist.app_version'),
            'resolve_project_name' => $payload['resolve_project_name'] ?? null,
            'metrics' => $payload['metrics'] ?? null,
            'content_summary' => $content ?: null,
            'details' => $payload['details'] ?? null,
            'occurred_at' => $occurredAt,
        ]);
    }

    public function syncBatch(User $user, array $events): array
    {
        $created = 0;
        $skipped = 0;
        $ids = [];

        foreach ($events as $eventPayload) {
            $eventPayload['increment_counter'] = $eventPayload['increment_counter'] ?? false;
            $before = UsageEvent::where('client_event_id', $eventPayload['client_event_id'] ?? '')->exists();
            $record = app(UsageService::class)->recordEvent($user, $eventPayload);
            if ($before) {
                $skipped++;
            } else {
                $created++;
            }
            $ids[] = $record->id;
        }

        return ['created' => $created, 'skipped' => $skipped, 'ids' => $ids];
    }

    public function paginatedForUser(User $user, int $perPage = 50)
    {
        return UsageEvent::where('user_id', $user->id)
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
