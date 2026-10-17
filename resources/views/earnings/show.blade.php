@extends('layouts.app')

@section('title', 'Shift Breakdown - ' . $entry->work_date->format('d M Y'))

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Shift Breakdown</h1>
                <span class="badge badge-neutral">{{ $entry->work_date->format('d M Y') }}</span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Certified server-calculated payout metrics and AI financial breakdown.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('earnings.index') }}" class="btn btn-secondary btn-sm">&larr; All Shifts</a>
            <a href="{{ route('complaints.create', ['earnings_entry_id' => $entry->id]) }}" class="btn btn-secondary btn-sm" style="border-color: #f59e0b; color: #b45309;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Dispute Deduction
            </a>
        </div>
    </div>

    <!-- 4 Key Numbers Grid -->
    <div class="metric-grid">
        <div class="metric-card" style="border-top: 4px solid #059669;">
            <div class="metric-label">Total Income</div>
            <div class="metric-value currency" style="color: #059669;">₹{{ number_format($entry->total_income, 2) }}</div>
            <div class="metric-sub">Gross ₹{{ number_format($entry->gross_earnings, 2) }} + Inc ₹{{ number_format($entry->incentives, 2) }}</div>
        </div>

        <div class="metric-card" style="border-top: 4px solid var(--danger);">
            <div class="metric-label">Total Expenses</div>
            <div class="metric-value currency" style="color: var(--danger);">-₹{{ number_format($entry->total_expenses, 2) }}</div>
            <div class="metric-sub">Fuel ₹{{ number_format($entry->fuel_expenses, 2) }} + Ded ₹{{ number_format($entry->platform_deductions, 2) }}</div>
        </div>

        <div class="metric-card" style="border-top: 4px solid #047857;">
            <div class="metric-label">Net Take-Home</div>
            <div class="metric-value currency" style="font-size: 1.6rem; color: #047857;">₹{{ number_format($entry->net_earnings, 2) }}</div>
            <div class="metric-sub">Pocket profit after all costs</div>
        </div>

        <div class="metric-card" style="border-top: 4px solid #f59e0b;">
            <div class="metric-label">Hourly Return</div>
            <div class="metric-value currency" style="color: #d97706;">₹{{ number_format($entry->effective_hourly_earnings, 2) }} <span style="font-size: 0.85rem;">/hr</span></div>
            <div class="metric-sub">Worked {{ $entry->hours_worked }} hours</div>
        </div>
    </div>

    <!-- Itemized Ledger Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Itemized Shift Ledger</h3>
            <span class="badge badge-success">Verified Calculations</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td style="width: 50%; font-weight: 600;">Gross Base Earnings</td>
                        <td class="currency" style="text-align: right;">₹{{ number_format($entry->gross_earnings, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600; color: #059669;">+ Incentives & Bonuses</td>
                        <td class="currency" style="text-align: right; color: #059669;">+₹{{ number_format($entry->incentives, 2) }}</td>
                    </tr>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td>= Total Shift Income</td>
                        <td class="currency" style="text-align: right;">₹{{ number_format($entry->total_income, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--danger);">- Fuel Expenses (Petrol/Diesel/EV)</td>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->fuel_expenses, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--danger);">- Other Expenses (Punctures, Food, Maintenance)</td>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->other_expenses, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--danger);">- Platform Deductions & Penalties</td>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->platform_deductions, 2) }}</td>
                    </tr>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td>= Total Shift Expenses</td>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->total_expenses, 2) }}</td>
                    </tr>
                    <tr style="background: #ecfdf5; font-size: 1.05rem; font-weight: 800;">
                        <td style="color: #065f46;">= Net Take-Home Earnings</td>
                        <td class="currency" style="text-align: right; color: #065f46;">₹{{ number_format($entry->net_earnings, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Shift Hours Logged</td>
                        <td style="text-align: right; font-weight: 700;">{{ $entry->hours_worked }} hours</td>
                    </tr>
                    <tr style="font-size: 1rem; font-weight: 800;">
                        <td>Effective Hourly Return (Net / Hours)</td>
                        <td class="currency" style="text-align: right; color: #b45309;">₹{{ number_format($entry->effective_hourly_earnings, 2) }} / hour</td>
                    </tr>
                    @if($entry->notes)
                    <tr>
                        <td style="font-weight: 600;">Worker Notes</td>
                        <td style="text-align: right; color: var(--gray-700);">{{ $entry->notes }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Feature A: Explain My Earnings with AI -->
    <div class="card" style="border: 2px solid #10b981; background: #ffffff;">
        <div class="card-header" style="background: #f0fdf4; margin: -1.25rem -1.25rem 1.25rem -1.25rem; padding: 1rem 1.25rem; border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg);">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.35rem;">✨</span>
                <div>
                    <h3 class="card-title" style="color: #065f46; margin-bottom: 0;">Explain My Earnings with Gemini AI</h3>
                    <div style="font-size: 0.775rem; color: #047857;">Instant analysis in simple English, Hindi (हिंदी), or Punjabi (ਪੰਜਾਬੀ)</div>
                </div>
            </div>
            <span class="badge badge-success">Gemini Powered</span>
        </div>

        <p style="font-size: 0.875rem; color: var(--gray-700); margin-bottom: 1.25rem;">
            Click below to have Google Gemini evaluate this shift. Gigy will explain your deductions, check if your net hourly rate justifies fuel spent, identify possible discrepancies, and recommend practical next steps.
        </p>

        <!-- Language Selection Buttons Form -->
        <form method="POST" action="{{ route('earnings.explain', $entry) }}" id="explainForm" style="margin-bottom: 1rem;">
            @csrf
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--gray-700);">Select Language:</span>
                <button type="submit" name="language" value="English" class="btn btn-sm {{ ($activeLanguage ?? 'English') === 'English' ? 'btn-primary' : 'btn-secondary' }}">
                    🇬🇧 English
                </button>
                <button type="submit" name="language" value="Hindi" class="btn btn-sm {{ ($activeLanguage ?? '') === 'Hindi' ? 'btn-primary' : 'btn-secondary' }}">
                    🇮🇳 Hindi (हिंदी)
                </button>
                <button type="submit" name="language" value="Punjabi" class="btn btn-sm {{ ($activeLanguage ?? '') === 'Punjabi' ? 'btn-primary' : 'btn-secondary' }}">
                    🇮🇳 Punjabi (ਪੰਜਾਬੀ)
                </button>
            </div>
        </form>

        <!-- AI Output Box -->
        @if(!empty($explanation))
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: var(--radius-md); padding: 1.25rem; margin-top: 1rem; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #065f46; font-size: 0.85rem;">
                        <span>🤖</span> Gigy AI Shift Analysis ({{ $activeLanguage ?? 'English' }})
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('explanationContent').innerText); alert('Analysis copied to clipboard!');">
                        📋 Copy
                    </button>
                </div>
                <div id="explanationContent" style="font-size: 0.925rem; color: #1e293b; line-height: 1.7; white-space: pre-wrap;">{{ $explanation }}</div>

                <div style="margin-top: 1.25rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <span style="font-size: 0.75rem; color: var(--gray-600);">
                        ⚠️ AI distinguishes facts from platform assumptions. Never claims to have contacted the platform directly.
                    </span>
                    <a href="{{ route('complaints.create', ['earnings_entry_id' => $entry->id]) }}" class="btn btn-secondary btn-sm" style="color: #b45309; border-color: #f59e0b;">
                        Draft Complaint Based on this Analysis &rarr;
                    </a>
                </div>
            </div>
        @else
            <div style="background: var(--gray-50); border: 1px dashed var(--gray-300); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; color: var(--gray-600); font-size: 0.875rem;">
                Click any language above to get an instant AI evaluation of this shift.
            </div>
        @endif
    </div>

    <!-- Shift Actions Footer -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
        <a href="{{ route('earnings.index') }}" class="btn btn-secondary">&larr; Back to All Shifts</a>

        <form method="POST" action="{{ route('earnings.destroy', $entry) }}" onsubmit="return confirm('Are you sure you want to delete this earnings record?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete Shift</button>
        </form>
    </div>
</div>
@endsection
