<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AssistAppReleaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AssistDownloadAdminController extends Controller
{
    public function __construct(
        protected AssistAppReleaseService $releases
    ) {}

    public function index(): View
    {
        $release = $this->releases->currentRelease();
        $filePath = $this->releases->releaseFilePath();

        return view('admin.assist-downloads.index', [
            'release' => $release,
            'fileExists' => (bool) $filePath,
            'fileSizeMb' => $filePath ? round(filesize($filePath) / 1024 / 1024, 2) : 0,
            'downloadUrl' => $this->releases->downloadUrl(),
            'maxUploadMb' => (int) config('assist.release.max_upload_mb', 500),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'installer' => 'required|file|max:'.((int) config('assist.release.max_upload_kb', 512000)),
            'version' => 'nullable|string|max:32',
            'release_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $manifest = $this->releases->storeUpload(
                $data['installer'],
                $data['version'] ?? null,
                $data['release_notes'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['installer' => $e->getMessage()]);
        }

        return back()->with('status', sprintf(
            'App uploaded (%s, %.1f MB). Download link is now: %s',
            $manifest['stored_filename'],
            ($manifest['size_bytes'] ?? 0) / 1024 / 1024,
            $this->releases->downloadUrl()
        ));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => 'accepted']);
        $this->releases->removeRelease();

        return back()->with('status', 'App release removed. Site download links will use ASSIST_DOWNLOAD_URL from .env until you upload again.');
    }
}
