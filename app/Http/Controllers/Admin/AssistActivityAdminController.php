<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsageEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssistActivityAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = UsageEvent::with('user')->orderByDesc('occurred_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('feature')) {
            $query->where('feature', $request->feature);
        }
        if ($request->filled('from')) {
            $query->where('occurred_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('occurred_at', '<=', $request->to);
        }

        $events = $query->paginate(50)->withQueryString();
        $users = User::orderBy('name')->limit(200)->get(['id', 'name', 'email']);

        return view('admin.assist-activity.index', compact('events', 'users'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = UsageEvent::with('user')->orderByDesc('occurred_at');
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $filename = 'assist-activity-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'user_email', 'event', 'feature', 'status', 'project_type', 'occurred_at', 'excerpt']);
            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    $excerpt = $row->content_summary['transcript_excerpt'] ?? '';
                    fputcsv($out, [
                        $row->id,
                        $row->user?->email,
                        $row->event,
                        $row->feature,
                        $row->status,
                        $row->project_type,
                        $row->occurred_at?->toIso8601String(),
                        is_string($excerpt) ? mb_substr($excerpt, 0, 200) : '',
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
