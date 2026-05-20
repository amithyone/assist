<?php

namespace App\Models;

use App\Models\Concerns\HasAssistPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasAssistPlan, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'youtube',
        'instagram',
        'marketing_opt_in',
        'is_admin',
        'billing_currency',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'marketing_opt_in' => 'boolean',
        ];
    }
}
