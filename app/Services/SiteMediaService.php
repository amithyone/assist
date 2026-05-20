<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SiteMediaService
{
    public function publicSubdir(): string
    {
        return trim(config('assist.site_media.public_subdir', 'assist/site'), '/');
    }

    /**
     * Web-accessible document root (public_html on Hostinger, else Laravel public/).
     */
    public function webPublicBase(): string
    {
        $html = base_path('public_html');
        if (is_dir($html)) {
            $realPublic = realpath(public_path()) ?: public_path();
            $realHtml = realpath($html) ?: $html;
            if ($realPublic !== $realHtml) {
                return rtrim($html, '/');
            }
        }

        return public_path();
    }

    public function publicRoot(): string
    {
        return $this->webPublicBase().'/'.$this->publicSubdir();
    }

    public function basePath(): string
    {
        return $this->publicSubdir();
    }

    public function pagePath(string $pageSlug): string
    {
        return $this->basePath().'/'.$pageSlug;
    }

    public function publicPathForDbPath(string $path): string
    {
        $path = ltrim($path, '/');

        return $this->webPublicBase().'/'.$path;
    }

    public function fileExists(?string $path): bool
    {
        if (empty($path) || str_starts_with($path, 'http')) {
            return str_starts_with($path ?? '', 'http');
        }

        return is_file($this->publicPathForDbPath($path));
    }

    public function imageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalized = ltrim($path, '/');

        if ($this->fileExists($normalized)) {
            return asset($normalized);
        }

        // Legacy: file only in storage/app/public — try publish-on-read URL via storage (may 404 on Hostinger)
        $storagePath = $this->legacyStoragePath($normalized);
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->url($storagePath);
        }

        return null;
    }

    protected function legacyStoragePath(string $normalized): ?string
    {
        $base = trim(config('assist.site_media.path', 'assist/site'), '/');
        if (str_starts_with($normalized, $base.'/')) {
            return $normalized;
        }
        if (str_starts_with($normalized, 'assist/site/')) {
            return $normalized;
        }

        return null;
    }

    public function storeSectionImage(UploadedFile $file, string $pageSlug, string $sectionKey): string
    {
        return $this->storePublicImage($file, $pageSlug, $sectionKey);
    }

    public function storePageImage(UploadedFile $file, string $pageSlug, string $field): string
    {
        return $this->storePublicImage($file, $pageSlug, $field);
    }

    protected function storePublicImage(UploadedFile $file, string $pageSlug, string $name): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new RuntimeException('Only JPG, PNG, or WebP images are allowed.');
        }

        $relativeDir = $this->pagePath($pageSlug);
        $absoluteDir = $this->publicRoot().'/'.$pageSlug;

        if (! is_dir($absoluteDir)) {
            File::makeDirectory($absoluteDir, 0755, true);
        }

        $filename = $name.'-'.time().'.'.$ext;
        $file->move($absoluteDir, $filename);

        return $relativeDir.'/'.$filename;
    }

    public function deleteIfStored(?string $path): void
    {
        if (empty($path) || str_starts_with($path, 'http')) {
            return;
        }

        $normalized = ltrim($path, '/');
        $publicFile = $this->publicPathForDbPath($normalized);
        if (is_file($publicFile)) {
            unlink($publicFile);
        }

        $storagePath = $this->legacyStoragePath($normalized);
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }
    }

    /**
     * Copy files from storage/app/public/assist/site to public/assist/site.
     *
     * @return int Number of files copied
     */
    public function publishFromStorage(): int
    {
        $storageBase = storage_path('app/public/'.$this->publicSubdir());
        if (! is_dir($storageBase)) {
            return 0;
        }

        $copied = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($storageBase, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $fullPath = $file->getPathname();
            $relative = $this->publicSubdir().'/'.substr($fullPath, strlen($storageBase) + 1);
            $dest = $this->webPublicBase().'/'.$relative;

            if (is_file($dest)) {
                continue;
            }

            $destDir = dirname($dest);
            if (! is_dir($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            if (copy($fullPath, $dest)) {
                $copied++;
            }
        }

        return $copied;
    }
}
