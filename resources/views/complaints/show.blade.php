@extends('layouts.app')

@section('title', 'Complaint Record - ' . $complaint->company_name)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">{{ $complaint->company_name }} Dispute</h1>
                <span class="badge {{ $complaint->status === 'resolved' ? 'badge-success' : ($complaint->status === 'submitted' ? 'badge-info' : 'badge-warning') }}">
                    {{ $complaint->status }}
                </span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Category: <strong>{{ $complaint->issue_category }}</strong> &bull; Drafted on {{ $complaint->created_at->format('d M Y, h:i A') }}
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('complaints.index') }}" class="btn btn-secondary btn-sm">&larr; All Complaints</a>
            <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-secondary btn-sm">Edit</a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
        <div class="card" style="margin-bottom: 0;">
            <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Discrepancy</div>
            <div style="margin-top: 0.25rem;">
                <div style="font-size: 0.85rem; color: var(--gray-600);">Expected: <strong class="currency" style="color: #059669;">{{ $complaint->expected_amount !== null ? '₹' . number_format($complaint->expected_amount, 2) : 'N/A' }}</strong></div>
                <div style="font-size: 0.85rem; color: var(--gray-600);">Received: <strong class="currency" style="color: var(--danger);">{{ $complaint->received_amount !== null ? '₹' . number_format($complaint->received_amount, 2) : 'N/A' }}</strong></div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Incident Date</div>
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--gray-900); margin-top: 0.25rem;">
                {{ $complaint->incident_date ? $complaint->incident_date->format('d M Y') : 'Not specified' }}
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Language</div>
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--gray-900); margin-top: 0.25rem;">
                {{ $complaint->preferred_language }}
            </div>
        </div>
    </div>

    <!-- Description & Context -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Worker's Problem Statement</h3>
        </div>
        <p style="font-size: 0.95rem; color: var(--gray-800); line-height: 1.6;">
            {{ $complaint->description }}
        </p>
    </div>

    <!-- Generated Draft Ticket -->
    <div class="card" style="border: 2px solid #cbd5e1;">
        <div class="card-header" style="background: var(--gray-50); margin: -1.25rem -1.25rem 1rem -1.25rem; padding: 0.85rem 1.25rem; border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg);">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.2rem;">📄</span>
                <h3 class="card-title" style="margin-bottom: 0;">Formal Dispute Draft</h3>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('draftBox').innerText); alert('Complaint draft copied to clipboard!');">
                📋 Copy Draft to Clipboard
            </button>
        </div>

        @if($complaint->generated_draft)
            <div id="draftBox" style="background: #ffffff; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200); font-family: monospace; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap; color: var(--gray-900);">{{ $complaint->generated_draft }}</div>
        @else
            <div style="text-align: center; color: var(--gray-600); padding: 1.5rem;">
                No AI draft text was saved for this complaint. You can <a href="{{ route('complaints.edit', $complaint) }}" style="color: var(--primary); font-weight: 700;">edit it</a> to generate one.
            </div>
        @endif

        <div style="margin-top: 1rem; font-size: 0.775rem; color: var(--gray-600);">
            💡 <strong>Next Step:</strong> Copy this draft and paste it into the Support / Help section of your partner app (Zomato Partner, Swiggy Partner, Uber Driver, etc.) or grievance ticket email.
        </div>
    </div>

    <!-- Quick Status Update Form & Delete -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Status</h3>
        </div>
        <form method="POST" action="{{ route('complaints.update', $complaint) }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            @csrf
            @method('PUT')
            <input type="hidden" name="company_name" value="{{ $complaint->company_name }}">
            <input type="hidden" name="issue_category" value="{{ $complaint->issue_category }}">
            <input type="hidden" name="expected_amount" value="{{ $complaint->expected_amount }}">
            <input type="hidden" name="received_amount" value="{{ $complaint->received_amount }}">
            <input type="hidden" name="incident_date" value="{{ $complaint->incident_date ? $complaint->incident_date->format('Y-m-d') : '' }}">
            <input type="hidden" name="description" value="{{ $complaint->description }}">
            <input type="hidden" name="preferred_language" value="{{ $complaint->preferred_language }}">
            <input type="hidden" name="generated_draft" value="{{ $complaint->generated_draft }}">

            <label for="statusSelect" class="form-label" style="margin-bottom: 0;">Update Dispute Status:</label>
            <select id="statusSelect" name="status" class="form-control" style="width: auto;">
                <option value="draft" {{ $complaint->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ $complaint->status === 'submitted' ? 'selected' : '' }}>Submitted to Support</option>
                <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Resolved / Paid Back</option>
                <option value="cancelled" {{ $complaint->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm">Save Status</button>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-secondary btn-sm">Edit Full Details</a>

            <form method="POST" action="{{ route('complaints.destroy', $complaint) }}" onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete Record</button>
            </form>
        </div>
    </div>
</div>
@endsection
