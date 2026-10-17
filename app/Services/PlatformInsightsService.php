<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlatformInsightsService
{
    /**
     * Aggregate comparison metrics across all companies.
     */
    public function getCompanyComparisonMetrics(string $sortBy = 'net_per_hour', string $sortDir = 'desc'): array
    {
        $companies = Company::where('is_demo', true)->get();

        $companyMetrics = [];

        foreach ($companies as $comp) {
            // Aggregate work sessions for this company
            $sessions = DB::table('work_sessions')
                ->where('company_id', $comp->id)
                ->where('is_demo', true)
                ->selectRaw('
                    COUNT(DISTINCT user_id) as worker_count,
                    COUNT(*) as total_sessions,
                    AVG(total_hours) as avg_hours,
                    AVG(gross_earnings) as avg_gross,
                    AVG(net_earnings) as avg_net,
                    AVG(hourly_rate) as avg_hourly_rate,
                    SUM(deliveries_completed) as total_deliveries
                ')
                ->first();

            // Median calculation
            $hourlyRates = DB::table('work_sessions')
                ->where('company_id', $comp->id)
                ->where('is_demo', true)
                ->pluck('hourly_rate')
                ->map(fn($v) => (float) $v)
                ->sort()
                ->values();

            $medianNetPerHour = $this->calculateMedian($hourlyRates);

            $workerCount = (int) ($sessions->worker_count ?? 0);
            $totalDeliveries = (int) ($sessions->total_deliveries ?? 0);
            $avgHours = round((float) ($sessions->avg_hours ?? 0), 1);
            $avgGross = round((float) ($sessions->avg_gross ?? 0), 2);
            $avgNet = round((float) ($sessions->avg_net ?? 0), 2);

            // Compute expense ratio
            $avgExpenses = max(0, $avgGross - $avgNet);
            $expenseRatio = $avgGross > 0 ? round(($avgExpenses / $avgGross) * 100, 1) : 0.0;

            // Complaint statistics for this company
            $complaints = DB::table('complaints')
                ->where('company_name', $comp->name)
                ->where('is_demo', true)
                ->selectRaw('
                    COUNT(*) as total_complaints,
                    SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved_count,
                    AVG(resolution_time_hours) as avg_resolution_hours
                ')
                ->first();

            $totalComplaints = (int) ($complaints->total_complaints ?? 0);
            $resolvedComplaints = (int) ($complaints->resolved_count ?? 0);
            $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100, 1) : 0.0;
            $avgResolutionHours = round((float) ($complaints->avg_resolution_hours ?? 0), 1);

            // Complaint rate per 1,000 deliveries
            $complaintRatePer1000 = $totalDeliveries > 0
                ? round(($totalComplaints / $totalDeliveries) * 1000, 2)
                : 0.00;

            $companyMetrics[] = [
                'id' => $comp->id,
                'name' => $comp->name,
                'slug' => $comp->slug,
                'category' => $comp->category,
                'color' => $comp->color,
                'worker_count' => $workerCount,
                'total_deliveries' => $totalDeliveries,
                'avg_daily_hours' => $avgHours,
                'avg_daily_net' => $avgNet,
                'median_net_per_hour' => $medianNetPerHour,
                'expense_ratio' => $expenseRatio,
                'total_complaints' => $totalComplaints,
                'complaint_rate_per_1000' => $complaintRatePer1000,
                'resolution_rate' => $resolutionRate,
                'avg_resolution_hours' => $avgResolutionHours,
            ];
        }

        // Sorting
        usort($companyMetrics, function ($a, $b) use ($sortBy, $sortDir) {
            $valA = match ($sortBy) {
                'deliveries' => $a['total_deliveries'],
                'expense_ratio' => $a['expense_ratio'],
                'resolution_rate' => $a['resolution_rate'],
                'complaint_rate' => $a['complaint_rate_per_1000'],
                'daily_net' => $a['avg_daily_net'],
                default => $a['median_net_per_hour'],
            };
            $valB = match ($sortBy) {
                'deliveries' => $b['total_deliveries'],
                'expense_ratio' => $b['expense_ratio'],
                'resolution_rate' => $b['resolution_rate'],
                'complaint_rate' => $b['complaint_rate_per_1000'],
                'daily_net' => $b['avg_daily_net'],
                default => $b['median_net_per_hour'],
            };

            return ($sortDir === 'asc') ? ($valA <=> $valB) : ($valB <=> $valA);
        });

        // Determine highlights
        $highestNet = collect($companyMetrics)->sortByDesc('median_net_per_hour')->first();
        $lowestExpense = collect($companyMetrics)->sortBy('expense_ratio')->first();
        $bestResolution = collect($companyMetrics)->sortByDesc('resolution_rate')->first();

        return [
            'companies' => $companyMetrics,
            'highlights' => [
                'highest_net' => $highestNet ? $highestNet['name'] : 'N/A',
                'highest_net_val' => $highestNet ? '₹' . number_format($highestNet['median_net_per_hour'], 2) . '/hr' : '',
                'lowest_expense' => $lowestExpense ? $lowestExpense['name'] : 'N/A',
                'lowest_expense_val' => $lowestExpense ? $lowestExpense['expense_ratio'] . '%' : '',
                'best_resolution' => $bestResolution ? $bestResolution['name'] : 'N/A',
                'best_resolution_val' => $bestResolution ? $bestResolution['resolution_rate'] . '%' : '',
            ],
            'chart_data' => [
                'labels' => array_column($companyMetrics, 'name'),
                'hourly_rates' => array_column($companyMetrics, 'median_net_per_hour'),
                'expense_ratios' => array_column($companyMetrics, 'expense_ratio'),
                'resolution_rates' => array_column($companyMetrics, 'resolution_rate'),
                'colors' => array_column($companyMetrics, 'color'),
            ],
        ];
    }

    private function calculateMedian(Collection $sortedList): float
    {
        $count = $sortedList->count();
        if ($count === 0) {
            return 0.00;
        }

        $middle = (int) floor($count / 2);
        if ($count % 2 === 0) {
            return round(($sortedList[$middle - 1] + $sortedList[$middle]) / 2, 2);
        }

        return round($sortedList[$middle], 2);
    }
}
