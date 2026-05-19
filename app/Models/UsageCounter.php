<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCounter extends Model
{
    protected $fillable = [
        'user_id', 'period',
        'timelines', 'transcribe_clips', 'reel_clones', 'beat_edits',
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
        if (! in_array($feature, ['timelines', 'transcribe_clips', 'reel_clones', 'beat_edits'], true)) {
            return;
        }
        $this->increment($feature, $units);
    }
}
