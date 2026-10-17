<?php

namespace App\Services;

use App\Models\EarningsEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EarningsService
{
    /**
     * Compute financial breakdown for shift values with decimal safety.
     *
     * @param float|int|string $gross
     * @param float|int|string $incentives
     * @param float|int|string $fuel
     * @param float|int|string $other
     * @param float|int|string $deductions
     * @param float|int|string $hours
     * @return array{total_income: float, total_expenses: float, net_earnings: float, effective_hourly_earnings: float}
     */
    public function computeBreakdown($gross, $incentives = 0, $fuel = 0, $other = 0, $deductions = 0, $hours = 0): array
    {
        $grossVal = max(0.00, round((float) $gross, 2));
        $incVal = max(0.00, round((float) $incentives, 2));
        $fuelVal = max(0.00, round((float) $fuel, 2));
        $otherVal = max(0.00, round((float) $other, 2));
        $dedVal = max(0.00, round((float) $deductions, 2));
        $hoursVal = max(0.00, round((float) $hours, 2));

        $totalIncome = round($grossVal + $incVal, 2);
        $totalExpenses = round($fuelVal + $otherVal + $dedVal, 2);
        $netEarnings = round($totalIncome - $totalExpenses, 2);

        $hourly = 0.00;
        if ($hoursVal > 0) {
            $hourly = round($netEarnings / $hoursVal, 2);
        }

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_earnings' => $netEarnings,
            'effective_hourly_earnings' => $hourly,
        ];
    }

    /**
     * Get aggregate dashboard metrics for a user.
     */
    public function getDashboardMetrics(User $user): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        // Optimized single query to fetch user entries with specific columns
        $entries = $user->earningsEntries()
            ->select([
                'id',
                'user_id',
                'work_date',
                'gross_earnings',
                'incentives',
                'fuel_expenses',
                'other_expenses',
                'platform_deductions',
                'hours_worked',
                'notes',
                'created_at',
            ])
            ->get();

        // Today's metrics
        $todayEntries = $entries->filter(fn($e) => $e->work_date && $e->work_date->isSameDay($today));
        $todayNet = round($todayEntries->sum(fn($e) => $e->net_earnings), 2);
        $todayHours = round($todayEntries->sum('hours_worked'), 2);

        // Weekly metrics
        $weeklyEntries = $entries->filter(fn($e) => $e->work_date && $e->work_date->gte($startOfWeek));
        $weeklyNet = round($weeklyEntries->sum(fn($e) => $e->net_earnings), 2);
        $weeklyHours = round($weeklyEntries->sum('hours_worked'), 2);
        $weeklyHourly = $weeklyHours > 0 ? round($weeklyNet / $weeklyHours, 2) : 0.00;

        // Lifetime metrics
        $totalHours = round($entries->sum('hours_worked'), 2);
        $allTimeNet = round($entries->sum(fn($e) => $e->net_earnings), 2);
        $allTimeHourly = $totalHours > 0 ? round($allTimeNet / $totalHours, 2) : 0.00;

        // Complaint stats
        $savedComplaintsCount = $user->complaints()->count();
        $pendingComplaintsCount = $user->complaints()->where('status', 'draft')->count();

        // Recent items
        $recentEarnings = $entries->take(5);
        $recentComplaints = $user->complaints()->latest()->take(5)->get();

        return [
            'todayNet' => $todayNet,
            'todayHours' => $todayHours,
            'weeklyNet' => $weeklyNet,
            'weeklyHours' => $weeklyHours,
            'weeklyHourly' => $weeklyHourly,
            'totalHours' => $totalHours,
            'allTimeHourly' => $allTimeHourly,
            'savedComplaintsCount' => $savedComplaintsCount,
            'pendingComplaintsCount' => $pendingComplaintsCount,
            'recentEarnings' => $recentEarnings,
            'recentComplaints' => $recentComplaints,
        ];
    }
}
