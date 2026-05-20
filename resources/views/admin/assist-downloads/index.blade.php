<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>App Download — Assist Admin</title>
    <link rel="stylesheet" href="{{ asset('assist/assist-site.css') }}">
</head>
<body style="background: #0f172a; color: #e2e8f0; padding: 40px;">
@include('admin.partials.nav')
<div style="max-width: 720px; margin: 0 auto;">
    <h1 style="font-size: 28px; margin-bottom: 8px;">App download</h1>
    <p style="opacity: .7; margin-bottom: 24px;">Upload the macOS installer (.dmg or .zip). Users download it from your site via the public link below.</p>

    @if (session('status'))
        <p style="color: #34d399; margin-bottom: 16px;">{{ session('status') }}</p>
    @endif
    @if ($errors->any())
        <div style="color: #f87171; margin-bottom: 16px;">
            @foreach ($errors->all() as $err)
                <p>{{ $err }}</p>
            @endforeach
        </div>
    @endif

    <div class="glass-panel" style="padding: 24px; border-radius: 16px; margin-bottom: 24px;">
        <h2 style="font-size: 16px; margin-bottom: 12px;">Current release</h2>
        @if ($fileExists && $release)
            <p><strong>Version:</strong> {{ $release['version'] ?? '—' }}</p>
            <p><strong>File:</strong> {{ $release['stored_filename'] }} ({{ $fileSizeMb }} MB)</p>
            <p><strong>Uploaded:</strong> {{ $release['uploaded_at'] ?? '—' }}</p>
            @if (!empty($release['release_notes']))
                <p><strong>Notes:</strong> {{ $release['release_notes'] }}</p>
            @endif
            <p style="margin-top: 16px;"><strong>Public download URL:</strong></p>
            <p><a href="{{ $downloadUrl }}" style="color: #818cf8; word-break: break-all;">{{ $downloadUrl }}</a></p>
            <p style="font-size: 12px; opacity: .6; margin-top: 8px;">Homepage, dashboard, and nav “Download” buttons use this link automatically after upload.</p>
        @else
            <p style="opacity: .7;">No installer uploaded yet. Links fall back to <code>ASSIST_DOWNLOAD_URL</code> in .env.</p>
        @endif
    </div>

    <div class="glass-panel" style="padding: 24px; border-radius: 16px; margin-bottom: 24px;">
        <h2 style="font-size: 16px; margin-bottom: 16px;">Upload new installer</h2>
        <form method="POST" action="{{ route('admin.assist.downloads.store') }}" enctype="multipart/form-data">
            @csrf
            <p style="margin-bottom: 8px; font-size: 14px;">Installer file (.dmg or .zip, max {{ $maxUploadMb }} MB)</p>
            <input type="file" name="installer" accept=".dmg,.zip,application/zip,application/x-apple-diskimage" required
                   style="margin-bottom: 16px; width: 100%; color: #e2e8f0;">
            <p style="margin-bottom: 8px; font-size: 14px;">Version label (optional)</p>
            <input type="text" name="version" value="{{ old('version', $release['version'] ?? config('assist.app_version')) }}"
                   placeholder="e.g. 1.2.0" style="width: 100%; padding: 10px; margin-bottom: 16px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #fff;">
            <p style="margin-bottom: 8px; font-size: 14px;">Release notes (optional)</p>
            <textarea name="release_notes" rows="3" placeholder="What's new in this build..."
                      style="width: 100%; padding: 10px; margin-bottom: 16px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #fff;">{{ old('release_notes', $release['release_notes'] ?? '') }}</textarea>
            <button type="submit" style="padding: 12px 24px; border-radius: 8px; background: #6366f1; color: #fff; border: none; cursor: pointer; font-weight: 600;">
                Upload &amp; publish download link
            </button>
        </form>
        <p style="font-size: 12px; opacity: .6; margin-top: 12px;">
            On shared hosting, raise PHP <code>upload_max_filesize</code> and <code>post_max_size</code> in hPanel if large DMGs fail.
        </p>
    </div>

    @if ($fileExists)
        <form method="POST" action="{{ route('admin.assist.downloads.destroy') }}" onsubmit="return confirm('Remove the uploaded installer?');">
            @csrf
            @method('DELETE')
            <label style="font-size: 14px; display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <input type="checkbox" name="confirm" value="1" required>
                I want to remove the current installer
            </label>
            <button type="submit" style="padding: 10px 20px; border-radius: 8px; background: #475569; color: #fff; border: none; cursor: pointer;">
                Remove release
            </button>
        </form>
    @endif
</div>
</body>
</html>
