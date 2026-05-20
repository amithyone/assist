<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /assist/setup',
            'Disallow: /dashboard',
            'Disallow: /billing/',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
