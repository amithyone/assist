<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = ['slug', 'name', 'limits', 'is_active'];

    protected $casts = [
        'limits' => 'array',
        'is_active' => 'boolean',
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
}
