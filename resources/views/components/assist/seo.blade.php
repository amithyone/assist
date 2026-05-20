@props(['seo' => []])

@php
    $seo = is_array($seo) ? $seo : [];
    $title = $seo['title'] ?? (config('assist.site_name', 'Assist').' — AI Editor for DaVinci Resolve');
    $description = $seo['description'] ?? '';
    $canonical = $seo['canonical'] ?? url()->current();
    $robots = $seo['robots'] ?? 'index,follow';
    $ogTitle = $seo['og_title'] ?? $title;
    $ogDescription = $seo['og_description'] ?? $description;
    $ogImage = $seo['og_image'] ?? asset(config('assist.default_og_image', 'assist/assist-logo.png'));
    $ogType = $seo['og_type'] ?? 'website';
    $ogUrl = $seo['og_url'] ?? $canonical;
    $siteName = $seo['site_name'] ?? config('assist.site_name', 'Assist');
    $twitterCard = $seo['twitter_card'] ?? 'summary_large_image';
    $twitterTitle = $seo['twitter_title'] ?? $ogTitle;
    $twitterDescription = $seo['twitter_description'] ?? $ogDescription;
    $twitterImage = $seo['twitter_image'] ?? $ogImage;
    $slug = $seo['slug'] ?? 'home';
    $keywords = $seo['keywords'] ?? null;
    $twitterHandle = config('assist.site_twitter_handle', '');

    $schemaBlocks = [];
    if (! empty($seo['schema_json']) && is_array($seo['schema_json'])) {
        $schemaBlocks = [$seo['schema_json']];
    } else {
        $schemaBlocks[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'description' => $description,
        ];
        $schemaBlocks[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $ogTitle,
            'description' => $ogDescription,
            'url' => $canonical,
            'isPartOf' => ['@type' => 'WebSite', 'name' => $siteName, 'url' => url('/')],
        ];
        if ($slug === 'home') {
            $schemaBlocks[] = [
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => $siteName,
                'applicationCategory' => 'MultimediaApplication',
                'operatingSystem' => 'macOS',
                'description' => $description,
                'url' => url('/'),
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
            ];
        }
    }
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if ($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="icon" href="{{ asset('assist/assist-logo.png') }}" type="image/png">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:secure_url" content="{{ $ogImage }}">
<meta property="og:image:alt" content="{{ $ogTitle }}">

<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
<meta name="twitter:image" content="{{ $twitterImage }}">
@if ($twitterHandle)
    <meta name="twitter:site" content="{{ $twitterHandle }}">
@endif

@foreach ($schemaBlocks as $block)
    <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
