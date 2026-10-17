@extends('layouts.app')

@section('title', 'Edit Complaint - ' . $complaint->company_name)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Edit Complaint</h1>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">Update dispute information or regenerate the formal draft.</p>
        </div>
        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-secondary btn-sm">&larr; Back to Complaint</a>
    </div>

    <form method="POST" action="{{ route('complaints.update', $complaint) }}">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 768px){grid-template-columns: 1fr 1fr;}">
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3 class="card-title">Dispute Details</h3>
                </div>

                <div class="form-group">
                    <label for="company_name" class="form-label">Platform / Company *</label>
                    <input id="company_name" type="text" name="company_name" class="form-control" value="{{ old('company_name', $complaint->company_name) }}" required>
                </div>

                <div class="form-group">
                    <label for="issue_category" class="form-label">Issue Category *</label>
                    <input id="issue_category" type="text" name="issue_category" class="form-control" value="{{ old('issue_category', $complaint->issue_category) }}" required>
                </div>

                <div class="form-group">
                    <label for="incident_date" class="form-label">Incident Date</label>
                    <input id="incident_date" type="date" name="incident_date" class="form-control" value="{{ old('incident_date', $complaint->incident_date ? $complaint->incident_date->format('Y-m-d') : '') }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="expected_amount" class="form-label">Expected Amount (₹)</label>
                        <input id="expected_amount" type="number" step="0.01" min="0" name="expected_amount" class="form-control" value="{{ old('expected_amount', $complaint->expected_amount) }}">
                    </div>

                    <div class="form-group">
                        <label for="received_amount" class="form-label">Received Amount (₹)</label>
                        <input id="received_amount" type="number" step="0.01" min="0" name="received_amount" class="form-control" value="{{ old('received_amount', $complaint->received_amount) }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="preferred_language" class="form-label">Language</label>
                        <select id="preferred_language" name="preferred_language" class="form-control">
                            <option value="English" {{ old('preferred_language', $complaint->preferred_language) == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Hindi" {{ old('preferred_language', $complaint->preferred_language) == 'Hindi' ? 'selected' : '' }}>Hindi (हिंदी)</option>
                            <option value="Punjabi" {{ old('preferred_language', $complaint->preferred_language) == 'Punjabi' ? 'selected' : '' }}>Punjabi (ਪੰਜਾਬੀ)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft" {{ old('status', $complaint->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ old('status', $complaint->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="cancelled" {{ old('status', $complaint->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" name="description" rows="4" class="form-control" required>{{ old('description', $complaint->description) }}</textarea>
                </div>
            </div>

            <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">
                <div class="card-header">
                    <h3 class="card-title">Formal Draft Content</h3>
                    <button type="button" id="btnRegenerate" class="btn btn-secondary btn-sm">
                        ✨ Regenerate AI Draft
                    </button>
                </div>

                <div class="form-group" style="flex: 1; display: flex; flex-direction: column;">
                    <textarea id="generated_draft" name="generated_draft" rows="12" class="form-control" style="flex: 1; font-family: monospace; font-size: 0.875rem; line-height: 1.6;">{{ old('generated_draft', $complaint->generated_draft) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btnRegenerate').addEventListener('click', async function () {
        const company = document.getElementById('company_name').value;
        const category = document.getElementById('issue_category').value;
        const expected = document.getElementById('expected_amount').value;
        const received = document.getElementById('received_amount').value;
        const incidentDate = document.getElementById('incident_date').value;
        const description = document.getElementById('description').value;
        const language = document.getElementById('preferred_language').value;

        const btn = document.getElementById('btnRegenerate');
        btn.disabled = true;
        btn.innerText = 'Generating...';

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
            } else {
                alert('Could not regenerate draft: ' + (data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred while connecting to the AI service.');
        } finally {
            btn.disabled = false;
            btn.innerText = '✨ Regenerate AI Draft';
        }
    });
</script>
@endpush
