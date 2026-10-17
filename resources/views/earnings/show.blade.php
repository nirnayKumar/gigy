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
            <a href="{{ route('complaints.create', ['earnings_entry_id' => $entry->id]) }}" class="btn btn-secondary btn-sm" style="border-color: #f59e0b; color: #b45309;" aria-label="Dispute deduction for this shift">
                <svg aria-hidden="true" focusable="false" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Dispute Deduction
            </a>
        </div>
    </div>

    <!-- 4 Key Numbers Grid -->
    <section aria-label="Shift Summary Metrics" class="metric-grid">
        <article class="metric-card" style="border-top: 4px solid #047857;">
            <h2 class="metric-label">Total Income</h2>
            <div class="metric-value currency" style="color: #047857;">₹{{ number_format($entry->total_income, 2) }}</div>
            <div class="metric-sub">Gross ₹{{ number_format($entry->gross_earnings, 2) }} + Inc ₹{{ number_format($entry->incentives, 2) }}</div>
        </article>

        <article class="metric-card" style="border-top: 4px solid var(--danger);">
            <h2 class="metric-label">Total Expenses</h2>
            <div class="metric-value currency" style="color: var(--danger);">-₹{{ number_format($entry->total_expenses, 2) }}</div>
            <div class="metric-sub">Fuel ₹{{ number_format($entry->fuel_expenses, 2) }} + Ded ₹{{ number_format($entry->platform_deductions, 2) }}</div>
        </article>

        <article class="metric-card" style="border-top: 4px solid #065f46;">
            <h2 class="metric-label">Net Take-Home</h2>
            <div class="metric-value currency" style="font-size: 1.6rem; color: #065f46;">₹{{ number_format($entry->net_earnings, 2) }}</div>
            <div class="metric-sub">Pocket profit after all costs</div>
        </article>

        <article class="metric-card" style="border-top: 4px solid #b45309;">
            <h2 class="metric-label">Hourly Return</h2>
            <div class="metric-value currency" style="color: #b45309;">₹{{ number_format($entry->effective_hourly_earnings, 2) }} <span style="font-size: 0.85rem;">/hr</span></div>
            <div class="metric-sub">Worked {{ $entry->hours_worked }} hours</div>
        </article>
    </section>

    <!-- Itemized Ledger Table -->
    <section aria-labelledby="ledger-heading" class="card">
        <div class="card-header">
            <h2 id="ledger-heading" class="card-title">Itemized Shift Ledger</h2>
            <span class="badge badge-success">Verified Calculations</span>
        </div>

        <div class="table-responsive">
            <table class="table" aria-label="Itemized Shift Breakdown Table">
                <caption class="sr-only">Detailed breakdown of gross earnings, incentives, fuel expenses, and platform deductions for {{ $entry->work_date->format('d M Y') }}</caption>
                <tbody>
                    <tr>
                        <th scope="row" style="width: 50%; font-weight: 600; text-align: left;">Gross Base Earnings</th>
                        <td class="currency" style="text-align: right;">₹{{ number_format($entry->gross_earnings, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="font-weight: 600; color: #047857; text-align: left;">+ Incentives & Bonuses</th>
                        <td class="currency" style="text-align: right; color: #047857;">+₹{{ number_format($entry->incentives, 2) }}</td>
                    </tr>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <th scope="row" style="text-align: left;">= Total Shift Income</th>
                        <td class="currency" style="text-align: right;">₹{{ number_format($entry->total_income, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="color: var(--danger); text-align: left;">- Fuel Expenses (Petrol/Diesel/EV)</th>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->fuel_expenses, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="color: var(--danger); text-align: left;">- Other Expenses (Punctures, Meals, Maintenance)</th>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->other_expenses, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="color: var(--danger); text-align: left;">- Platform Deductions & Penalties</th>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->platform_deductions, 2) }}</td>
                    </tr>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <th scope="row" style="text-align: left;">= Total Shift Expenses</th>
                        <td class="currency" style="text-align: right; color: var(--danger);">-₹{{ number_format($entry->total_expenses, 2) }}</td>
                    </tr>
                    <tr style="background: #ecfdf5; font-size: 1.05rem; font-weight: 800;">
                        <th scope="row" style="color: #064e3b; text-align: left;">= Net Take-Home Earnings</th>
                        <td class="currency" style="text-align: right; color: #064e3b;">₹{{ number_format($entry->net_earnings, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="font-weight: 600; text-align: left;">Shift Hours Logged</th>
                        <td style="text-align: right; font-weight: 700;">{{ $entry->hours_worked }} hours</td>
                    </tr>
                    <tr style="font-size: 1rem; font-weight: 800;">
                        <th scope="row" style="text-align: left;">Effective Hourly Return (Net / Hours)</th>
                        <td class="currency" style="text-align: right; color: #b45309;">₹{{ number_format($entry->effective_hourly_earnings, 2) }} / hour</td>
                    </tr>
                    @if($entry->notes)
                    <tr>
                        <th scope="row" style="font-weight: 600; text-align: left;">Worker Notes</th>
                        <td style="text-align: right; color: var(--gray-700);">{{ $entry->notes }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>

    <!-- AI Feature A: Explain My Earnings with AI -->
    <section aria-labelledby="ai-explainer-heading" class="card" style="border: 2px solid #047857; background: #ffffff;">
        <div class="card-header" style="background: #f0fdf4; margin: -1.25rem -1.25rem 1.25rem -1.25rem; padding: 1rem 1.25rem; border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg);">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span aria-hidden="true" style="font-size: 1.35rem;">✨</span>
                <div>
                    <h2 id="ai-explainer-heading" class="card-title" style="color: #064e3b; margin-bottom: 0;">Explain My Earnings with Gemini AI</h2>
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
            <fieldset style="border: none; padding: 0; margin: 0;">
                <legend class="sr-only">Choose language for shift explanation</legend>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--gray-700);">Select Language:</span>
                    <button type="submit" name="language" value="English" class="btn btn-sm {{ ($activeLanguage ?? 'English') === 'English' ? 'btn-primary' : 'btn-secondary' }}" aria-pressed="{{ ($activeLanguage ?? 'English') === 'English' ? 'true' : 'false' }}">
                        🇬🇧 English
                    </button>
                    <button type="submit" name="language" value="Hindi" class="btn btn-sm {{ ($activeLanguage ?? '') === 'Hindi' ? 'btn-primary' : 'btn-secondary' }}" aria-pressed="{{ ($activeLanguage ?? '') === 'Hindi' ? 'true' : 'false' }}">
                        🇮🇳 Hindi (हिंदी)
                    </button>
                    <button type="submit" name="language" value="Punjabi" class="btn btn-sm {{ ($activeLanguage ?? '') === 'Punjabi' ? 'btn-primary' : 'btn-secondary' }}" aria-pressed="{{ ($activeLanguage ?? '') === 'Punjabi' ? 'true' : 'false' }}">
                        🇮🇳 Punjabi (ਪੰਜਾਬੀ)
                    </button>
                </div>
            </fieldset>
        </form>

        <!-- AI Output Box with accessible Live Region -->
        @if(!empty($explanation))
            <div role="region" aria-label="AI Earnings Explanation" aria-live="polite" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: var(--radius-md); padding: 1.25rem; margin-top: 1rem; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #064e3b; font-size: 0.875rem;">
                        <span aria-hidden="true">🤖</span> Gigy AI Shift Analysis ({{ $activeLanguage ?? 'English' }})
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('explanationContent').innerText); alert('Analysis copied to clipboard!');" aria-label="Copy AI explanation text">
                        📋 Copy Text
                    </button>
                </div>
                <div id="explanationContent" style="font-size: 0.925rem; color: #0f172a; line-height: 1.7; white-space: pre-wrap;">{{ $explanation }}</div>

                <div style="margin-top: 1.25rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <span style="font-size: 0.775rem; color: var(--gray-600);">
                        ⚠️ AI distinguishes facts from platform assumptions. Never claims to have contacted the platform directly.
                    </span>
                    <a href="{{ route('complaints.create', ['earnings_entry_id' => $entry->id]) }}" class="btn btn-secondary btn-sm" style="color: #b45309; border-color: #f59e0b;" aria-label="Draft a grievance complaint using this shift analysis">
                        Draft Complaint Based on this Analysis &rarr;
                    </a>
                </div>
            </div>
        @else
            <div style="background: var(--gray-50); border: 1px dashed var(--gray-300); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; color: var(--gray-600); font-size: 0.875rem;">
                Click any language above to get an instant AI evaluation of this shift.
            </div>
        @endif
    </section>

    <!-- Shift Actions Footer -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
        <a href="{{ route('earnings.index') }}" class="btn btn-secondary">&larr; Back to All Shifts</a>

        <form method="POST" action="{{ route('earnings.destroy', $entry) }}" onsubmit="return confirm('Are you sure you want to delete this earnings record?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" aria-label="Delete this shift record">Delete Shift</button>
        </form>
    </div>
</div>
@endsection
