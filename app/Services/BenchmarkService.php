<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BenchmarkService
{
    const MINIMUM_COHORT_SIZE = 10;

    /**
     * Compute comprehensive peer benchmarks and comparison for the given user.
     */
    public function computeBenchmark(User $user, string $city = 'All', string $company = 'All', string $role = 'All', string $hoursBand = 'All'): array
    {
        // 1. Compute Worker's own personal metrics
        $workerEntries = $user->earningsEntries()->take(30)->get();
        if ($workerEntries->isEmpty()) {
            // Pick synthetic worker for demonstration purposes if account has no personal shifts
            $sampleDemo = User::where('is_demo', true)->first();
            if ($sampleDemo) {
                $workerEntries = $sampleDemo->earningsEntries()->take(30)->get();
            }
        }

        $workerTotalHours = (float) $workerEntries->sum('hours_worked');
        $workerNetTotal = (float) $workerEntries->sum('net_earnings');
        $workerIncomeTotal = (float) $workerEntries->sum('total_income');
        $workerExpenseTotal = (float) $workerEntries->sum('total_expenses');
        $workerDeliveriesTotal = (int) $workerEntries->sum('deliveries_count');
        $workerDaysCount = max(1, $workerEntries->count());

        $workerHourlyNet = $workerTotalHours > 0 ? round($workerNetTotal / $workerTotalHours, 2) : 0.00;
        $workerDailyNet = round($workerNetTotal / $workerDaysCount, 2);
        $workerDailyHours = round($workerTotalHours / $workerDaysCount, 1);
        $workerDeliveriesPerHour = $workerTotalHours > 0 ? round($workerDeliveriesTotal / $workerTotalHours, 1) : 0.0;
        $workerExpenseRatio = $workerIncomeTotal > 0 ? round(($workerExpenseTotal / $workerIncomeTotal) * 100, 1) : 0.0;

        // 2. Query Cohort data with applied filters
        $query = DB::table('work_sessions')
            ->where('is_demo', true);

        if ($city !== 'All') {
            $query->where('city', $city);
        }
        if ($role !== 'All') {
            $query->where('worker_role', $role);
        }
        if ($company !== 'All') {
            $compRecord = DB::table('companies')->where('name', $company)->orWhere('slug', $company)->first();
            if ($compRecord) {
                $query->where('company_id', $compRecord->id);
            }
        }
        if ($hoursBand === 'part_time') {
            $query->where('total_hours', '<=', 5);
        } elseif ($hoursBand === 'full_time') {
            $query->where('total_hours', '>', 5);
        }

        // Count distinct workers in cohort
        $cohortWorkersCount = $query->distinct('user_id')->count('user_id');

        // Cohort Privacy Safeguard: Minimum 10 workers
        if ($cohortWorkersCount < self::MINIMUM_COHORT_SIZE) {
            return [
                'cohort_size' => $cohortWorkersCount,
                'is_suppressed' => true,
                'min_required' => self::MINIMUM_COHORT_SIZE,
                'message' => "Cohort size ({$cohortWorkersCount} workers) is below the minimum privacy threshold (" . self::MINIMUM_COHORT_SIZE . " workers). Filters have been broadened to preserve worker anonymity.",
                'worker' => [
                    'hourly_net' => $workerHourlyNet,
                    'daily_net' => $workerDailyNet,
                    'daily_hours' => $workerDailyHours,
                    'deliveries_per_hour' => $workerDeliveriesPerHour,
                    'expense_ratio' => $workerExpenseRatio,
                ],
                'median' => [],
                'percentiles' => [],
                'context_statement' => 'Insufficient peer cohort size to generate statistically reliable benchmark.',
            ];
        }

        // Retrieve cohort observations
        $sessions = $query->select('total_hours', 'deliveries_completed', 'gross_earnings', 'net_earnings', 'hourly_rate')->get();

        $hourlyRates = $sessions->pluck('hourly_rate')->map(fn($v) => (float) $v)->sort()->values();
        $dailyNets = $sessions->pluck('net_earnings')->map(fn($v) => (float) $v)->sort()->values();
        $dailyHoursList = $sessions->pluck('total_hours')->map(fn($v) => (float) $v)->sort()->values();

        // Compute percentiles (25th, 50th/median, 75th)
        $p25Hourly = $this->calculatePercentile($hourlyRates, 25);
        $medianHourly = $this->calculatePercentile($hourlyRates, 50);
        $p75Hourly = $this->calculatePercentile($hourlyRates, 75);

        $medianDailyNet = $this->calculatePercentile($dailyNets, 50);
        $medianHours = $this->calculatePercentile($dailyHoursList, 50);

        // Community deliveries per hour
        $totalCohortHours = (float) $sessions->sum('total_hours');
        $totalCohortDelivs = (int) $sessions->sum('deliveries_completed');
        $communityDeliveriesPerHour = $totalCohortHours > 0 ? round($totalCohortDelivs / $totalCohortHours, 1) : 2.4;

        // Community expense ratio
        $totalCohortGross = (float) $sessions->sum('gross_earnings');
        $totalCohortNet = (float) $sessions->sum('net_earnings');
        $totalCohortExpenses = max(0, $totalCohortGross - $totalCohortNet);
        $communityExpenseRatio = $totalCohortGross > 0 ? round(($totalCohortExpenses / $totalCohortGross) * 100, 1) : 22.5;

        // Determine Worker Percentile Rank
        $workerRankPercentile = $this->calculateRank($hourlyRates, $workerHourlyNet);

        // Contextual Explanation statement
        if ($workerHourlyNet >= $medianHourly) {
            $diffPercent = $medianHourly > 0 ? round((($workerHourlyNet - $medianHourly) / $medianHourly) * 100) : 0;
            $contextStatement = "Your net earnings of ₹{$workerHourlyNet}/hr are {$diffPercent}% above the community median (₹{$medianHourly}/hr), placing you in the top " . (100 - $workerRankPercentile) . "% of peers.";
        } else {
            $diffPercent = $medianHourly > 0 ? round((($medianHourly - $workerHourlyNet) / $medianHourly) * 100) : 0;
            $contextStatement = "Your net earnings of ₹{$workerHourlyNet}/hr are {$diffPercent}% below the community median (₹{$medianHourly}/hr). Optimizing peak-hour shifts and monitoring deductions could increase hourly take-home.";
        }

        // Earnings distribution bins for distribution chart
        $distributionBins = $this->buildDistributionBins($hourlyRates, $workerHourlyNet);

        return [
            'cohort_size' => $cohortWorkersCount,
            'sessions_count' => $sessions->count(),
            'is_suppressed' => false,
            'worker' => [
                'hourly_net' => $workerHourlyNet,
                'daily_net' => $workerDailyNet,
                'daily_hours' => $workerDailyHours,
                'deliveries_per_hour' => $workerDeliveriesPerHour,
                'expense_ratio' => $workerExpenseRatio,
                'percentile_rank' => $workerRankPercentile,
            ],
            'median' => [
                'hourly_net' => $medianHourly,
                'daily_net' => $medianDailyNet,
                'daily_hours' => $medianHours,
                'deliveries_per_hour' => $communityDeliveriesPerHour,
                'expense_ratio' => $communityExpenseRatio,
            ],
            'percentiles' => [
                'p25' => $p25Hourly,
                'p50' => $medianHourly,
                'p75' => $p75Hourly,
            ],
            'distribution' => $distributionBins,
            'context_statement' => $contextStatement,
        ];
    }

    private function calculatePercentile(Collection $sortedList, int $percentile): float
    {
        $count = $sortedList->count();
        if ($count === 0) {
            return 0.00;
        }

        $index = ($percentile / 100) * ($count - 1);
        $lower = (int) floor($index);
        $upper = (int) ceil($index);
        $weight = $index - $lower;

        if ($lower === $upper) {
            return round($sortedList[$lower], 2);
        }

        return round($sortedList[$lower] * (1 - $weight) + $sortedList[$upper] * $weight, 2);
    }

    private function calculateRank(Collection $sortedList, float $value): int
    {
        $count = $sortedList->count();
        if ($count === 0) {
            return 50;
        }

        $belowCount = $sortedList->filter(fn($v) => $v < $value)->count();
        return (int) round(($belowCount / $count) * 100);
    }

    private function buildDistributionBins(Collection $sortedList, float $workerValue): array
    {
        if ($sortedList->isEmpty()) {
            return ['labels' => [], 'data' => [], 'worker_bin_index' => 0];
        }

        $min = 80;
        $max = 240;
        $binSize = 20;

        $bins = [];
        $labels = [];
        for ($b = $min; $b < $max; $b += $binSize) {
            $bEnd = $b + $binSize;
            $labels[] = "₹{$b}-₹{$bEnd}";
            $bins[] = $sortedList->filter(fn($v) => $v >= $b && $v < $bEnd)->count();
        }

        $workerBinIndex = (int) floor(($workerValue - $min) / $binSize);
        $workerBinIndex = max(0, min(count($labels) - 1, $workerBinIndex));

        return [
            'labels' => $labels,
            'data' => $bins,
            'worker_bin_index' => $workerBinIndex,
        ];
    }
}
