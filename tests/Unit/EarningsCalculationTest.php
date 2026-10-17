<?php

namespace Tests\Unit;

use App\Services\EarningsService;
use PHPUnit\Framework\TestCase;

class EarningsCalculationTest extends TestCase
{
    private EarningsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EarningsService();
    }

    public function test_standard_shift_breakdown_calculation(): void
    {
        $breakdown = $this->service->computeBreakdown(
            gross: 1200.50,
            incentives: 150.00,
            fuel: 200.00,
            other: 25.00,
            deductions: 50.25,
            hours: 8.5
        );

        // gross (1200.50) + incentives (150.00) = 1350.50
        $this->assertEquals(1350.50, $breakdown['total_income']);

        // deductions (50.25) + fuel (200.00) + other (25.00) = 275.25
        $this->assertEquals(275.25, $breakdown['total_expenses']);

        // net = 1350.50 - 275.25 = 1075.25
        $this->assertEquals(1075.25, $breakdown['net_earnings']);

        // hourly = 1075.25 / 8.5 = 126.50
        $this->assertEquals(126.50, $breakdown['effective_hourly_earnings']);
    }

    public function test_zero_hours_worked_does_not_throw_division_by_zero(): void
    {
        $breakdown = $this->service->computeBreakdown(
            gross: 500.00,
            incentives: 50.00,
            fuel: 50.00,
            other: 0.00,
            deductions: 20.00,
            hours: 0.0
        );

        $this->assertEquals(550.00, $breakdown['total_income']);
        $this->assertEquals(70.00, $breakdown['total_expenses']);
        $this->assertEquals(480.00, $breakdown['net_earnings']);
        $this->assertEquals(0.00, $breakdown['effective_hourly_earnings']);
    }

    public function test_zero_expenses_and_zero_incentives(): void
    {
        $breakdown = $this->service->computeBreakdown(
            gross: 800.00,
            incentives: 0.00,
            fuel: 0.00,
            other: 0.00,
            deductions: 0.00,
            hours: 5.0
        );

        $this->assertEquals(800.00, $breakdown['total_income']);
        $this->assertEquals(0.00, $breakdown['total_expenses']);
        $this->assertEquals(800.00, $breakdown['net_earnings']);
        $this->assertEquals(160.00, $breakdown['effective_hourly_earnings']);
    }

    public function test_net_loss_scenario_where_expenses_exceed_earnings(): void
    {
        $breakdown = $this->service->computeBreakdown(
            gross: 200.00,
            incentives: 0.00,
            fuel: 300.00,
            other: 100.00,
            deductions: 50.00,
            hours: 6.0
        );

        $this->assertEquals(200.00, $breakdown['total_income']);
        $this->assertEquals(450.00, $breakdown['total_expenses']);
        // net = 200 - 450 = -250.00
        $this->assertEquals(-250.00, $breakdown['net_earnings']);
        // hourly = -250 / 6 = -41.67
        $this->assertEquals(-41.67, $breakdown['effective_hourly_earnings']);
    }

    public function test_fractional_hours_precision(): void
    {
        $breakdown = $this->service->computeBreakdown(
            gross: 1050.00,
            incentives: 0.00,
            fuel: 0.00,
            other: 0.00,
            deductions: 0.00,
            hours: 3.5
        );

        // 1050 / 3.5 = 300.00
        $this->assertEquals(300.00, $breakdown['effective_hourly_earnings']);
    }
}
