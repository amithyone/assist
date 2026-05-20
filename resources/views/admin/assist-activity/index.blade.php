@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')

@section('title', 'Activity')
@section('page_title', 'Usage & activity')

@section('content')
<div class="assist-admin-card glass-panel">
    <form method="get" class="assist-admin-form-grid assist-admin-form-grid-2" style="align-items: end;">
        <div class="assist-field">
            <label for="user_id">User</label>
            <select id="user_id" name="user_id" class="assist-select">
                <option value="">All users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->email }}</option>
                @endforeach
            </select>
        </div>
        <div class="assist-field">
            <label for="feature">Feature</label>
            <select id="feature" name="feature" class="assist-select">
                <option value="">All features</option>
                @foreach(['preproduction','reel_clones','beat_edits','music_video_cuts','ai_edits','timelines','transcribe_clips'] as $f)
                    <option value="{{ $f }}" @selected(request('feature') === $f)>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <x-assist.input label="From" name="from" type="date" :value="request('from')" />
        <x-assist.input label="To" name="to" type="date" :value="request('to')" />
        <div class="flex gap-4">
            <button type="submit" class="assist-btn assist-btn-primary">Filter</button>
            <a href="{{ route('admin.assist.activity.export', request()->only(['user_id','feature','from','to'])) }}" class="assist-btn assist-btn-outline">Export CSV</a>
        </div>
    </form>
</div>

<div class="assist-admin-card glass-panel">
    <div class="assist-admin-table-wrap">
        <table class="assist-admin-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Feature</th>
                    <th>Status</th>
                    <th>Project</th>
                    <th>Excerpt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $e)
                    <tr>
                        <td class="assist-text-muted">{{ $e->occurred_at?->format('M j, H:i') ?? $e->created_at->format('M j, H:i') }}</td>
                        <td>{{ $e->user?->email }}</td>
                        <td>{{ $e->event }}</td>
                        <td>{{ $e->feature }}</td>
                        <td>
                            <span class="assist-admin-badge {{ $e->status === 'success' ? 'assist-admin-badge-success' : 'assist-admin-badge-muted' }}">{{ $e->status }}</span>
                        </td>
                        <td>{{ $e->project_type ?? '—' }}</td>
                        <td class="assist-text-muted" style="max-width: 200px;">{{ Str::limit($e->content_summary['transcript_excerpt'] ?? '', 80) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
</div>
@endsection
