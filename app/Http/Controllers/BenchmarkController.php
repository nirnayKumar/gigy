<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\BenchmarkService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BenchmarkController extends Controller
{
    public function __construct(
        protected BenchmarkService $benchmarkService
    ) {}

    public function index(Request $request): View
    {
        $city = $request->query('city', 'All');
        $company = $request->query('company', 'All');
        $role = $request->query('role', 'All');
        $hoursBand = $request->query('hours_band', 'All');

        $user = $request->user();

        $benchmarkData = $this->benchmarkService->computeBenchmark($user, $city, $company, $role, $hoursBand);

        $companies = Company::orderBy('name')->get();
        $cities = ['All', 'Bengaluru', 'Delhi NCR', 'Mumbai', 'Hyderabad'];
        $roles = [
            'All',
            'Food Delivery Partner',
            'Quick-Commerce Courier',
            'Courier & Parcel Rider',
            'Ride-hail Driver',
        ];

        return view('analytics.benchmark', [
            'benchmark' => $benchmarkData,
            'filters' => [
                'city' => $city,
                'company' => $company,
                'role' => $role,
                'hours_band' => $hoursBand,
            ],
            'companies' => $companies,
            'cities' => $cities,
            'roles' => $roles,
        ]);
    }
}
