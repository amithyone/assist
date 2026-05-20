<?php

namespace App\Http\Controllers\Api\Assist;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request, ActivityService $activity): JsonResponse
    {
        $perPage = min(100, max(10, (int) $request->query('per_page', 50)));
        $paginated = $activity->paginatedForUser($request->user(), $perPage);

        return response()->json($paginated);
    }

    public function sync(Request $request, ActivityService $activity): JsonResponse
    {
        $max = (int) config('assist.activity_sync_max_batch', 50);
        $data = $request->validate([
            'events' => 'required|array|max:'.$max,
            'events.*.client_event_id' => 'required|uuid',
            'events.*.feature' => 'required|string',
            'events.*.event' => 'required|string',
            'events.*.status' => 'nullable|string',
            'events.*.occurred_at' => 'nullable|date',
            'events.*.project_type' => 'nullable|string',
            'events.*.metrics' => 'nullable|array',
            'events.*.content_summary' => 'nullable|array',
        ]);

        $result = $activity->syncBatch($request->user(), $data['events']);

        return response()->json([
            'success' => true,
            ...$result,
        ]);
    }
}
