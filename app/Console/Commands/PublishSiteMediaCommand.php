<?php

namespace App\Console\Commands;

use App\Services\SiteContentService;
use App\Services\SiteMediaService;
use Illuminate\Console\Command;

class PublishSiteMediaCommand extends Command
{
    protected $signature = 'assist:publish-site-media';

    protected $description = 'Copy CMS images from storage/app/public/assist/site to public/assist/site (Hostinger-safe)';

    public function handle(SiteMediaService $media, SiteContentService $content): int
    {
        $copied = $media->publishFromStorage();

        $content->clearCache();

        $this->info("Published {$copied} file(s) to public/{$media->publicSubdir()}/");

        return self::SUCCESS;
    }
}
