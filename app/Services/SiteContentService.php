<?php

namespace App\Services;

use App\Models\SitePage;
use Illuminate\Support\Facades\Cache;

class SiteContentService
{
    public function __construct(
        protected SiteMediaService $media
    ) {}

    public function clearCache(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget($this->cacheKey($slug));

            return;
        }
        foreach (SitePage::SLUGS as $s) {
            Cache::forget($this->cacheKey($s));
        }
    }

    protected function cacheKey(string $slug): string
    {
        return 'assist.site_page.'.$slug;
    }

    /**
     * @return array{page: SitePage|null, sections: array<string, array<string, mixed>>, seo: array<string, mixed>, intro: array<string, mixed>}
     */
    public function forSlug(string $slug): array
    {
        return Cache::remember($this->cacheKey($slug), 3600, function () use ($slug) {
            $page = SitePage::with('sections')
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first();

            if (! $page) {
                return [
                    'page' => null,
                    'sections' => [],
                    'seo' => $this->defaultSeo($slug),
                    'intro' => [],
                ];
            }

            $sections = [];
            foreach ($page->sections as $section) {
                $sections[$section->section_key] = $this->sectionPayload($section);
            }

            return [
                'page' => $page,
                'sections' => $sections,
                'seo' => $this->seoPayload($page),
                'intro' => $page->intro ?? [],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function sectionPayload($section): array
    {
        $content = $section->content ?? [];

        $imageUrl = null;
        if ($section->image_path && $this->media->fileExists($section->image_path)) {
            $imageUrl = $this->media->imageUrl($section->image_path);
        }

        return array_merge($content, [
            'image_url' => $imageUrl,
            'image_alt' => $section->image_alt,
            'image_path' => $section->image_path,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function seoPayload(SitePage $page): array
    {
        $siteName = config('assist.site_name', 'Assist');
        $defaultImage = $this->resolveSeoImage(config('assist.default_og_image', 'assist/assist-logo.png'));
        $ogImage = $this->resolveSeoImage($page->og_image) ?: $defaultImage;
        $twitterImage = $this->resolveSeoImage($page->twitter_image) ?: $ogImage;
        $canonical = $page->canonical_url ?: $this->canonicalForSlug($page->slug);
        $title = $page->meta_title ?: ($page->name.' — '.$siteName);
        $description = $page->meta_description ?: '';

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $page->meta_keywords,
            'canonical' => $canonical,
            'robots' => $page->robots ?: 'index,follow',
            'og_title' => $page->og_title ?: $title,
            'og_description' => $page->og_description ?: $description,
            'og_image' => $ogImage,
            'og_type' => $page->og_type ?: 'website',
            'og_url' => $canonical,
            'twitter_card' => $page->twitter_card ?: 'summary_large_image',
            'twitter_title' => $page->twitter_title ?: ($page->og_title ?: $title),
            'twitter_description' => $page->twitter_description ?: ($page->og_description ?: $description),
            'twitter_image' => $twitterImage,
            'schema_json' => $page->schema_json,
            'site_name' => $siteName,
            'slug' => $page->slug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultSeo(string $slug): array
    {
        $siteName = config('assist.site_name', 'Assist');
        $defaults = [
            'home' => [
                'title' => 'Assist — AI Editor for DaVinci Resolve',
                'description' => 'Music Video Cuts, beat-synced montages, and dialogue assemblies inside DaVinci Resolve. Love shooting. Enjoy the edit again.',
            ],
            'pricing' => [
                'title' => 'Pricing — Assist',
                'description' => 'Simple plans for Assist desktop and DaVinci Resolve workflows.',
            ],
            'docs' => [
                'title' => 'Documentation — Assist',
                'description' => 'Install Assist, connect Resolve, and use Music Video Cuts, Reels Cloner, Beat Edit, and more.',
            ],
            'privacy' => [
                'title' => 'Privacy Policy — Assist',
                'description' => 'How Amithyone Media collects, uses, and protects your data when you use Assist and this website.',
            ],
            'terms' => [
                'title' => 'Terms of Use — Assist',
                'description' => 'Terms and conditions for using Assist desktop, this website, and related services.',
            ],
            'login' => [
                'title' => 'Log in — Assist',
                'description' => 'Sign in to your Assist account.',
            ],
            'register' => [
                'title' => 'Register — Assist',
                'description' => 'Create your Assist account.',
            ],
            'forgot_password' => [
                'title' => 'Forgot password — Assist',
                'description' => 'Reset your Assist account password.',
            ],
            'reset_password' => [
                'title' => 'Reset password — Assist',
                'description' => 'Choose a new password for your Assist account.',
            ],
        ];
        $d = $defaults[$slug] ?? ['title' => $siteName, 'description' => ''];
        $canonical = $this->canonicalForSlug($slug);
        $image = $this->resolveSeoImage(config('assist.default_og_image', 'assist/assist-logo.png'));

        return [
            'title' => $d['title'],
            'description' => $d['description'],
            'keywords' => null,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'og_title' => $d['title'],
            'og_description' => $d['description'],
            'og_image' => $image,
            'og_type' => 'website',
            'og_url' => $canonical,
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $d['title'],
            'twitter_description' => $d['description'],
            'twitter_image' => $image,
            'schema_json' => null,
            'site_name' => $siteName,
            'slug' => $slug,
        ];
    }

    protected function resolveSeoImage(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if ($this->media->fileExists($path)) {
            return $this->media->imageUrl($path);
        }

        return $this->media->imageUrl(config('assist.default_og_image', 'assist/assist-logo.png'));
    }

    public function canonicalForSlug(string $slug): string
    {
        return match ($slug) {
            'home' => url('/'),
            'pricing' => route('assist.pricing'),
            'docs' => route('assist.docs'),
            'privacy' => route('assist.privacy'),
            'terms' => route('assist.terms'),
            'login' => route('login'),
            'register' => route('assist.register'),
            'forgot_password' => route('assist.password.request'),
            'reset_password' => url('/reset-password'),
            default => url('/'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function homeViewData(): array
    {
        $data = $this->forSlug('home');

        return [
            'page' => $data['page'],
            'sections' => $data['sections'],
            'seo' => $data['seo'],
            'intro' => $data['intro'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function pageViewData(string $slug): array
    {
        return $this->forSlug($slug);
    }
}
