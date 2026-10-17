<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\EarningsEntry;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = $request->user()->complaints()->with('earningsEntry')->paginate(15);
        return view('complaints.index', [
            'complaints' => $complaints,
        ]);
    }

    public function create(Request $request)
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

    public function generateDraft(Request $request, GeminiService $gemini)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:100'],
            'issue_category' => ['required', 'string', 'max:100'],
            'expected_amount' => ['nullable', 'numeric', 'min:0'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'incident_date' => ['nullable', 'date'],
            'description' => ['required', 'string', 'max:2000'],
            'preferred_language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
        ]);

        $language = $validated['preferred_language'] ?? ($request->user()->preferred_language ?? 'English');
        $draft = $gemini->generateComplaintDraft($validated, $language);

        return response()->json([
            'success' => true,
            'draft' => $draft,
            'language' => $language,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'earnings_entry_id' => ['nullable', 'exists:earnings_entries,id'],
            'company_name' => ['required', 'string', 'max:100'],
            'issue_category' => ['required', 'string', 'max:100'],
            'expected_amount' => ['nullable', 'numeric', 'min:0'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'incident_date' => ['nullable', 'date'],
            'description' => ['required', 'string', 'max:2000'],
            'preferred_language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
            'generated_draft' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,submitted,resolved,cancelled'],
        ]);

        // Verify earnings_entry_id belongs to user if present
        if (!empty($validated['earnings_entry_id'])) {
            $ownsEntry = $request->user()->earningsEntries()->where('id', $validated['earnings_entry_id'])->exists();
            if (!$ownsEntry) {
                $validated['earnings_entry_id'] = null;
            }
        }

        $complaint = $request->user()->complaints()->create([
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

        return redirect()->route('complaints.show', $complaint)->with('success', 'Complaint draft saved to your database!');
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        return view('complaints.show', [
            'complaint' => $complaint,
        ]);
    }

    public function edit(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        $userEarnings = Auth::user()->earningsEntries()->latest('work_date')->take(20)->get();

        return view('complaints.edit', [
            'complaint' => $complaint,
            'userEarnings' => $userEarnings,
        ]);
    }

    public function update(Request $request, Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:100'],
            'issue_category' => ['required', 'string', 'max:100'],
            'expected_amount' => ['nullable', 'numeric', 'min:0'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'incident_date' => ['nullable', 'date'],
            'description' => ['required', 'string', 'max:2000'],
            'preferred_language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
            'generated_draft' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:draft,submitted,resolved,cancelled'],
        ]);

        $complaint->update($validated);

        return redirect()->route('complaints.show', $complaint)->with('success', 'Complaint draft updated.');
    }

    public function destroy(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        $complaint->delete();

        return redirect()->route('complaints.index')->with('success', 'Complaint record deleted.');
    }
}
