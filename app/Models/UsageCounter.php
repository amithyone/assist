<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCounter extends Model
{
    public const COUNTABLE_FEATURES = [
        'timelines',
        'transcribe_clips',
        'reel_clones',
        'beat_edits',
        'music_video_cuts',
        'ai_edits',
        'preproduction',
    ];

    protected $fillable = [
        'user_id',
        'period',
        'timelines',
        'transcribe_clips',
        'reel_clones',
        'beat_edits',
        'music_video_cuts',
        'ai_edits',
        'preproduction',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCountFor(string $feature): int
    {
        return (int) ($this->{$feature} ?? 0);
    }

    public function incrementFeature(string $feature, int $units = 1): void
    {
        if (! in_array($feature, self::COUNTABLE_FEATURES, true)) {
            return;
        }
        $this->increment($feature, $units);
    }
}
