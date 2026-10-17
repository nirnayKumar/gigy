@extends('layouts.app')

@section('title', 'Grievance & Payment Complaints')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Complaints & Grievances</h1>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Draft, track, and manage payment disputes for Zomato, Swiggy, Uber, Ola, Zepto, Blinkit & other platforms.
            </p>
        </div>
        <a href="{{ route('complaints.create') }}" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Draft New Complaint
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Saved Complaint Records</h3>
            <span style="font-size: 0.85rem; color: var(--gray-600);">{{ $complaints->total() }} recorded</span>
        </div>

        @if($complaints->isEmpty())
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p style="font-weight: 600; color: var(--gray-700);">No complaints or dispute drafts yet.</p>
                <p style="font-size: 0.85rem; margin-top: 0.25rem;">If a platform deducted unfair penalties or withheld incentives, generate a professional dispute draft in seconds.</p>
                <a href="{{ route('complaints.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Draft First Complaint
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <caption class="sr-only">List of saved grievance drafts and dispute submissions</caption>
                    <thead>
                        <tr>
                            <th scope="col">Platform</th>
                            <th scope="col">Issue Category</th>
                            <th scope="col">Incident Date</th>
                            <th scope="col">Expected vs Received</th>
                            <th scope="col">Language</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $c)
                            <tr>
                                <td>
                                    <strong style="font-size: 0.95rem; color: var(--gray-900);">{{ $c->company_name }}</strong>
                                    @if($c->earningsEntry)
                                        <div style="font-size: 0.725rem; color: var(--primary);">
                                            Linked to shift: {{ $c->earningsEntry->work_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $c->issue_category }}</span>
                                </td>
                                <td>
                                    <span>{{ $c->incident_date ? $c->incident_date->format('d M Y') : 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="currency" style="font-size: 0.85rem;">
                                        Exp: <span style="color: #059669;">{{ $c->expected_amount !== null ? '₹' . number_format($c->expected_amount, 2) : '—' }}</span>
                                    </div>
                                    <div class="currency" style="font-size: 0.85rem; color: var(--danger);">
                                        Rec: <span>{{ $c->received_amount !== null ? '₹' . number_format($c->received_amount, 2) : '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-neutral">{{ $c->preferred_language }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $c->status === 'resolved' ? 'badge-success' : ($c->status === 'submitted' ? 'badge-info' : 'badge-warning') }}">
                                        {{ $c->status }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.4rem;">
                                        <a href="{{ route('complaints.show', $c) }}" class="btn btn-secondary btn-sm" aria-label="View complaint draft for {{ $c->company_name }}">
                                            View
                                        </a>
                                        <a href="{{ route('complaints.edit', $c) }}" class="btn btn-secondary btn-sm" aria-label="Edit complaint draft for {{ $c->company_name }}">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('complaints.destroy', $c) }}" onsubmit="return confirm('Delete complaint draft for {{ $c->company_name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: #fecaca;" aria-label="Delete complaint draft for {{ $c->company_name }}">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav aria-label="Complaints pagination" style="margin-top: 1.25rem;">
                {{ $complaints->links() }}
            </nav>
        @endif
    </div>
</div>
@endsection
