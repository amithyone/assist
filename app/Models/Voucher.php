<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    public const DISCOUNT_PERCENT = 'percent';

    public const DISCOUNT_FIXED_NGN = 'fixed_ngn';

    public const DISCOUNT_FIXED_USD = 'fixed_usd';

    protected $fillable = [
        'code',
        'label',
        'discount_type',
        'discount_value',
        'plan_slug',
        'max_redemptions',
        'redemption_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function discountLabel(): string
    {
        return match ($this->discount_type) {
            self::DISCOUNT_PERCENT => rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.').'% off',
            self::DISCOUNT_FIXED_NGN => '₦'.number_format((float) $this->discount_value, 0).' off',
            self::DISCOUNT_FIXED_USD => '$'.number_format((float) $this->discount_value, 2).' off',
            default => (string) $this->discount_value,
        };
    }
}
