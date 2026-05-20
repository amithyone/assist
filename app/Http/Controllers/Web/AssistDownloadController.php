<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AssistAppReleaseService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssistDownloadController extends Controller
{
    public function download(AssistAppReleaseService $releases): BinaryFileResponse
    {
        $path = $releases->releaseFilePath();
        if (! $path) {
            abort(404, 'No Assist installer has been uploaded yet.');
        }

        $manifest = $releases->currentRelease();
        $downloadName = $manifest['original_filename'] ?? basename($path);

        return response()->download($path, $downloadName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
