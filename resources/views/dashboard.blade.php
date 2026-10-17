@extends('layouts.app')

@section('title', 'Worker Dashboard')

@section('content')
<div>
    <!-- Welcome Header & Quick Action Buttons -->
    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem; @media(min-width:768px){flex-direction: row; justify-content: space-between; align-items: center;}">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Namaste, {{ $user->name }}</h1>
                <span class="badge badge-success">{{ $user->worker_type ?? 'Gig Partner' }}</span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Language: <strong>{{ $user->preferred_language }}</strong> &bull; Track shifts, audit deductions, and protect your gig earnings.
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('earnings.create') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Shift
            </a>
            <a href="{{ route('complaints.create') }}" class="btn btn-secondary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Draft Complaint
            </a>
            <a href="{{ route('assistant.index') }}" class="btn btn-secondary" style="border-color: #a7f3d0; background: #ecfdf5; color: #065f46;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Ask Assistant
            </a>
        </div>
    </div>

    <!-- Real Database-Backed Metrics Grid -->
    <div class="metric-grid">
        <!-- Metric 1: Today's Net Earnings -->
        <div class="metric-card">
            <div class="metric-label">Today's Net Earnings</div>
            <div class="metric-value currency" style="color: #059669;">
                ₹{{ number_format($todayNet, 2) }}
            </div>
            <div class="metric-sub">
                {{ $todayHours }} hrs worked today
            </div>
        </div>

        <!-- Metric 2: Weekly Net Earnings -->
        <div class="metric-card">
            <div class="metric-label">Weekly Net Earnings</div>
            <div class="metric-value currency">
                ₹{{ number_format($weeklyNet, 2) }}
            </div>
            <div class="metric-sub">
                {{ $weeklyHours }} hrs &bull; Avg ₹{{ number_format($weeklyHourly, 2) }}/hr
            </div>
        </div>

        <!-- Metric 3: Total Hours & All-time Hourly -->
        <div class="metric-card">
            <div class="metric-label">Total Hours Worked</div>
            <div class="metric-value">
                {{ number_format($totalHours, 1) }} <span style="font-size: 0.95rem; font-weight: 600; color: var(--gray-600);">hrs</span>
            </div>
            <div class="metric-sub">
                Avg Rate: <strong class="currency">₹{{ number_format($allTimeHourly, 2) }}/hr</strong>
            </div>
        </div>

        <!-- Metric 4: Saved Complaints -->
        <div class="metric-card">
            <div class="metric-label">Saved Complaints</div>
            <div class="metric-value" style="color: #ea580c;">
                {{ $savedComplaintsCount }}
            </div>
            <div class="metric-sub">
                {{ $pendingComplaintsCount }} active drafts
            </div>
        </div>
    </div>

    <!-- Two Columns: Recent Earnings & Recent Complaints -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 992px){grid-template-columns: 3fr 2fr;}">
        <!-- Recent Earnings Section -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Recent Shift Earnings
                </h3>
                <a href="{{ route('earnings.index') }}" style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-decoration: none;">View All &rarr;</a>
            </div>

            @if($recentEarnings->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p style="font-weight: 600; color: var(--gray-700);">No shift earnings recorded yet.</p>
                    <p style="font-size: 0.85rem; margin-top: 0.25rem;">Record your daily shifts to calculate true net income and unlock Gemini AI explanations.</p>
                    <a href="{{ route('earnings.create') }}" class="btn btn-primary btn-sm" style="margin-top: 1rem;">
                        + Record First Shift
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Gross + Inc</th>
                                <th>Expenses</th>
                                <th>Net Take-Home</th>
                                <th>Hourly Rate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEarnings as $entry)
                                <tr>
                                    <td>
                                        <strong>{{ $entry->work_date->format('d M Y') }}</strong>
                                        <div style="font-size: 0.75rem; color: var(--gray-600);">{{ $entry->hours_worked }} hrs</div>
                                    </td>
                                    <td>
                                        <div class="currency">₹{{ number_format($entry->total_income, 2) }}</div>
                                        <div style="font-size: 0.75rem; color: #059669;">+₹{{ number_format($entry->incentives, 2) }} inc</div>
                                    </td>
                                    <td>
                                        <div class="currency" style="color: var(--danger);">₹{{ number_format($entry->total_expenses, 2) }}</div>
                                        <div style="font-size: 0.75rem; color: var(--gray-600);">-₹{{ number_format($entry->platform_deductions, 2) }} ded</div>
                                    </td>
                                    <td>
                                        <div class="currency" style="font-size: 1.05rem; color: #059669; font-weight: 800;">
                                            ₹{{ number_format($entry->net_earnings, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-neutral">₹{{ number_format($entry->effective_hourly_earnings, 2) }}/hr</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('earnings.show', $entry) }}" class="btn btn-secondary btn-sm" title="Explain or View Breakdown">
                                            Breakdown &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Recent Complaints Section -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Recent Complaints
                </h3>
                <a href="{{ route('complaints.index') }}" style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-decoration: none;">View All &rarr;</a>
            </div>

            @if($recentComplaints->isEmpty())
                <div class="empty-state" style="padding: 2.5rem 1rem;">
                    <div class="empty-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p style="font-weight: 600; color: var(--gray-700);">No complaints drafted yet.</p>
                    <p style="font-size: 0.85rem; margin-top: 0.25rem;">Faced an unfair deduction or missing incentive? Use Gigy AI to generate a formal dispute draft.</p>
                    <a href="{{ route('complaints.create') }}" class="btn btn-secondary btn-sm" style="margin-top: 1rem;">
                        Draft Grievance Ticket
                    </a>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($recentComplaints as $complaint)
                        <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 0.85rem; background: var(--gray-50);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                                <div>
                                    <strong style="color: var(--gray-900);">{{ $complaint->company_name }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--gray-600); margin-left: 0.25rem;">&bull; {{ $complaint->issue_category }}</span>
                                </div>
                                <span class="badge {{ $complaint->status === 'resolved' ? 'badge-success' : ($complaint->status === 'submitted' ? 'badge-info' : 'badge-warning') }}">
                                    {{ $complaint->status }}
                                </span>
                            </div>
                            <p style="font-size: 0.825rem; color: var(--gray-700); margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $complaint->description }}
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--gray-600);">
                                <span>{{ $complaint->created_at->diffForHumans() }}</span>
                                <a href="{{ route('complaints.show', $complaint) }}" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                    View Draft &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
