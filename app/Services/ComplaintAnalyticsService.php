<?php

namespace App\Services;

use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComplaintAnalyticsService
{
    /**
     * Compute comprehensive complaint trends and metrics with applied filters.
     */
    public function getComplaintAnalytics(array $filters): array
    {
        $query = DB::table('complaints')
            ->where('is_demo', true);

        // Apply filters
        if (!empty($filters['company']) && $filters['company'] !== 'All') {
            $query->where('company_name', $filters['company']);
        }
        if (!empty($filters['category']) && $filters['category'] !== 'All') {
            $query->where('issue_category', $filters['category']);
        }
        if (!empty($filters['city']) && $filters['city'] !== 'All') {
            $query->where('city', $filters['city']);
        }
        if (!empty($filters['role']) && $filters['role'] !== 'All') {
            $query->where('worker_role', $filters['role']);
        }
        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $query->where('status', $filters['status']);
        }

        $allComplaints = $query->get();
        $totalCount = $allComplaints->count();

        // Status breakdown
        $resolvedCount = $allComplaints->where('status', 'resolved')->count();
        $submittedCount = $allComplaints->where('status', 'submitted')->count();
        $draftCount = $allComplaints->where('status', 'draft')->count();
        $cancelledCount = $allComplaints->where('status', 'cancelled')->count();

        $resolutionRate = $totalCount > 0 ? round(($resolvedCount / $totalCount) * 100, 1) : 0.0;

        // Resolution times
        $resolvedTimes = $allComplaints->where('status', 'resolved')
            ->pluck('resolution_time_hours')
            ->filter(fn($v) => $v !== null && $v > 0)
            ->map(fn($v) => (int) $v)
            ->sort()
            ->values();

        $medianResolutionHours = 0;
        if ($resolvedTimes->isNotEmpty()) {
            $mid = (int) floor($resolvedTimes->count() / 2);
            $medianResolutionHours = $resolvedTimes[$mid];
        }

        // Categories Ranking (Horizontal Bar Chart)
        $categoryCounts = $allComplaints->groupBy('issue_category')
            ->map(fn($group) => $group->count())
            ->sortDesc();

        $categoryLabels = $categoryCounts->keys()->toArray();
        $categoryValues = $categoryCounts->values()->toArray();

        // Status Distribution (Donut Chart)
        $statusDonut = [
            'labels' => ['Resolved', 'Open / Submitted', 'Drafts', 'Cancelled'],
            'data' => [$resolvedCount, $submittedCount, $draftCount, $cancelledCount],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
        ];

        // Monthly / Bi-weekly trend over time (Line Chart)
        $monthlyTrend = DB::table('complaints')
            ->where('is_demo', true)
            ->selectRaw('DATE_FORMAT(incident_date, "%b %Y") as period_label, incident_date, COUNT(*) as volume')
            ->groupBy('period_label', 'incident_date')
            ->orderBy('incident_date')
            ->get()
            ->groupBy('period_label')
            ->map(fn($items) => $items->sum('volume'));

        // Company Comparison (Complaint count & resolved rate)
        $companyComparison = DB::table('complaints')
            ->where('is_demo', true)
            ->selectRaw('
                company_name,
                COUNT(*) as total,
                SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved
            ')
            ->groupBy('company_name')
            ->orderByDesc('total')
            ->get();

        // Period-over-Period trend insight
        $now = Carbon::now();
        $recent30Days = $allComplaints->filter(function ($c) use ($now) {
            return Carbon::parse($c->incident_date)->gte($now->copy()->subDays(30));
        })->count();

        $prev30Days = $allComplaints->filter(function ($c) use ($now) {
            $d = Carbon::parse($c->incident_date);
            return $d->gte($now->copy()->subDays(60)) && $d->lt($now->copy()->subDays(30));
        })->count();

        $volumeChangePct = 0;
        if ($prev30Days > 0) {
            $volumeChangePct = round((($recent30Days - $prev30Days) / $prev30Days) * 100, 1);
        }

        $topIssue = $categoryLabels[0] ?? 'Incentives not credited';
        $topIssuePercent = $totalCount > 0 ? round(($categoryValues[0] / $totalCount) * 100, 1) : 0;

        // Contextual AI-style analytical insights
        $insights = [
            "{$topIssue} is the most frequently reported grievance, accounting for {$topIssuePercent}% of all recorded platform disputes.",
            $volumeChangePct >= 0
                ? "Grievance filing volume increased by {$volumeChangePct}% in the last 30-day period compared with the prior 30 days."
                : "Grievance filing volume decreased by " . abs($volumeChangePct) . "% in the last 30-day period.",
            "Across the network, the median resolution time stands at {$medianResolutionHours} hours, with {$resolutionRate}% of disputes reaching a resolved outcome.",
        ];

        return [
            'total_complaints' => $totalCount,
            'resolved_count' => $resolvedCount,
            'submitted_count' => $submittedCount,
            'resolution_rate' => $resolutionRate,
            'median_resolution_hours' => $medianResolutionHours,
            'insights' => $insights,
            'category_chart' => [
                'labels' => $categoryLabels,
                'data' => $categoryValues,
            ],
            'status_chart' => $statusDonut,
            'trend_chart' => [
                'labels' => $monthlyTrend->keys()->toArray(),
                'data' => $monthlyTrend->values()->toArray(),
            ],
            'company_chart' => [
                'labels' => $companyComparison->pluck('company_name')->toArray(),
                'total' => $companyComparison->pluck('total')->toArray(),
                'resolved' => $companyComparison->pluck('resolved')->toArray(),
            ],
        ];
    }
}
