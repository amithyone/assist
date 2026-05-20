<!DOCTYPE html>
<html lang="en">
@php use Illuminate\Support\Str; @endphp
<head>
    <meta charset="UTF-8">
    <title>Assist Activity</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 24px; background: #0f172a; color: #e2e8f0; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { border-bottom: 1px solid #334155; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { color: #94a3b8; }
        .filters { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; align-items: end; }
        input, select, button { padding: 8px 12px; border-radius: 6px; border: 1px solid #475569; background: #1e293b; color: #f8fafc; }
        button, a.btn { background: #6366f1; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .excerpt { max-width: 280px; color: #94a3b8; font-size: 12px; }
        .badge { padding: 2px 8px; border-radius: 4px; background: #334155; font-size: 12px; }
        .badge.success { background: #166534; }
        .badge.denied { background: #991b1b; }
    </style>
</head>
<body>
    @include('admin.partials.nav')
    <h1>Assist usage &amp; activity</h1>
    <form class="filters" method="get">
        <label>User
            <select name="user_id">
                <option value="">All</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->email }}</option>
                @endforeach
            </select>
        </label>
        <label>Feature
            <select name="feature">
                <option value="">All</option>
                @foreach(['preproduction','reel_clones','beat_edits','music_video_cuts','ai_edits','timelines','transcribe_clips'] as $f)
                    <option value="{{ $f }}" @selected(request('feature') === $f)>{{ $f }}</option>
                @endforeach
            </select>
        </label>
        <label>From <input type="date" name="from" value="{{ request('from') }}"></label>
        <label>To <input type="date" name="to" value="{{ request('to') }}"></label>
        <button type="submit">Filter</button>
        <a class="btn" href="{{ url()->current() }}?{{ http_build_query(request()->only(['user_id','feature','from','to'])) }}&export=1"
           onclick="this.href='{{ route('admin.assist.activity.export', request()->only(['user_id','feature','from','to'])) }}'">Export CSV</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>User</th>
                <th>Event</th>
                <th>Feature</th>
                <th>Status</th>
                <th>Project</th>
                <th>Metrics</th>
                <th>Excerpt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $e)
                <tr>
                    <td>{{ $e->occurred_at?->format('Y-m-d H:i') ?? $e->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $e->user?->email }}</td>
                    <td>{{ $e->event }}</td>
                    <td>{{ $e->feature }}</td>
                    <td><span class="badge {{ $e->status === 'success' ? 'success' : '' }}">{{ $e->status }}</span></td>
                    <td>{{ $e->project_type ?? '—' }}</td>
                    <td><code style="font-size:11px">{{ json_encode($e->metrics) }}</code></td>
                    <td class="excerpt">{{ Str::limit($e->content_summary['transcript_excerpt'] ?? '', 120) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No activity yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap" style="margin-top:16px">{{ $events->links() }}</div>
</body>
</html>
