<?php

namespace App\Http\Controllers;

use App\Models\EarningsGoal;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:daily,weekly,monthly',
            'target_amount' => 'required|numeric|min:100|max:500000',
            'notes' => 'nullable|string|max:255',
        ]);

        $now = Carbon::now();

        if ($validated['type'] === 'daily') {
            $startDate = $now->copy()->startOfDay()->toDateString();
            $endDate = $now->copy()->endOfDay()->toDateString();
        } elseif ($validated['type'] === 'weekly') {
            $startDate = $now->copy()->startOfWeek()->toDateString();
            $endDate = $now->copy()->endOfWeek()->toDateString();
        } else {
            $startDate = $now->copy()->startOfMonth()->toDateString();
            $endDate = $now->copy()->endOfMonth()->toDateString();
        }

        // Archive previous active goals of this type
        $request->user()->earningsGoals()
            ->where('type', $validated['type'])
            ->where('status', 'active')
            ->update(['status' => 'archived']);

        $request->user()->earningsGoals()->create([
            'type' => $validated['type'],
            'target_amount' => $validated['target_amount'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Earnings goal successfully created!');
    }

    public function destroy(Request $request, EarningsGoal $goal): RedirectResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $goal->delete();

        return back()->with('success', 'Earnings goal removed.');
    }
}
