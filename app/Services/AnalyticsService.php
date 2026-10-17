<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\EarningsEntry;
use App\Models\EarningsGoal;
use App\Models\User;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Resolve date range from request filter.
     *
     * @return array{startDate: Carbon, endDate: Carbon, prevStartDate: Carbon, prevEndDate: Carbon, label: string}
     */
    public function resolveDateRange(string $range = '30d', ?string $customFrom = null, ?string $customTo = null): array
    {
        $now = Carbon::now();

        if ($range === 'today') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $prevStartDate = $startDate->copy()->subDay();
            $prevEndDate = $endDate->copy()->subDay();
            $label = 'Today';
        } elseif ($range === '7d') {
            $startDate = $now->copy()->subDays(6)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $prevStartDate = $startDate->copy()->subDays(7);
            $prevEndDate = $startDate->copy()->subSecond();
            $label = 'Last 7 Days';
        } elseif ($range === '90d') {
            $startDate = $now->copy()->subDays(89)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $prevStartDate = $startDate->copy()->subDays(90);
            $prevEndDate = $startDate->copy()->subSecond();
            $label = 'Last 90 Days';
        } elseif ($range === 'custom' && $customFrom && $customTo) {
            $startDate = Carbon::parse($customFrom)->startOfDay();
            $endDate = Carbon::parse($customTo)->endOfDay();
            $diffDays = max(1, $startDate->diffInDays($endDate) + 1);
            $prevStartDate = $startDate->copy()->subDays($diffDays);
            $prevEndDate = $startDate->copy()->subSecond();
            $label = "Custom ({$startDate->format('d M')} - {$endDate->format('d M')})";
        } else { // default '30d'
            $startDate = $now->copy()->subDays(29)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $prevStartDate = $startDate->copy()->subDays(30);
            $prevEndDate = $startDate->copy()->subSecond();
            $label = 'Last 30 Days';
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'prevStartDate' => $prevStartDate,
            'prevEndDate' => $prevEndDate,
            'label' => $label,
        ];
    }

    /**
     * Compute Top-Level KPIs for the authenticated worker.
     */
    public function computeWorkerKpis(User $user, array $dateInfo): array
    {
        $start = $dateInfo['startDate'];
        $end = $dateInfo['endDate'];
        $prevStart = $dateInfo['prevStartDate'];
        $prevEnd = $dateInfo['prevEndDate'];

        // If the user has few personal records (new user), fall back safely to their records or provide contextual demo metrics notice
        $currentEntries = $user->earningsEntries()
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $prevEntries = $user->earningsEntries()
            ->whereBetween('work_date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->get();

        // If user has zero personal entries in range, check if they have any entries at all or are evaluating in demo mode
        $isUsingDemoFallback = false;
        if ($currentEntries->isEmpty() && $user->earningsEntries()->count() === 0) {
            // Pick a representative synthetic profile for visualization demonstration
            $demoWorker = User::where('is_demo', true)->first();
            if ($demoWorker) {
                $currentEntries = $demoWorker->earningsEntries()
                    ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                    ->get();
                $prevEntries = $demoWorker->earningsEntries()
                    ->whereBetween('work_date', [$prevStart->toDateString(), $prevEnd->toDateString()])
                    ->get();
                $isUsingDemoFallback = true;
            }
        }

        $totalGross = (float) $currentEntries->sum('gross_earnings');
        $totalIncentives = (float) $currentEntries->sum('incentives');
        $totalFuel = (float) $currentEntries->sum('fuel_expenses');
        $totalOther = (float) $currentEntries->sum('other_expenses');
        $totalDeductions = (float) $currentEntries->sum('platform_deductions');
        $totalHours = (float) $currentEntries->sum('hours_worked');
        $totalDeliveries = (int) $currentEntries->sum('deliveries_count');

        $totalIncome = round($totalGross + $totalIncentives, 2);
        $totalExpenses = round($totalFuel + $totalOther + $totalDeductions, 2);
        $netEarnings = round($totalIncome - $totalExpenses, 2);
        $hourlyNet = $totalHours > 0 ? round($netEarnings / $totalHours, 2) : 0.00;
        $earningsPerDelivery = $totalDeliveries > 0 ? round($netEarnings / $totalDeliveries, 2) : 0.00;
        $expenseRatio = $totalIncome > 0 ? round(($totalExpenses / $totalIncome) * 100, 1) : 0.0;

        // Previous period comparisons
        $prevGross = (float) $prevEntries->sum('gross_earnings');
        $prevIncentives = (float) $prevEntries->sum('incentives');
        $prevIncome = $prevGross + $prevIncentives;
        $prevExpenses = (float) $prevEntries->sum('fuel_expenses') + (float) $prevEntries->sum('other_expenses') + (float) $prevEntries->sum('platform_deductions');
        $prevNet = round($prevIncome - $prevExpenses, 2);

        $netChangePercent = 0.0;
        if ($prevNet > 0) {
            $netChangePercent = round((($netEarnings - $prevNet) / $prevNet) * 100, 1);
        }

        // Today's metrics specifically
        $todayEntry = $currentEntries->firstWhere('work_date', Carbon::today());
        $todayGross = $todayEntry ? (float) ($todayEntry->gross_earnings + $todayEntry->incentives) : 0.00;
        $todayNet = $todayEntry ? (float) $todayEntry->net_earnings : 0.00;
        $todayDeliveries = $todayEntry ? (int) $todayEntry->deliveries_count : 0;

        return [
            'total_income' => $totalIncome,
            'net_earnings' => $netEarnings,
            'total_hours' => $totalHours,
            'total_deliveries' => $totalDeliveries,
            'hourly_net' => $hourlyNet,
            'earnings_per_delivery' => $earningsPerDelivery,
            'total_incentives' => $totalIncentives,
            'total_expenses' => $totalExpenses,
            'expense_ratio' => $expenseRatio,
            'net_change_percent' => $netChangePercent,
            'today_gross' => $todayGross,
            'today_net' => $todayNet,
            'today_deliveries' => $todayDeliveries,
            'is_using_demo_fallback' => $isUsingDemoFallback,
            'entries_count' => $currentEntries->count(),
        ];
    }

    /**
     * Generate daily series for Earnings Over Time line chart.
     */
    public function getEarningsOverTimeSeries(User $user, array $dateInfo): array
    {
        $start = $dateInfo['startDate']->copy()->startOfDay();
        $end = $dateInfo['endDate']->copy()->endOfDay();

        $entries = $user->earningsEntries()
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(function ($e) {
                return $e->work_date->toDateString();
            });

        // If new worker with no entries, load sample demo worker for display
        if ($entries->isEmpty() && $user->earningsEntries()->count() === 0) {
            $demoWorker = User::where('is_demo', true)->first();
            if ($demoWorker) {
                $entries = $demoWorker->earningsEntries()
                    ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                    ->get()
                    ->keyBy(function ($e) {
                        return $e->work_date->toDateString();
                    });
            }
        }

        // Also query community average for each day to provide the comparison series!
        $communityDailyAvg = DB::table('earnings_entries')
            ->where('is_demo', true)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('work_date, ROUND(AVG(gross_earnings + incentives - fuel_expenses - other_expenses - platform_deductions), 2) as avg_net')
            ->groupBy('work_date')
            ->pluck('avg_net', 'work_date');

        $labels = [];
        $grossSeries = [];
        $expenseSeries = [];
        $netSeries = [];
        $peerBenchmarkSeries = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $labels[] = $current->format('d M');

            if ($entries->has($dateStr)) {
                $e = $entries->get($dateStr);
                $income = (float) ($e->gross_earnings + $e->incentives);
                $expenses = (float) ($e->fuel_expenses + $e->other_expenses + $e->platform_deductions);
                $net = (float) $e->net_earnings;
            } else {
                $income = 0.00;
                $expenses = 0.00;
                $net = 0.00;
            }

            $grossSeries[] = $income;
            $expenseSeries[] = $expenses;
            $netSeries[] = $net;
            $peerBenchmarkSeries[] = (float) ($communityDailyAvg->get($dateStr) ?? 0.00);

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'gross' => $grossSeries,
            'expenses' => $expenseSeries,
            'net' => $netSeries,
            'peer_benchmark' => $peerBenchmarkSeries,
        ];
    }

    /**
     * Income breakdown and expense breakdown for donut charts.
     */
    public function getIncomeAndExpenseBreakdown(User $user, array $dateInfo): array
    {
        $start = $dateInfo['startDate'];
        $end = $dateInfo['endDate'];

        $entries = $user->earningsEntries()
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        if ($entries->isEmpty() && $user->earningsEntries()->count() === 0) {
            $demoWorker = User::where('is_demo', true)->first();
            if ($demoWorker) {
                $entries = $demoWorker->earningsEntries()
                    ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                    ->get();
            }
        }

        $base = (float) $entries->sum('gross_earnings');
        $incentives = (float) $entries->sum('incentives');

        $fuel = (float) $entries->sum('fuel_expenses');
        $deductions = (float) $entries->sum('platform_deductions');
        $other = (float) $entries->sum('other_expenses');

        return [
            'income' => [
                'labels' => ['Base Pay', 'Incentives & Bonuses'],
                'data' => [$base, $incentives],
                'colors' => ['#059669', '#10b981'],
            ],
            'expenses' => [
                'labels' => ['Fuel Expenses', 'Platform Deductions', 'Maintenance / Other'],
                'data' => [$fuel, $deductions, $other],
                'colors' => ['#ef4444', '#f59e0b', '#6b7280'],
            ],
        ];
    }

    /**
     * Hourly earnings heatmap: day-of-week (0=Sun..6=Sat) vs hour-of-day (0..23).
     */
    public function getHourlyHeatmapData(string $city = 'All', string $role = 'All'): array
    {
        $query = DB::table('work_sessions')
            ->selectRaw('
                DAYOFWEEK(start_time) - 1 as day_of_week,
                HOUR(start_time) as hour_of_day,
                COUNT(*) as session_count,
                ROUND(AVG(hourly_rate), 2) as avg_hourly_rate
            ');

        if ($city !== 'All') {
            $query->where('city', $city);
        }
        if ($role !== 'All') {
            $query->where('worker_role', $role);
        }

        $records = $query->groupBy('day_of_week', 'hour_of_day')->get();

        // Construct 7 x 24 matrix
        $matrix = [];
        for ($d = 0; $d < 7; $d++) {
            $matrix[$d] = array_fill(0, 24, ['rate' => 0.00, 'count' => 0]);
        }

        foreach ($records as $r) {
            $d = (int) $r->day_of_week;
            $h = (int) $r->hour_of_day;
            if ($d >= 0 && $d < 7 && $h >= 0 && $h < 24) {
                $matrix[$d][$h] = [
                    'rate' => (float) $r->avg_hourly_rate,
                    'count' => (int) $r->session_count,
                ];
            }
        }

        return [
            'days' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'hours' => range(0, 23),
            'matrix' => $matrix,
        ];
    }

    /**
     * Delivery Efficiency KPIs from delivery-level records.
     */
    public function getDeliveryEfficiency(User $user, array $dateInfo): array
    {
        $start = $dateInfo['startDate'];
        $end = $dateInfo['endDate'];

        $deliveries = $user->deliveries()
            ->whereBetween('pickup_time', [$start, $end])
            ->get();

        $isDemoFallback = false;
        if ($deliveries->isEmpty() && $user->deliveries()->count() === 0) {
            $demoWorker = User::where('is_demo', true)->first();
            if ($demoWorker) {
                $deliveries = $demoWorker->deliveries()
                    ->whereBetween('pickup_time', [$start, $end])
                    ->get();
                $isDemoFallback = true;
            }
        }

        $totalCount = $deliveries->count();
        if ($totalCount === 0) {
            return [
                'has_data' => false,
                'deliveries_per_hour' => 0.0,
                'avg_time_minutes' => 0,
                'avg_earnings_per_delivery' => 0.00,
                'completion_rate' => 0.0,
                'cancellation_rate' => 0.0,
                'is_demo_fallback' => $isDemoFallback,
            ];
        }

        $completed = $deliveries->where('status', 'completed');
        $completedCount = $completed->count();
        $cancelledCount = $totalCount - $completedCount;

        $completionRate = round(($completedCount / $totalCount) * 100, 1);
        $cancellationRate = round(($cancelledCount / $totalCount) * 100, 1);
        $avgDuration = (int) round($completed->avg('duration_minutes') ?? 22);
        $avgEarnings = round((float) ($completed->avg('earnings') ?? 0.00), 2);

        // Approximate deliveries per hour: 60 / avgDuration
        $deliveriesPerHour = $avgDuration > 0 ? round(60 / $avgDuration, 1) : 2.5;

        return [
            'has_data' => true,
            'total_recorded' => $totalCount,
            'completed_count' => $completedCount,
            'deliveries_per_hour' => $deliveriesPerHour,
            'avg_time_minutes' => $avgDuration,
            'avg_earnings_per_delivery' => $avgEarnings,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'is_demo_fallback' => $isDemoFallback,
        ];
    }
}
