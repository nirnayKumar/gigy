<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        // All entries for this user
        $entries = $user->earningsEntries()->get();

        // Today's entry/entries
        $todayEntries = $entries->filter(function ($e) use ($today) {
            return $e->work_date && $e->work_date->isSameDay($today);
        });

        $todayGross = $todayEntries->sum('gross_earnings');
        $todayIncentives = $todayEntries->sum('incentives');
        $todayExpenses = $todayEntries->sum(function ($e) {
            return $e->total_expenses;
        });
        $todayNet = $todayEntries->sum(function ($e) {
            return $e->net_earnings;
        });
        $todayHours = $todayEntries->sum('hours_worked');

        // Weekly entries
        $weeklyEntries = $entries->filter(function ($e) use ($startOfWeek) {
            return $e->work_date && $e->work_date->gte($startOfWeek);
        });

        $weeklyNet = $weeklyEntries->sum(function ($e) {
            return $e->net_earnings;
        });
        $weeklyHours = $weeklyEntries->sum('hours_worked');
        $weeklyHourly = $weeklyHours > 0 ? round($weeklyNet / $weeklyHours, 2) : 0.00;

        // All time hours & complaints
        $totalHours = $entries->sum('hours_worked');
        $allTimeNet = $entries->sum(function ($e) {
            return $e->net_earnings;
        });
        $allTimeHourly = $totalHours > 0 ? round($allTimeNet / $totalHours, 2) : 0.00;

        $savedComplaintsCount = $user->complaints()->count();
        $pendingComplaintsCount = $user->complaints()->where('status', 'draft')->count();

        // Recent earnings & complaints
        $recentEarnings = $user->earningsEntries()->take(5)->get();
        $recentComplaints = $user->complaints()->take(5)->get();

        return view('dashboard', [
            'user' => $user,
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
        ]);
    }
}
