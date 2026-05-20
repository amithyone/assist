<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use RuntimeException;

class AssistAppReleaseService
{
    public function storageDir(): string
    {
        return storage_path('app/'.trim(config('assist.release.storage_dir', 'assist-releases'), '/'));
    }

    public function manifestPath(): string
    {
        return $this->storageDir().'/manifest.json';
    }

    public function publicCopyDir(): string
    {
        return public_path(trim(config('assist.release.public_subdir', 'assist/downloads'), '/'));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function currentRelease(): ?array
    {
        $path = $this->manifestPath();
        if (! is_file($path)) {
            return null;
        }
        $data = json_decode((string) file_get_contents($path), true);

        return is_array($data) ? $data : null;
    }

    public function releaseFilePath(): ?string
    {
        $manifest = $this->currentRelease();
        if (! $manifest || empty($manifest['stored_filename'])) {
            return null;
        }
        $path = $this->storageDir().'/'.$manifest['stored_filename'];

        return is_file($path) ? $path : null;
    }

    public function downloadUrl(): string
    {
        if ($this->releaseFilePath()) {
            return url('/download/assist');
        }

        return config('assist.download_url', '#download');
    }

    public function storeUpload(UploadedFile $file, ?string $version = null, ?string $releaseNotes = null): array
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'dmg');
        if (! in_array($ext, ['dmg', 'zip'], true)) {
            throw new RuntimeException('Only .dmg or .zip installers are allowed.');
        }

        $dir = $this->storageDir();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $storedFilename = 'Assist_AI_Editor.'.$ext;
        $file->move($dir, $storedFilename);

        $fullPath = $dir.'/'.$storedFilename;
        $manifest = [
            'version' => $version ?: config('assist.app_version', '1.0.0'),
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'size_bytes' => filesize($fullPath) ?: 0,
            'uploaded_at' => now()->toIso8601String(),
            'release_notes' => $releaseNotes,
        ];

        file_put_contents($this->manifestPath(), json_encode($manifest, JSON_PRETTY_PRINT));

        $this->syncToPublicCopy($fullPath, $storedFilename);
        $this->updateDownloadUrlInEnv();

        return $manifest;
    }

    public function removeRelease(): void
    {
        $manifest = $this->currentRelease();
        if ($manifest && ! empty($manifest['stored_filename'])) {
            $path = $this->storageDir().'/'.$manifest['stored_filename'];
            if (is_file($path)) {
                unlink($path);
            }
        }
        if (is_file($this->manifestPath())) {
            unlink($this->manifestPath());
        }

        $publicDir = $this->publicCopyDir();
        if (is_dir($publicDir)) {
            foreach (glob($publicDir.'/*') ?: [] as $f) {
                if (is_file($f)) {
                    unlink($f);
                }
            }
        }
    }

    protected function syncToPublicCopy(string $sourcePath, string $filename): void
    {
        $publicDir = $this->publicCopyDir();
        if (! is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        foreach (glob($publicDir.'/*') ?: [] as $old) {
            if (is_file($old)) {
                unlink($old);
            }
        }
        copy($sourcePath, $publicDir.'/'.$filename);
    }

    protected function updateDownloadUrlInEnv(): void
    {
        $url = url('/download/assist');
        (new EnvWriter)->setMany(['ASSIST_DOWNLOAD_URL' => $url]);
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        } catch (\Throwable) {
            // ignore during partial bootstrap
        }
    }
}
