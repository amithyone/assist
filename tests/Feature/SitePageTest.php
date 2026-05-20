<?php

namespace Tests\Feature;

use App\Models\SitePage;
use App\Models\User;
use Database\Seeders\SitePageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_seeded_hero_heading(): void
    {
        $this->seed(SitePageSeeder::class);

        $response = $this->get(route('assist.home'));

        $response->assertOk();
        $response->assertSee('Love shooting. Enjoy the edit again.', false);
    }

    public function test_sitemap_lists_public_urls(): void
    {
        $this->seed(SitePageSeeder::class);

        $response = $this->get(route('assist.sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $this->assertStringContainsString(route('assist.pricing'), $response->getContent());
    }

    public function test_robots_includes_sitemap(): void
    {
        $response = $this->get(route('assist.robots'));

        $response->assertOk();
        $this->assertStringContainsString('Sitemap:', $response->getContent());
    }

    public function test_admin_can_update_page_seo(): void
    {
        $this->seed(SitePageSeeder::class);
        $admin = User::factory()->create(['is_admin' => true]);
        $page = SitePage::where('slug', 'pricing')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.assist.site-pages.update', $page), [
            'is_published' => true,
            'meta_title' => 'Custom Pricing Title',
            'meta_description' => 'Custom pricing description for tests.',
            'robots' => 'index,follow',
        ]);

        $response->assertRedirect(route('admin.assist.site-pages.edit', $page));
        $this->assertDatabaseHas('site_pages', [
            'id' => $page->id,
            'meta_title' => 'Custom Pricing Title',
        ]);
    }
}
