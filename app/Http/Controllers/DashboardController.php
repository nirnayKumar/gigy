<?php

namespace App\Http\Controllers;

use App\Services\EarningsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected EarningsService $earningsService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $metrics = $this->earningsService->getDashboardMetrics($user);

        return view('dashboard', array_merge(['user' => $user], $metrics));
    }
}
