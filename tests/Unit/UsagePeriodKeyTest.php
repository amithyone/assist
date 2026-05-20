<?php

namespace Tests\Unit;

use App\Models\Plan;
use PHPUnit\Framework\TestCase;

class UsagePeriodKeyTest extends TestCase
{
    public function test_weekly_period_format_length(): void
    {
        $key = now()->format('o-\WW');
        $this->assertLessThanOrEqual(10, strlen($key));
        $this->assertMatchesRegularExpression('/^\d{4}-W\d{2}$/', $key);
    }

    public function test_free_plan_is_weekly(): void
    {
        $plan = new Plan(['usage_period' => 'weekly']);
        $this->assertSame('weekly', $plan->usage_period);
    }
}
