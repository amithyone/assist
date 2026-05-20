<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitePageSection extends Model
{
    protected $fillable = [
        'site_page_id',
        'section_key',
        'sort_order',
        'content',
        'image_path',
        'image_alt',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(SitePage::class, 'site_page_id');
    }
}
