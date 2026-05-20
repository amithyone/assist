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
        $platforms = [];
        foreach ($this->releases->platformDefinitions() as $key => $def) {
            $release = $this->releases->platformRelease($key);
            $path = $this->releases->platformFilePath($key);
            $platforms[$key] = [
                'key' => $key,
                'label' => $def['label'] ?? $key,
                'description' => $def['description'] ?? '',
                'extensions' => $def['extensions'] ?? [],
                'release' => $release,
                'fileExists' => (bool) $path,
                'fileSizeMb' => $path ? round(filesize($path) / 1024 / 1024, 2) : 0,
                'downloadUrl' => $this->releases->platformDownloadUrl($key),
            ];
        }

        return view('admin.assist-downloads.index', [
            'platforms' => $platforms,
            'availableCount' => count($this->releases->availablePlatforms()),
            'primaryDownloadUrl' => $this->releases->downloadUrl(),
            'macNotice' => config('assist.release.mac_availability_notice'),
            'maxUploadMb' => (int) config('assist.release.max_upload_mb', 500),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $platformKeys = implode(',', array_keys($this->releases->platformDefinitions()));

        $data = $request->validate([
            'platform' => 'required|string|in:'.$platformKeys,
            'installer' => 'required|file|max:'.((int) config('assist.release.max_upload_kb', 512000)),
            'version' => 'nullable|string|max:32',
            'release_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $manifest = $this->releases->storePlatformUpload(
                $data['installer'],
                $data['platform'],
                $data['version'] ?? null,
                $data['release_notes'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['installer' => $e->getMessage()])->withInput();
        }

        $label = $this->releases->platformDefinitions()[$data['platform']]['label'] ?? $data['platform'];

        return back()->with('status', sprintf(
            '%s uploaded (%s, %.1f MB). Public URL: %s',
            $label,
            $manifest['stored_filename'],
            ($manifest['size_bytes'] ?? 0) / 1024 / 1024,
            $this->releases->platformDownloadUrl($data['platform'])
        ));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $platformKeys = implode(',', array_keys($this->releases->platformDefinitions()));

        $data = $request->validate([
            'confirm' => 'accepted',
            'platform' => 'required|string|in:'.$platformKeys,
        ]);

        $this->releases->removePlatform($data['platform']);
        $label = $this->releases->platformDefinitions()[$data['platform']]['label'] ?? $data['platform'];

        return back()->with('status', $label.' release removed. It will no longer appear on the site.');
    }
}
