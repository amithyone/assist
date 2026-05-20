<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'plan_slug',
        'gateway',
        'transaction_id',
        'external_reference',
        'amount',
        'currency',
        'status',
        'checkout_payload',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'checkout_payload' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): ?Plan
    {
        return Plan::where('slug', $this->plan_slug)->first();
    }
}
