@extends('layouts.admin')

@section('title', 'App release')
@section('page_title', 'App release')

@section('content')
<div class="assist-admin-card glass-panel">
    <h2>Published on site</h2>
    <p class="assist-text-muted mb-4">
        Upload one installer per platform. Empty platforms are hidden from the public site.
        Right now Assist is built for <strong>Apple Silicon Mac (arm64)</strong>; add Intel Mac, Windows, or Linux when those builds are ready.
    </p>
    @if ($availableCount > 0)
        <p class="assist-text-muted mb-4">Primary download URL: <a href="{{ $primaryDownloadUrl }}" style="color: var(--primary-hover); word-break: break-all;">{{ $primaryDownloadUrl }}</a></p>
    @else
        <p class="assist-text-muted">Nothing published yet. Upload at least <strong>Mac (Apple Silicon)</strong> to enable downloads on the site.</p>
    @endif
</div>

@foreach ($platforms as $platform)
<div class="assist-admin-card glass-panel">
    <h2>{{ $platform['label'] }}</h2>
    <p class="assist-text-muted mb-4">{{ $platform['description'] }}</p>
    <p class="assist-text-muted mb-4">Allowed: .{{ implode(', .', $platform['extensions']) }} (max {{ $maxUploadMb }} MB)</p>

    @if ($platform['fileExists'] && $platform['release'])
        <div class="assist-admin-form-grid assist-admin-form-grid-2 mb-4">
            <p><span class="assist-text-muted">Version</span><br><strong>{{ $platform['release']['version'] ?? '—' }}</strong></p>
            <p><span class="assist-text-muted">Size</span><br><strong>{{ $platform['fileSizeMb'] }} MB</strong></p>
            <p><span class="assist-text-muted">File</span><br>{{ $platform['release']['stored_filename'] }}</p>
            <p><span class="assist-text-muted">Uploaded</span><br>{{ $platform['release']['uploaded_at'] ?? '—' }}</p>
        </div>
        @if (!empty($platform['release']['release_notes']))
            <p class="mb-4"><span class="assist-text-muted">Notes</span><br>{{ $platform['release']['release_notes'] }}</p>
        @endif
        <p class="assist-admin-section-title">Public URL</p>
        <a href="{{ $platform['downloadUrl'] }}" class="assist-text-muted" style="color: var(--primary-hover); word-break: break-all;">{{ $platform['downloadUrl'] }}</a>
        <p class="assist-text-muted mt-4" style="font-size: 0.875rem;">Visible on homepage, dashboard, and nav when uploaded.</p>

        <form method="POST" action="{{ route('admin.assist.downloads.destroy') }}" class="mt-6" onsubmit="return confirm('Remove {{ $platform['label'] }} from the site?');">
            @csrf
            @method('DELETE')
            <input type="hidden" name="platform" value="{{ $platform['key'] }}">
            <label class="assist-checkbox-row">
                <input type="checkbox" name="confirm" value="1" required>
                Remove {{ $platform['label'] }} release
            </label>
            <button type="submit" class="assist-btn assist-btn-outline mt-4">Remove from site</button>
        </form>
    @else
        <p class="assist-text-muted mb-4">Not published — visitors will not see a download button for this platform.</p>
    @endif

    <h3 class="mt-6" style="font-size: 1rem;">{{ $platform['fileExists'] ? 'Replace' : 'Upload' }} installer</h3>
    <form method="POST" action="{{ route('admin.assist.downloads.store') }}" enctype="multipart/form-data" class="assist-admin-form-grid mt-4" style="max-width: 560px;">
        @csrf
        <input type="hidden" name="platform" value="{{ $platform['key'] }}">
        <div class="assist-field">
            <label for="installer_{{ $platform['key'] }}">Installer file</label>
            <input type="file" id="installer_{{ $platform['key'] }}" name="installer" class="assist-input" accept="{{ collect($platform['extensions'])->map(fn ($e) => '.'.$e)->implode(',') }}" required>
        </div>
        <x-assist.input label="Version label" name="version" :value="old('version', $platform['release']['version'] ?? config('assist.app_version'))" />
        <div class="assist-field">
            <label for="release_notes_{{ $platform['key'] }}">Release notes</label>
            <textarea id="release_notes_{{ $platform['key'] }}" name="release_notes" class="assist-textarea" rows="2">{{ old('release_notes', $platform['release']['release_notes'] ?? '') }}</textarea>
        </div>
        <div>
            <button type="submit" class="assist-btn assist-btn-primary">Upload & publish</button>
        </div>
    </form>
</div>
@endforeach
@endsection
