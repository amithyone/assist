<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SitePage extends Model
{
    public const SLUGS = [
        'home',
        'pricing',
        'docs',
        'login',
        'register',
        'forgot_password',
        'reset_password',
    ];

    protected $fillable = [
        'slug',
        'name',
        'is_published',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'robots',
        'schema_json',
        'intro',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'schema_json' => 'array',
        'intro' => 'array',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(SitePageSection::class)->orderBy('sort_order');
    }

    public function sectionByKey(string $key): ?SitePageSection
    {
        return $this->sections->firstWhere('section_key', $key);
    }
}
