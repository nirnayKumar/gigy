<?php

namespace App\Http\Controllers;

use App\Services\PlatformInsightsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformInsightsController extends Controller
{
    public function __construct(
        protected PlatformInsightsService $platformInsightsService
    ) {}

    public function index(Request $request): View
    {
        $sortBy = $request->query('sort', 'net_per_hour');
        $sortDir = $request->query('dir', 'desc');

        $insights = $this->platformInsightsService->getCompanyComparisonMetrics($sortBy, $sortDir);

        return view('analytics.platforms', [
            'insights' => $insights,
            'currentSort' => $sortBy,
            'currentDir' => $sortDir,
        ]);
    }
}
