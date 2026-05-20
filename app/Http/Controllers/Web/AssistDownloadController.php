<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AssistAppReleaseService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssistDownloadController extends Controller
{
    public function download(AssistAppReleaseService $releases): BinaryFileResponse
    {
        $primary = $releases->primaryPlatformKey();
        if (! $primary) {
            abort(404, 'No Assist installer has been uploaded yet.');
        }

        return $this->servePlatform($releases, $primary);
    }

    public function downloadPlatform(string $platform, AssistAppReleaseService $releases): BinaryFileResponse
    {
        if (! $releases->isValidPlatform($platform)) {
            abort(404, 'Unknown download platform.');
        }

        return $this->servePlatform($releases, $platform);
    }

    protected function servePlatform(AssistAppReleaseService $releases, string $platform): BinaryFileResponse
    {
        $path = $releases->platformFilePath($platform);
        if (! $path) {
            abort(404, 'No installer is available for this platform.');
        }

        $manifest = $releases->platformRelease($platform);
        $downloadName = $manifest['original_filename'] ?? basename($path);

        return response()->download($path, $downloadName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
