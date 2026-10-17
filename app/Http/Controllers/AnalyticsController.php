<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EarningsGoal;
use App\Services\AnalyticsService;
use App\Services\BenchmarkService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected BenchmarkService $benchmarkService
    ) {}

    /**
     * Display the Gigy 2.0 Interactive Analytics Dashboard.
     */
    public function index(Request $request): View
    {
        $range = $request->query('range', '30d');
        $customFrom = $request->query('from');
        $customTo = $request->query('to');
        $city = $request->query('city', 'All');
        $company = $request->query('company', 'All');
        $role = $request->query('role', 'All');

        $dateInfo = $this->analyticsService->resolveDateRange($range, $customFrom, $customTo);

        $user = $request->user();

        // 1. KPI Cards
        $kpis = $this->analyticsService->computeWorkerKpis($user, $dateInfo);

        // 2. Earnings Over Time Line Chart
        $overTimeSeries = $this->analyticsService->getEarningsOverTimeSeries($user, $dateInfo);

        // 3. Donut Breakdowns (Income & Expenses)
        $breakdowns = $this->analyticsService->getIncomeAndExpenseBreakdown($user, $dateInfo);

        // 4. Hourly Earnings Heatmap
        $heatmap = $this->analyticsService->getHourlyHeatmapData($city, $role);

        // 5. Delivery Efficiency
        $efficiency = $this->analyticsService->getDeliveryEfficiency($user, $dateInfo);

        // 6. Peer Benchmark Summary
        $benchmarkSummary = $this->benchmarkService->computeBenchmark($user, $city, $company, $role);

        // 7. Active Goal
        $activeGoal = $user->earningsGoals()->where('status', 'active')->latest()->first();

        // Available filter choices
        $companies = Company::orderBy('name')->get();
        $cities = ['All', 'Bengaluru', 'Delhi NCR', 'Mumbai', 'Hyderabad'];
        $roles = [
            'All',
            'Food Delivery Partner',
            'Quick-Commerce Courier',
            'Courier & Parcel Rider',
            'Ride-hail Driver',
        ];

        return view('analytics.index', [
            'kpis' => $kpis,
            'overTimeSeries' => $overTimeSeries,
            'breakdowns' => $breakdowns,
            'heatmap' => $heatmap,
            'efficiency' => $efficiency,
            'benchmark' => $benchmarkSummary,
            'activeGoal' => $activeGoal,
            'filters' => [
                'range' => $range,
                'from' => $customFrom,
                'to' => $customTo,
                'city' => $city,
                'company' => $company,
                'role' => $role,
                'label' => $dateInfo['label'],
            ],
            'companies' => $companies,
            'cities' => $cities,
            'roles' => $roles,
        ]);
    }

    /**
     * Export aggregate performance report to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $range = $request->query('range', '30d');
        $dateInfo = $this->analyticsService->resolveDateRange($range);
        $user = $request->user();

        $entries = $user->earningsEntries()
            ->whereBetween('work_date', [$dateInfo['startDate']->toDateString(), $dateInfo['endDate']->toDateString()])
            ->orderBy('work_date')
            ->get();

        $filename = "gigy_analytics_export_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($entries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Work Date', 'Gross Earnings (INR)', 'Incentives (INR)', 'Fuel Expenses (INR)', 'Platform Deductions (INR)', 'Other Expenses (INR)', 'Net Take-Home (INR)', 'Hours Worked', 'Hourly Rate (INR/hr)', 'Deliveries Completed']);

            foreach ($entries as $e) {
                fputcsv($handle, [
                    $e->work_date->toDateString(),
                    $e->gross_earnings,
                    $e->incentives,
                    $e->fuel_expenses,
                    $e->platform_deductions,
                    $e->other_expenses,
                    $e->net_earnings,
                    $e->hours_worked,
                    $e->effective_hourly_earnings,
                    $e->deliveries_count,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
