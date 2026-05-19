<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageEvent extends Model
{
    protected $fillable = [
        'user_id', 'client_event_id', 'feature', 'event', 'status', 'units',
        'project_type', 'app_version', 'resolve_project_name',
        'metrics', 'content_summary', 'details', 'occurred_at',
    ];

    protected $casts = [
        'metrics' => 'array',
        'content_summary' => 'array',
        'details' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
