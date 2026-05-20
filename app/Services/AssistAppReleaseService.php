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
     * @return array<string, array{label: string, short_label: string, description: string, extensions: string[], sort: int}>
     */
    public function platformDefinitions(): array
    {
        $platforms = config('assist.release.platforms', []);

        return is_array($platforms) ? $platforms : [];
    }

    public function isValidPlatform(string $platform): bool
    {
        return array_key_exists($platform, $this->platformDefinitions());
    }

    /**
     * Normalized manifest with a `platforms` map (migrates legacy single-file uploads).
     *
     * @return array<string, mixed>
     */
    public function manifest(): array
    {
        $path = $this->manifestPath();
        if (! is_file($path)) {
            return ['platforms' => []];
        }

        $data = json_decode((string) file_get_contents($path), true);
        if (! is_array($data)) {
            return ['platforms' => []];
        }

        if (isset($data['platforms']) && is_array($data['platforms'])) {
            return $data;
        }

        if (! empty($data['stored_filename'])) {
            return [
                'platforms' => [
                    'mac_arm64' => $data,
                ],
            ];
        }

        return ['platforms' => []];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function currentRelease(): ?array
    {
        $manifest = $this->manifest();
        $platforms = $manifest['platforms'] ?? [];
        if ($platforms === []) {
            return null;
        }

        $primary = $this->primaryPlatformKey();
        if ($primary && isset($platforms[$primary])) {
            return $platforms[$primary];
        }

        return reset($platforms) ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function platformRelease(string $platform): ?array
    {
        if (! $this->isValidPlatform($platform)) {
            return null;
        }

        $entry = $this->manifest()['platforms'][$platform] ?? null;

        return is_array($entry) ? $entry : null;
    }

    public function platformFilePath(string $platform): ?string
    {
        $release = $this->platformRelease($platform);
        if (! $release || empty($release['stored_filename'])) {
            return null;
        }

        $path = $this->storageDir().'/'.$release['stored_filename'];

        return is_file($path) ? $path : null;
    }

    public function releaseFilePath(): ?string
    {
        $primary = $this->primaryPlatformKey();

        return $primary ? $this->platformFilePath($primary) : null;
    }

    public function hasAnyRelease(): bool
    {
        return $this->availablePlatforms() !== [];
    }

    /**
     * @return list<string>
     */
    public function availablePlatforms(): array
    {
        $available = [];
        foreach ($this->sortedPlatformKeys() as $key) {
            if ($this->platformFilePath($key)) {
                $available[] = $key;
            }
        }

        return $available;
    }

    /**
     * @return list<array{key: string, label: string, short_label: string, description: string, url: string, version: string|null, size_mb: float}>
     */
    public function availableDownloads(): array
    {
        $out = [];
        foreach ($this->availablePlatforms() as $key) {
            $def = $this->platformDefinitions()[$key] ?? [];
            $release = $this->platformRelease($key) ?? [];
            $path = $this->platformFilePath($key);
            $out[] = [
                'key' => $key,
                'label' => $def['label'] ?? $key,
                'short_label' => $def['short_label'] ?? $key,
                'description' => $def['description'] ?? '',
                'url' => $this->platformDownloadUrl($key),
                'version' => $release['version'] ?? null,
                'size_mb' => $path ? round(filesize($path) / 1024 / 1024, 1) : 0.0,
            ];
        }

        return $out;
    }

    public function primaryPlatformKey(): ?string
    {
        $available = $this->availablePlatforms();
        if ($available === []) {
            return null;
        }

        foreach (['mac_arm64', 'mac_x86_64', 'windows', 'linux'] as $preferred) {
            if (in_array($preferred, $available, true)) {
                return $preferred;
            }
        }

        return $available[0];
    }

    public function platformDownloadUrl(string $platform): string
    {
        if ($this->platformFilePath($platform)) {
            return route('assist.download.platform', ['platform' => $platform]);
        }

        return config('assist.download_url', '#download');
    }

    public function downloadUrl(?string $platform = null): string
    {
        if ($platform !== null && $this->isValidPlatform($platform) && $this->platformFilePath($platform)) {
            return $this->platformDownloadUrl($platform);
        }

        $primary = $this->primaryPlatformKey();
        if ($primary) {
            return $this->platformDownloadUrl($primary);
        }

        return config('assist.download_url', '#download');
    }

    public function macAvailabilityNotice(): ?string
    {
        $available = $this->availablePlatforms();
        if ($available === []) {
            return null;
        }

        $onlyArmMac = $available === ['mac_arm64']
            || (count($available) === 1 && $available[0] === 'mac_arm64');

        if ($onlyArmMac) {
            return config('assist.release.mac_availability_notice');
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected function sortedPlatformKeys(): array
    {
        $keys = array_keys($this->platformDefinitions());
        usort($keys, function (string $a, string $b): int {
            $sortA = (int) ($this->platformDefinitions()[$a]['sort'] ?? 99);
            $sortB = (int) ($this->platformDefinitions()[$b]['sort'] ?? 99);
            if ($sortA === $sortB) {
                return strcmp($a, $b);
            }

            return $sortA <=> $sortB;
        });

        return $keys;
    }

    public function storeUpload(
        UploadedFile $file,
        string $platform,
        ?string $version = null,
        ?string $releaseNotes = null
    ): array {
        return $this->storePlatformUpload($file, $platform, $version, $releaseNotes);
    }

    public function storePlatformUpload(
        UploadedFile $file,
        string $platform,
        ?string $version = null,
        ?string $releaseNotes = null
    ): array {
        if (! $this->isValidPlatform($platform)) {
            throw new RuntimeException('Unknown platform.');
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        $allowed = array_map('strtolower', $this->platformDefinitions()[$platform]['extensions'] ?? []);
        if ($ext === '' || ! in_array($ext, $allowed, true)) {
            throw new RuntimeException(
                'Invalid file type for '.$this->platformDefinitions()[$platform]['label']
                .'. Allowed: '.implode(', ', $allowed)
            );
        }

        $dir = $this->storageDir();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $storedFilename = 'Assist_AI_Editor_'.$platform.'.'.$ext;
        $previous = $this->platformRelease($platform);
        if ($previous && ! empty($previous['stored_filename']) && $previous['stored_filename'] !== $storedFilename) {
            $oldPath = $dir.'/'.$previous['stored_filename'];
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
            $oldPublic = $this->publicCopyDir().'/'.$previous['stored_filename'];
            if (is_file($oldPublic)) {
                unlink($oldPublic);
            }
        }

        $originalName = $file->getClientOriginalName();
        $file->move($dir, $storedFilename);
        $fullPath = $dir.'/'.$storedFilename;

        $entry = [
            'platform' => $platform,
            'version' => $version ?: config('assist.app_version', '1.0.0'),
            'original_filename' => $originalName,
            'stored_filename' => $storedFilename,
            'size_bytes' => filesize($fullPath) ?: 0,
            'uploaded_at' => now()->toIso8601String(),
            'release_notes' => $releaseNotes,
        ];

        $manifest = $this->manifest();
        $manifest['platforms'][$platform] = $entry;
        file_put_contents($this->manifestPath(), json_encode($manifest, JSON_PRETTY_PRINT));

        $this->syncToPublicCopy($fullPath, $storedFilename);

        if ($this->primaryPlatformKey() === $platform) {
            $this->updateDownloadUrlInEnv();
        }

        return $entry;
    }

    public function removeRelease(): void
    {
        $this->removeAllReleases();
    }

    public function removePlatform(string $platform): void
    {
        if (! $this->isValidPlatform($platform)) {
            return;
        }

        $release = $this->platformRelease($platform);
        if ($release && ! empty($release['stored_filename'])) {
            $path = $this->storageDir().'/'.$release['stored_filename'];
            if (is_file($path)) {
                unlink($path);
            }
            $publicPath = $this->publicCopyDir().'/'.$release['stored_filename'];
            if (is_file($publicPath)) {
                unlink($publicPath);
            }
        }

        $manifest = $this->manifest();
        unset($manifest['platforms'][$platform]);
        if (($manifest['platforms'] ?? []) === []) {
            if (is_file($this->manifestPath())) {
                unlink($this->manifestPath());
            }
        } else {
            file_put_contents($this->manifestPath(), json_encode($manifest, JSON_PRETTY_PRINT));
        }
    }

    public function removeAllReleases(): void
    {
        foreach (array_keys($this->platformDefinitions()) as $platform) {
            $this->removePlatform($platform);
        }
        if (is_file($this->manifestPath())) {
            unlink($this->manifestPath());
        }
    }

    protected function syncToPublicCopy(string $sourcePath, string $filename): void
    {
        $publicDir = $this->publicCopyDir();
        if (! is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        copy($sourcePath, $publicDir.'/'.$filename);
    }

    protected function updateDownloadUrlInEnv(): void
    {
        $primary = $this->primaryPlatformKey();
        $url = $primary
            ? $this->platformDownloadUrl($primary)
            : config('assist.download_url', '#download');

        (new EnvWriter)->setMany(['ASSIST_DOWNLOAD_URL' => $url]);
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        } catch (\Throwable) {
            // ignore during partial bootstrap
        }
    }
}
