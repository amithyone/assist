@extends('layouts.admin')

@section('title', 'Site content')
@section('page_title', 'Site content')

@section('content')
<div class="assist-admin-card glass-panel">
    <p class="assist-text-muted mb-4">Edit homepage sections, featured images, and SEO for all public marketing pages.</p>
    <table class="assist-admin-table">
        <thead>
            <tr>
                <th>Page</th>
                <th>Slug</th>
                <th>Published</th>
                <th>Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{ $page->name }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>{{ $page->is_published ? 'Yes' : 'No' }}</td>
                    <td>{{ $page->updated_at?->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.assist.site-pages.edit', $page) }}" class="assist-btn assist-btn-outline assist-btn-sm">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($pages->isEmpty())
        <p class="assist-text-muted">No pages yet. Run <code>php artisan db:seed --class=SitePageSeeder</code>.</p>
    @endif
</div>
@endsection
