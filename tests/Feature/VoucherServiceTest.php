<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_percent_voucher_reduces_price(): void
    {
        $plan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'limits' => ['reel_clones' => 10],
            'is_active' => true,
            'price_ngn' => 10000,
            'price_usd' => 10,
            'usage_period' => 'monthly',
            'sort_order' => 1,
        ]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'voucher-test@example.com',
            'password' => 'password',
        ]);
        $voucher = Voucher::create([
            'code' => 'SAVE20',
            'discount_type' => Voucher::DISCOUNT_PERCENT,
            'discount_value' => 20,
            'is_active' => true,
        ]);

        $service = app(VoucherService::class);
        $found = $service->findValid('save20', $user, $plan, 'ngn');
        $breakdown = $service->priceBreakdown(10000, $found, 'ngn');

        $this->assertSame(8000.0, $breakdown['final']);
        $this->assertSame(2000.0, $breakdown['discount']);
    }

    public function test_plan_restricted_voucher_rejects_wrong_plan(): void
    {
        $pro = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'limits' => [],
            'is_active' => true,
            'price_ngn' => 5000,
            'price_usd' => 5,
            'usage_period' => 'monthly',
            'sort_order' => 1,
        ]);
        Plan::create([
            'slug' => 'unlimited',
            'name' => 'Unlimited',
            'limits' => [],
            'is_active' => true,
            'price_ngn' => 30000,
            'price_usd' => 30,
            'usage_period' => 'monthly',
            'sort_order' => 2,
        ]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'voucher-test@example.com',
            'password' => 'password',
        ]);
        Voucher::create([
            'code' => 'PROONLY',
            'discount_type' => Voucher::DISCOUNT_PERCENT,
            'discount_value' => 10,
            'plan_slug' => 'pro',
            'is_active' => true,
        ]);

        $service = app(VoucherService::class);
        $unlimited = Plan::where('slug', 'unlimited')->first();

        $this->expectException(RuntimeException::class);
        $service->findValid('PROONLY', $user, $unlimited, 'ngn');
    }
}
