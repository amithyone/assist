<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $routes = [
            ['loc' => url('/'), 'slug' => 'home', 'priority' => '1.0'],
            ['loc' => route('assist.pricing'), 'slug' => 'pricing', 'priority' => '0.9'],
            ['loc' => route('assist.docs'), 'slug' => 'docs', 'priority' => '0.8'],
            ['loc' => route('assist.privacy'), 'slug' => 'privacy', 'priority' => '0.5'],
            ['loc' => route('assist.terms'), 'slug' => 'terms', 'priority' => '0.5'],
        ];

        $pages = SitePage::whereIn('slug', array_column($routes, 'slug'))->get()->keyBy('slug');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($routes as $route) {
            $page = $pages->get($route['slug']);
            $lastmod = $page?->updated_at?->toAtomString() ?? now()->toAtomString();
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($route['loc']).'</loc>'."\n";
            $xml .= '    <lastmod>'.$lastmod.'</lastmod>'."\n";
            $xml .= '    <changefreq>weekly</changefreq>'."\n";
            $xml .= '    <priority>'.$route['priority'].'</priority>'."\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
