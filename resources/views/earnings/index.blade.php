@extends('layouts.app')

@section('title', 'Earnings Tracker & History')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Earnings Tracker</h1>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Audit your shifts, track fuel & deductions, and compute true hourly wages.
            </p>
        </div>
        <a href="{{ route('earnings.create') }}" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record New Shift
        </a>
    </div>

    <!-- Aggregate Lifetime Stats Banner -->
    <div class="card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border: none;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; @media(min-width:768px){grid-template-columns: repeat(4, 1fr);}">
            <div>
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Total Income</div>
                <div class="currency" style="font-size: 1.35rem; font-weight: 800; margin-top: 0.25rem;">₹{{ number_format($totalGross + $totalIncentives, 2) }}</div>
                <div style="font-size: 0.75rem; color: #10b981;">₹{{ number_format($totalIncentives, 2) }} incentives</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Total Expenses</div>
                <div class="currency" style="font-size: 1.35rem; font-weight: 800; color: #f87171; margin-top: 0.25rem;">₹{{ number_format($totalExpenses, 2) }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Fuel & deductions</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Net Take-Home</div>
                <div class="currency" style="font-size: 1.35rem; font-weight: 800; color: #34d399; margin-top: 0.25rem;">₹{{ number_format($totalNet, 2) }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">True pocket profit</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Avg Hourly Rate</div>
                <div class="currency" style="font-size: 1.35rem; font-weight: 800; color: #fbbf24; margin-top: 0.25rem;">₹{{ number_format($avgHourly, 2) }}/hr</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">{{ number_format($totalHours, 1) }} total hours</div>
            </div>
        </div>
    </div>

    <!-- Shifts List Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Shift Records</h3>
            <span style="font-size: 0.85rem; color: var(--gray-600);">{{ $entries->total() }} recorded shift(s)</span>
        </div>

        @if($entries->isEmpty())
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p style="font-weight: 600; color: var(--gray-700);">No earnings logged yet.</p>
                <p style="font-size: 0.85rem; margin-top: 0.25rem;">Log your shift details today to track your hourly rates and let Gemini AI explain your payout breakdown.</p>
                <a href="{{ route('earnings.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Record Shift Now
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <caption class="sr-only">Detailed list of recorded gig shifts with financial breakdown</caption>
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Total Income</th>
                            <th scope="col">Total Expenses</th>
                            <th scope="col">Net Profit</th>
                            <th scope="col">Hours</th>
                            <th scope="col">Rate (₹/hr)</th>
                            <th scope="col"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            <tr>
                                <td>
                                    <strong>{{ $entry->work_date->format('d M Y') }}</strong>
                                    @if($entry->notes)
                                        <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $entry->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="currency">₹{{ number_format($entry->total_income, 2) }}</span>
                                    <div style="font-size: 0.7rem; color: var(--gray-600);">Gross: ₹{{ number_format($entry->gross_earnings, 2) }}</div>
                                </td>
                                <td>
                                    <span class="currency" style="color: var(--danger);">₹{{ number_format($entry->total_expenses, 2) }}</span>
                                    <div style="font-size: 0.7rem; color: var(--gray-600);">Fuel: ₹{{ number_format($entry->fuel_expenses, 2) }} &bull; Ded: ₹{{ number_format($entry->platform_deductions, 2) }}</div>
                                </td>
                                <td>
                                    <strong class="currency" style="color: #059669; font-size: 1.05rem;">
                                        ₹{{ number_format($entry->net_earnings, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span>{{ $entry->hours_worked }} hrs</span>
                                </td>
                                <td>
                                    <span class="badge {{ $entry->effective_hourly_earnings >= 100 ? 'badge-success' : 'badge-warning' }}">
                                        ₹{{ number_format($entry->effective_hourly_earnings, 2) }}/hr
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.4rem;">
                                        <a href="{{ route('earnings.show', $entry) }}" class="btn btn-secondary btn-sm" aria-label="View details and AI breakdown for {{ $entry->work_date->format('d M Y') }}">
                                            Breakdown
                                        </a>
                                        <form method="POST" action="{{ route('earnings.destroy', $entry) }}" onsubmit="return confirm('Delete shift from {{ $entry->work_date->format('d M Y') }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: #fecaca;" aria-label="Delete shift record from {{ $entry->work_date->format('d M Y') }}">
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

            <nav aria-label="Earnings pagination" style="margin-top: 1.25rem;">
                {{ $entries->links() }}
            </nav>
        @endif
    </div>
</div>
@endsection
