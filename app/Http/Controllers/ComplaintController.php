<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateComplaintDraftRequest;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Models\Complaint;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function __construct(
        protected GeminiService $geminiService
    ) {}

    public function index(Request $request): View
    {
        $complaints = $request->user()->complaints()
            ->with('earningsEntry')
            ->paginate(15);

        return view('complaints.index', [
            'complaints' => $complaints,
        ]);
    }

    public function create(Request $request): View
    {
        $prefill = [];
        $earningsEntryId = $request->query('earnings_entry_id');

        if ($earningsEntryId) {
            $entry = $request->user()->earningsEntries()->find($earningsEntryId);
            if ($entry) {
                $prefill = [
                    'earnings_entry_id' => $entry->id,
                    'incident_date' => $entry->work_date->format('Y-m-d'),
                    'expected_amount' => $entry->total_income,
                    'received_amount' => $entry->net_earnings,
                    'description' => "Discrepancy in shift on {$entry->work_date->format('d M Y')}. Platform deductions of ₹{$entry->platform_deductions} were applied.",
                ];
            }
        }

        $userEarnings = $request->user()->earningsEntries()->latest('work_date')->take(20)->get();

        return view('complaints.create', [
            'prefill' => $prefill,
            'userEarnings' => $userEarnings,
        ]);
    }

    public function generateDraft(GenerateComplaintDraftRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $language = $validated['preferred_language'] ?? ($request->user()->preferred_language ?? 'English');

        $draft = $this->geminiService->generateComplaintDraft($validated, $language);

        return response()->json([
            'success' => true,
            'draft' => $draft,
            'language' => $language,
        ]);
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Verify earnings_entry_id belongs to logged-in user if provided
        if (!empty($validated['earnings_entry_id'])) {
            $ownsEntry = $request->user()->earningsEntries()->where('id', $validated['earnings_entry_id'])->exists();
            if (!$ownsEntry) {
                $validated['earnings_entry_id'] = null;
            }
        }

        $complaint = DB::transaction(function () use ($request, $validated) {
            return $request->user()->complaints()->create([
                'earnings_entry_id' => $validated['earnings_entry_id'] ?? null,
                'company_name' => $validated['company_name'],
                'issue_category' => $validated['issue_category'],
                'expected_amount' => $validated['expected_amount'] ?? null,
                'received_amount' => $validated['received_amount'] ?? null,
                'incident_date' => $validated['incident_date'] ?? null,
                'description' => $validated['description'],
                'preferred_language' => $validated['preferred_language'] ?? 'English',
                'generated_draft' => $validated['generated_draft'] ?? null,
                'status' => $validated['status'] ?? 'draft',
            ]);
        });

        return redirect()->route('complaints.show', $complaint)->with('success', 'Complaint draft saved to your database!');
    }

    public function show(Request $request, Complaint $complaint): View
    {
        $this->authorize('view', $complaint);

        return view('complaints.show', [
            'complaint' => $complaint,
        ]);
    }

    public function edit(Request $request, Complaint $complaint): View
    {
        $this->authorize('update', $complaint);

        $userEarnings = $request->user()->earningsEntries()->latest('work_date')->take(20)->get();

        return view('complaints.edit', [
            'complaint' => $complaint,
            'userEarnings' => $userEarnings,
        ]);
    }

    public function update(UpdateComplaintRequest $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('update', $complaint);
        $validated = $request->validated();

        DB::transaction(function () use ($complaint, $validated) {
            $complaint->update($validated);
        });

        return redirect()->route('complaints.show', $complaint)->with('success', 'Complaint draft updated.');
    }

    public function destroy(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('delete', $complaint);

        DB::transaction(function () use ($complaint) {
            $complaint->delete();
        });

        return redirect()->route('complaints.index')->with('success', 'Complaint record deleted.');
    }
}
