<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExplainEarningsRequest;
use App\Http\Requests\StoreEarningsRequest;
use App\Models\EarningsEntry;
use App\Services\EarningsService;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function __construct(
        protected EarningsService $earningsService,
        protected GeminiService $geminiService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $entries = $user->earningsEntries()->paginate(15);

        // Aggregate statistics using optimized collection
        $allEntries = $user->earningsEntries()->get();
        $totalGross = round($allEntries->sum('gross_earnings'), 2);
        $totalIncentives = round($allEntries->sum('incentives'), 2);
        $totalExpenses = round($allEntries->sum(fn($e) => $e->total_expenses), 2);
        $totalNet = round($allEntries->sum(fn($e) => $e->net_earnings), 2);
        $totalHours = round($allEntries->sum('hours_worked'), 2);
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

    public function create(): View
    {
        return view('earnings.create');
    }

    public function store(StoreEarningsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $entry = DB::transaction(function () use ($request, $validated) {
            return $request->user()->earningsEntries()->create([
                'work_date' => $validated['work_date'],
                'gross_earnings' => $validated['gross_earnings'],
                'incentives' => $validated['incentives'] ?? 0.00,
                'fuel_expenses' => $validated['fuel_expenses'] ?? 0.00,
                'other_expenses' => $validated['other_expenses'] ?? 0.00,
                'platform_deductions' => $validated['platform_deductions'] ?? 0.00,
                'hours_worked' => $validated['hours_worked'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('earnings.show', $entry)->with('success', 'Earnings shift recorded successfully!');
    }

    public function show(Request $request, EarningsEntry $earningsEntry): View
    {
        // Enforce policy authorization
        $this->authorize('view', $earningsEntry);

        return view('earnings.show', [
            'entry' => $earningsEntry,
        ]);
    }

    public function explain(ExplainEarningsRequest $request, EarningsEntry $earningsEntry): View|JsonResponse
    {
        // Enforce policy authorization
        $this->authorize('view', $earningsEntry);

        $language = $request->input('language', $request->user()->preferred_language ?? 'English');
        if (!in_array($language, ['English', 'Hindi', 'Punjabi'])) {
            $language = 'English';
        }

        $explanation = $this->geminiService->explainEarnings($earningsEntry, $language);

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

    public function destroy(Request $request, EarningsEntry $earningsEntry): RedirectResponse
    {
        // Enforce policy authorization
        $this->authorize('delete', $earningsEntry);

        DB::transaction(function () use ($earningsEntry) {
            $earningsEntry->delete();
        });

        return redirect()->route('earnings.index')->with('success', 'Earnings record deleted.');
    }
}
