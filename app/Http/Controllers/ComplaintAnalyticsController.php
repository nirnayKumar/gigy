<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Complaint;
use App\Services\ComplaintAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintAnalyticsController extends Controller
{
    public function __construct(
        protected ComplaintAnalyticsService $complaintAnalyticsService
    ) {}

    public function index(Request $request): View
    {
        $company = $request->query('company', 'All');
        $category = $request->query('category', 'All');
        $status = $request->query('status', 'All');
        $city = $request->query('city', 'All');
        $role = $request->query('role', 'All');
        $search = $request->query('q');

        $filters = [
            'company' => $company,
            'category' => $category,
            'status' => $status,
            'city' => $city,
            'role' => $role,
        ];

        $analytics = $this->complaintAnalyticsService->getComplaintAnalytics($filters);

        // Paginated complaint explorer
        $explorerQuery = Complaint::where('is_demo', true);

        if ($company !== 'All') {
            $explorerQuery->where('company_name', $company);
        }
        if ($category !== 'All') {
            $explorerQuery->where('issue_category', $category);
        }
        if ($status !== 'All') {
            $explorerQuery->where('status', $status);
        }
        if ($city !== 'All') {
            $explorerQuery->where('city', $city);
        }
        if ($role !== 'All') {
            $explorerQuery->where('worker_role', $role);
        }
        if (!empty($search)) {
            $explorerQuery->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('issue_category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $complaints = $explorerQuery->orderByDesc('incident_date')->paginate(15)->withQueryString();

        $companies = Company::orderBy('name')->get();
        $categories = [
            'All',
            'Incentives not credited',
            'Unfair penalties',
            'Payment delays',
            'Incorrect earnings calculations',
            'App or technical issues',
            'Support response delays',
            'Customer disputes',
            'Account suspension',
            'Safety concerns',
        ];

        return view('analytics.complaints', [
            'analytics' => $analytics,
            'complaints' => $complaints,
            'filters' => $filters,
            'search' => $search,
            'companies' => $companies,
            'categories' => $categories,
        ]);
    }
}
