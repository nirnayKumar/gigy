<?php

namespace App\Http\Controllers;

use App\Models\EarningsEntry;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $entries = $user->earningsEntries()->paginate(15);

        // Calculate summary for this user
        $allEntries = $user->earningsEntries()->get();
        $totalGross = $allEntries->sum('gross_earnings');
        $totalIncentives = $allEntries->sum('incentives');
        $totalExpenses = $allEntries->sum(fn($e) => $e->total_expenses);
        $totalNet = $allEntries->sum(fn($e) => $e->net_earnings);
        $totalHours = $allEntries->sum('hours_worked');
        $avgHourly = $totalHours > 0 ? round($totalNet / $totalHours, 2) : 0.00;

        return view('earnings.index', [
            'entries' => $entries,
            'totalGross' => $totalGross,
            'totalIncentives' => $totalIncentives,
            'totalExpenses' => $totalExpenses,
            'totalNet' => $totalNet,
            'totalHours' => $totalHours,
            'avgHourly' => $avgHourly,
        ]);
    }

    public function create()
    {
        return view('earnings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_date' => ['required', 'date'],
            'gross_earnings' => ['required', 'numeric', 'min:0'],
            'incentives' => ['nullable', 'numeric', 'min:0'],
            'fuel_expenses' => ['nullable', 'numeric', 'min:0'],
            'other_expenses' => ['nullable', 'numeric', 'min:0'],
            'platform_deductions' => ['nullable', 'numeric', 'min:0'],
            'hours_worked' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $entry = $request->user()->earningsEntries()->create([
            'work_date' => $validated['work_date'],
            'gross_earnings' => $validated['gross_earnings'],
            'incentives' => $validated['incentives'] ?? 0.00,
            'fuel_expenses' => $validated['fuel_expenses'] ?? 0.00,
            'other_expenses' => $validated['other_expenses'] ?? 0.00,
            'platform_deductions' => $validated['platform_deductions'] ?? 0.00,
            'hours_worked' => $validated['hours_worked'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('earnings.show', $entry)->with('success', 'Earnings shift recorded successfully!');
    }

    public function show(EarningsEntry $earningsEntry)
    {
        if ($earningsEntry->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this earnings record.');
        }

        return view('earnings.show', [
            'entry' => $earningsEntry,
        ]);
    }

    public function explain(Request $request, EarningsEntry $earningsEntry, GeminiService $gemini)
    {
        if ($earningsEntry->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this earnings record.');
        }

        $language = $request->input('language', $request->user()->preferred_language ?? 'English');
        if (!in_array($language, ['English', 'Hindi', 'Punjabi'])) {
            $language = 'English';
        }

        $explanation = $gemini->explainEarnings($earningsEntry, $language);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'language' => $language,
                'explanation' => $explanation,
            ]);
        }

        return view('earnings.show', [
            'entry' => $earningsEntry,
            'explanation' => $explanation,
            'activeLanguage' => $language,
        ]);
    }

    public function destroy(EarningsEntry $earningsEntry)
    {
        if ($earningsEntry->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this earnings record.');
        }

        $earningsEntry->delete();

        return redirect()->route('earnings.index')->with('success', 'Earnings record deleted.');
    }
}
