<?php

namespace App\Http\Controllers\Api\Assist;

use App\Http\Controllers\Controller;
use App\Services\UsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function check(Request $request, UsageService $usage): JsonResponse
    {
        $data = $request->validate([
            'feature' => 'required|string|in:timelines,transcribe_clips,reel_clones,beat_edits',
            'units' => 'integer|min:1|max:1000',
        ]);

        $result = $usage->check(
            $request->user(),
            $data['feature'],
            (int) ($data['units'] ?? 1)
        );

        return response()->json($result);
    }

    public function record(Request $request, UsageService $usage): JsonResponse
    {
        $data = $request->validate([
            'feature' => 'required|string',
            'event' => 'required|string',
            'units' => 'integer|min:0|max:1000',
            'client_event_id' => 'nullable|uuid',
            'status' => 'nullable|string',
            'project_type' => 'nullable|string',
            'metrics' => 'nullable|array',
            'content_summary' => 'nullable|array',
            'details' => 'nullable|array',
        ]);

        $data['increment_counter'] = true;
        $data['units'] = (int) ($data['units'] ?? 1);

        $event = $usage->recordEvent($request->user(), $data);

        return response()->json([
            'success' => true,
            'event_id' => $event->id,
            'limits' => $usage->limitsSnapshot($request->user()),
        ]);
    }
}
