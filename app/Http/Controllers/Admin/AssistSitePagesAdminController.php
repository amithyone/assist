<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use App\Models\SitePageSection;
use App\Services\SiteContentService;
use App\Services\SiteMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistSitePagesAdminController extends Controller
{
    public function __construct(
        protected SiteContentService $content,
        protected SiteMediaService $media
    ) {}

    public function index(): View
    {
        $pages = SitePage::orderBy('slug')->get();

        return view('admin.assist-site-pages.index', compact('pages'));
    }

    public function edit(SitePage $sitePage): View
    {
        $sitePage->load('sections');
        $previewUrl = $this->previewUrl($sitePage->slug);
        $media = $this->media;

        return view('admin.assist-site-pages.edit', compact('sitePage', 'previewUrl', 'media'));
    }

    public function update(Request $request, SitePage $sitePage): RedirectResponse
    {
        $data = $request->validate([
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:120',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:120',
            'og_description' => 'nullable|string|max:320',
            'og_type' => 'nullable|string|max:32',
            'twitter_card' => 'nullable|string|max:32',
            'twitter_title' => 'nullable|string|max:120',
            'twitter_description' => 'nullable|string|max:320',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:64',
            'intro_eyebrow' => 'nullable|string|max:120',
            'intro_heading' => 'nullable|string|max:255',
            'intro_subheading' => 'nullable|string|max:500',
            'intro_body_html' => 'nullable|string|max:5000',
            'og_image' => 'nullable|image|max:'.((int) config('assist.site_media.max_upload_kb', 5120)),
            'twitter_image' => 'nullable|image|max:'.((int) config('assist.site_media.max_upload_kb', 5120)),
            'remove_og_image' => 'boolean',
            'remove_twitter_image' => 'boolean',
            'sections' => 'nullable|array',
            'section_images' => 'nullable|array',
            'section_image_alts' => 'nullable|array',
            'remove_section_images' => 'nullable|array',
        ]);

        $intro = array_filter([
            'eyebrow' => $data['intro_eyebrow'] ?? null,
            'heading' => $data['intro_heading'] ?? null,
            'subheading' => $data['intro_subheading'] ?? null,
            'body_html' => $data['intro_body_html'] ?? null,
        ]);

        $sitePage->fill([
            'is_published' => $request->boolean('is_published'),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_type' => $data['og_type'] ?? 'website',
            'twitter_card' => $data['twitter_card'] ?? 'summary_large_image',
            'twitter_title' => $data['twitter_title'] ?? null,
            'twitter_description' => $data['twitter_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'robots' => $data['robots'] ?? 'index,follow',
            'intro' => $intro ?: null,
        ]);

        if ($request->boolean('remove_og_image') && $sitePage->og_image) {
            $this->media->deleteIfStored($sitePage->og_image);
            $sitePage->og_image = null;
        }
        if ($request->hasFile('og_image')) {
            $this->media->deleteIfStored($sitePage->og_image);
            $sitePage->og_image = $this->media->storePageImage($request->file('og_image'), $sitePage->slug, 'og');
        }

        if ($request->boolean('remove_twitter_image') && $sitePage->twitter_image) {
            $this->media->deleteIfStored($sitePage->twitter_image);
            $sitePage->twitter_image = null;
        }
        if ($request->hasFile('twitter_image')) {
            $this->media->deleteIfStored($sitePage->twitter_image);
            $sitePage->twitter_image = $this->media->storePageImage($request->file('twitter_image'), $sitePage->slug, 'twitter');
        }

        $sitePage->save();

        if ($sitePage->slug === 'home' && is_array($request->input('sections'))) {
            $this->updateHomeSections($request, $sitePage);
        }

        $this->content->clearCache($sitePage->slug);

        return redirect()
            ->route('admin.assist.site-pages.edit', $sitePage)
            ->with('status', 'Page content and SEO saved.');
    }

    protected function updateHomeSections(Request $request, SitePage $sitePage): void
    {
        $sectionsInput = $request->input('sections', []);
        $alts = $request->input('section_image_alts', []);
        $remove = $request->input('remove_section_images', []);

        foreach ($sectionsInput as $key => $fields) {
            if (! is_array($fields)) {
                continue;
            }
            $section = SitePageSection::firstOrCreate(
                ['site_page_id' => $sitePage->id, 'section_key' => $key],
                ['sort_order' => 0, 'content' => []]
            );

            $content = $section->content ?? [];
            foreach ($fields as $field => $value) {
                if ($field === 'pills' || $field === 'bullets' || $field === 'items' || $field === 'project_types') {
                    $lines = is_string($value) ? preg_split('/\r\n|\r|\n/', $value) : [];
                    $content[$field] = array_values(array_filter(array_map('trim', $lines)));
                } else {
                    $content[$field] = $value;
                }
            }
            $section->content = $content;

            if (isset($alts[$key])) {
                $section->image_alt = $alts[$key];
            }

            if (! empty($remove[$key]) && $section->image_path) {
                $this->media->deleteIfStored($section->image_path);
                $section->image_path = null;
            }

            if ($request->hasFile("section_images.{$key}")) {
                $this->media->deleteIfStored($section->image_path);
                $section->image_path = $this->media->storeSectionImage(
                    $request->file("section_images.{$key}"),
                    $sitePage->slug,
                    $key
                );
            }

            $section->save();
        }
    }

    protected function previewUrl(string $slug): string
    {
        return match ($slug) {
            'home' => route('assist.home'),
            'pricing' => route('assist.pricing'),
            'docs' => route('assist.docs'),
            'login' => route('login'),
            'register' => route('assist.register'),
            'forgot_password' => route('assist.password.request'),
            'reset_password' => url('/reset-password'),
            default => route('assist.home'),
        };
    }
}
