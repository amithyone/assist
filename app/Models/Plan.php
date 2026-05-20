<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    public const FEATURES = [
        'preproduction',
        'reel_clones',
        'beat_edits',
        'music_video_cuts',
        'ai_edits',
        'timelines',
        'transcribe_clips',
    ];

    protected $fillable = [
        'slug',
        'name',
        'limits',
        'is_active',
        'price_ngn',
        'price_usd',
        'usage_period',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'limits' => 'array',
        'is_active' => 'boolean',
        'price_usd' => 'decimal:2',
    ];

    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    public function limitFor(string $feature): ?int
    {
        $limits = $this->limits ?? [];
        if (! array_key_exists($feature, $limits)) {
            return null;
        }
        $val = $limits[$feature];

        return $val === null ? null : (int) $val;
    }

    public function priceForCurrency(string $currency): float
    {
        $currency = strtolower($currency);
        if ($currency === 'usd') {
            return (float) ($this->price_usd ?? 0);
        }

        return (float) ($this->price_ngn ?? 0);
    }

    /**
     * @return list<string>
     */
    public function marketingFeatures(): array
    {
        $lines = [];
        $limits = $this->limits ?? [];
        $period = $this->usage_period === 'weekly' ? 'per week' : 'per month';

        foreach (self::FEATURES as $feature) {
            if (! array_key_exists($feature, $limits)) {
                continue;
            }
            $limit = $limits[$feature];
            $label = str_replace('_', ' ', ucwords($feature, '_'));
            if ($limit === null) {
                $lines[] = "Unlimited {$label}";
            } else {
                $lines[] = "{$limit} {$label} {$period}";
            }
        }

        if (empty($lines)) {
            $lines[] = 'All features included';
        }

        return $lines;
    }
}
