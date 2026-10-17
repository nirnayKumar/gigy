@extends('layouts.app')

@section('title', 'Draft Grievance Complaint')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Draft Grievance Complaint</h1>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Generate professional dispute tickets for platform grievance officers with Gemini AI.
            </p>
        </div>
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary btn-sm">&larr; Back to Complaints</a>
    </div>

    @if(!empty($prefill['earnings_entry_id']))
        <div class="alert alert-info" style="margin-bottom: 1rem;">
            <span>ℹ Prefilled with details from your recorded shift on {{ $prefill['incident_date'] ?? '' }}. You can edit any field.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('complaints.store') }}" id="complaintForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 768px){grid-template-columns: 1fr 1fr;}">
            <!-- Left Column: Details -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3 class="card-title">1. Dispute Details</h3>
                    <span class="badge badge-neutral">Required Info</span>
                </div>

                <div class="form-group">
                    <label for="company_name" class="form-label">Platform / Company *</label>
                    <select id="company_name" name="company_name" class="form-control" required>
                        <option value="Zomato" {{ old('company_name') == 'Zomato' ? 'selected' : '' }}>Zomato</option>
                        <option value="Swiggy" {{ old('company_name') == 'Swiggy' ? 'selected' : '' }}>Swiggy</option>
                        <option value="Uber" {{ old('company_name') == 'Uber' ? 'selected' : '' }}>Uber India</option>
                        <option value="Ola Cabs" {{ old('company_name') == 'Ola Cabs' ? 'selected' : '' }}>Ola Cabs / Auto</option>
                        <option value="Zepto" {{ old('company_name') == 'Zepto' ? 'selected' : '' }}>Zepto</option>
                        <option value="Blinkit" {{ old('company_name') == 'Blinkit' ? 'selected' : '' }}>Blinkit</option>
                        <option value="Porter" {{ old('company_name') == 'Porter' ? 'selected' : '' }}>Porter</option>
                        <option value="Amazon Flex" {{ old('company_name') == 'Amazon Flex' ? 'selected' : '' }}>Amazon Flex</option>
                        <option value="Shadowfax" {{ old('company_name') == 'Shadowfax' ? 'selected' : '' }}>Shadowfax</option>
                        <option value="Other Platform" {{ old('company_name') == 'Other Platform' ? 'selected' : '' }}>Other Platform</option>
                    </select>
                    @error('company_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="issue_category" class="form-label">Issue Category *</label>
                    <select id="issue_category" name="issue_category" class="form-control" required>
                        <option value="Unfair Platform Deduction" {{ old('issue_category') == 'Unfair Platform Deduction' ? 'selected' : '' }}>Unfair Platform Deduction</option>
                        <option value="Missing Target Incentive" {{ old('issue_category') == 'Missing Target Incentive' ? 'selected' : '' }}>Missing Target Incentive / Surge</option>
                        <option value="Wrong Customer Cancellation Penalty" {{ old('issue_category') == 'Wrong Customer Cancellation Penalty' ? 'selected' : '' }}>Wrong Customer Cancellation Penalty</option>
                        <option value="Unpaid Restaurant Waiting Time" {{ old('issue_category') == 'Unpaid Restaurant Waiting Time' ? 'selected' : '' }}>Unpaid Restaurant / Pickup Waiting Time</option>
                        <option value="Cash on Delivery (COD) Deposit Discrepancy" {{ old('issue_category') == 'Cash on Delivery (COD) Deposit Discrepancy' ? 'selected' : '' }}>Cash on Delivery (COD) Deposit Discrepancy</option>
                        <option value="Wrong Mileage / Toll Calculation" {{ old('issue_category') == 'Wrong Mileage / Toll Calculation' ? 'selected' : '' }}>Wrong Mileage / Toll Reimbursement</option>
                        <option value="Other Payment Issue" {{ old('issue_category') == 'Other Payment Issue' ? 'selected' : '' }}>Other Payment Issue</option>
                    </select>
                    @error('issue_category')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="incident_date" class="form-label">Incident Date</label>
                    <input id="incident_date" type="date" name="incident_date" class="form-control" value="{{ old('incident_date', $prefill['incident_date'] ?? date('Y-m-d')) }}">
                    @error('incident_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="expected_amount" class="form-label">Expected Amount (₹)</label>
                        <input id="expected_amount" type="number" step="0.01" min="0" name="expected_amount" class="form-control" value="{{ old('expected_amount', $prefill['expected_amount'] ?? '1100.00') }}" placeholder="1100.00">
                        @error('expected_amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="received_amount" class="form-label">Received Amount (₹)</label>
                        <input id="received_amount" type="number" step="0.01" min="0" name="received_amount" class="form-control" value="{{ old('received_amount', $prefill['received_amount'] ?? '900.00') }}" placeholder="900.00">
                        @error('received_amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="preferred_language" class="form-label">Draft Language *</label>
                    <select id="preferred_language" name="preferred_language" class="form-control">
                        <option value="English" {{ old('preferred_language', Auth::user()->preferred_language) == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Hindi" {{ old('preferred_language', Auth::user()->preferred_language) == 'Hindi' ? 'selected' : '' }}>Hindi (हिंदी)</option>
                        <option value="Punjabi" {{ old('preferred_language', Auth::user()->preferred_language) == 'Punjabi' ? 'selected' : '' }}>Punjabi (ਪੰਜਾਬੀ)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="earnings_entry_id" class="form-label">Associated Shift (Optional)</label>
                    <select id="earnings_entry_id" name="earnings_entry_id" class="form-control">
                        <option value="">-- None --</option>
                        @foreach($userEarnings as $ue)
                            <option value="{{ $ue->id }}" {{ old('earnings_entry_id', $prefill['earnings_entry_id'] ?? '') == $ue->id ? 'selected' : '' }}>
                                Shift on {{ $ue->work_date->format('d M Y') }} (Net: ₹{{ number_format($ue->net_earnings, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="description" class="form-label">What happened? (Facts & details) *</label>
                    <textarea id="description" name="description" rows="4" class="form-control" placeholder="Describe the order/trip IDs, what was promised, what was deducted, and why it is incorrect..." required>{{ old('description', $prefill['description'] ?? 'Completed 15 target deliveries during the rainy peak shift on scheduled hours. Target bonus of ₹100 was not credited and ₹50 penalty was applied without reason.') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Right Column: AI Generation & Editable Draft -->
            <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">
                <div class="card-header">
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <span style="font-size: 1.25rem;">✨</span>
                        <h3 class="card-title">2. AI Complaint Draft</h3>
                    </div>
                    <span class="badge badge-warning" id="draftStatusBadge">Draft Only</span>
                </div>

                <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 1rem;">
                    Generate an assertive, polite complaint ticket using Gemini AI. You can review, edit, and copy it before saving.
                </p>

                <!-- Trigger AI Generation Button -->
                <button type="button" id="btnGenerateAI" class="btn btn-primary btn-block" style="margin-bottom: 1rem; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <span id="btnAIIcon">✨</span>
                    <span id="btnAIText">Generate Dispute Draft with AI</span>
                </button>

                <!-- Editable Draft Textarea -->
                <div class="form-group" style="flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <label for="generated_draft" class="form-label" style="margin-bottom: 0;">Formal Draft Content (Editable):</label>
                        <button type="button" id="btnCopyDraft" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                            📋 Copy Text
                        </button>
                    </div>
                    <textarea id="generated_draft" name="generated_draft" rows="12" class="form-control" style="flex: 1; font-family: monospace; font-size: 0.875rem; line-height: 1.6;" placeholder="Click 'Generate Dispute Draft with AI' above or type your complaint letter here...">{{ old('generated_draft') }}</textarea>
                    @error('generated_draft')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-sm); padding: 0.75rem; margin-bottom: 1rem; font-size: 0.75rem; color: var(--gray-600);">
                    ⚠️ <strong>Notice:</strong> Gigy prepares and saves this dispute ticket in your local database for your records. We never automatically send emails or messages to platforms.
                </div>

                <!-- Submit / Save Button -->
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    💾 Save Complaint Draft to Database
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btnGenerateAI').addEventListener('click', async function () {
        const company = document.getElementById('company_name').value;
        const category = document.getElementById('issue_category').value;
        const expected = document.getElementById('expected_amount').value;
        const received = document.getElementById('received_amount').value;
        const incidentDate = document.getElementById('incident_date').value;
        const description = document.getElementById('description').value;
        const language = document.getElementById('preferred_language').value;

        if (!description.trim()) {
            alert('Please provide a brief description of the issue first.');
            document.getElementById('description').focus();
            return;
        }

        const btn = document.getElementById('btnGenerateAI');
        const originalText = document.getElementById('btnAIText').innerText;
        btn.disabled = true;
        document.getElementById('btnAIText').innerText = 'Generating draft with Gemini AI...';

        try {
            const response = await fetch("{{ route('complaints.generate-draft') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    company_name: company,
                    issue_category: category,
                    expected_amount: expected,
                    received_amount: received,
                    incident_date: incidentDate,
                    description: description,
                    preferred_language: language
                })
            });

            const data = await response.json();
            if (data.success && data.draft) {
                document.getElementById('generated_draft').value = data.draft;
                document.getElementById('draftStatusBadge').className = 'badge badge-success';
                document.getElementById('draftStatusBadge').innerText = 'Draft Generated (' + data.language + ')';
            } else {
                alert('Could not generate draft: ' + (data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred while connecting to the AI service.');
        } finally {
            btn.disabled = false;
            document.getElementById('btnAIText').innerText = originalText;
        }
    });

    document.getElementById('btnCopyDraft').addEventListener('click', function () {
        const text = document.getElementById('generated_draft').value;
        if (!text.trim()) {
            alert('No draft content to copy.');
            return;
        }
        navigator.clipboard.writeText(text).then(() => {
            alert('Complaint draft copied to clipboard!');
        });
    });
</script>
@endpush
