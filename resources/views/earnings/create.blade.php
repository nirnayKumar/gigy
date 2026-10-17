@extends('layouts.app')

@section('title', 'Record Shift Earnings')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Record Shift Earnings</h1>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
                Enter shift details to calculate net take-home pay and hourly returns.
            </p>
        </div>
        <a href="{{ route('earnings.index') }}" class="btn btn-secondary btn-sm">&larr; Back to History</a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width:768px){grid-template-columns: 3fr 2fr;}">
        <!-- Form Section -->
        <div class="card">
            <form method="POST" action="{{ route('earnings.store') }}" id="earningsForm" novalidate>
                @csrf

                <div class="form-group">
                    <label for="work_date" class="form-label">Shift Date <span aria-hidden="true">*</span></label>
                    <input id="work_date" type="date" name="work_date" class="form-control" value="{{ old('work_date', date('Y-m-d')) }}" required aria-required="true" @error('work_date') aria-invalid="true" aria-describedby="work_date_error" @enderror>
                    @error('work_date')
                        <div id="work_date_error" class="form-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="gross_earnings" class="form-label">Gross Earnings (₹) <span aria-hidden="true">*</span></label>
                        <input id="gross_earnings" type="number" step="0.01" min="0" name="gross_earnings" class="form-control calc-trigger" value="{{ old('gross_earnings', '1000.00') }}" placeholder="1000.00" required aria-required="true" aria-describedby="gross_earnings_help @error('gross_earnings') gross_earnings_error @enderror" @error('gross_earnings') aria-invalid="true" @enderror>
                        <div id="gross_earnings_help" class="form-text">Base trip & order payouts</div>
                        @error('gross_earnings')
                            <div id="gross_earnings_error" class="form-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="incentives" class="form-label">Incentives / Bonuses (₹)</label>
                        <input id="incentives" type="number" step="0.01" min="0" name="incentives" class="form-control calc-trigger" value="{{ old('incentives', '100.00') }}" placeholder="0.00" aria-describedby="incentives_help @error('incentives') incentives_error @enderror" @error('incentives') aria-invalid="true" @enderror>
                        <div id="incentives_help" class="form-text">Surge, rain bonus, targets</div>
                        @error('incentives')
                            <div id="incentives_error" class="form-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="fuel_expenses" class="form-label">Fuel Expenses (₹)</label>
                        <input id="fuel_expenses" type="number" step="0.01" min="0" name="fuel_expenses" class="form-control calc-trigger" value="{{ old('fuel_expenses', '150.00') }}" placeholder="0.00" aria-describedby="fuel_help @error('fuel_expenses') fuel_error @enderror" @error('fuel_expenses') aria-invalid="true" @enderror>
                        <div id="fuel_help" class="form-text">Petrol / Diesel / EV Charging</div>
                        @error('fuel_expenses')
                            <div id="fuel_error" class="form-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="other_expenses" class="form-label">Other Expenses (₹)</label>
                        <input id="other_expenses" type="number" step="0.01" min="0" name="other_expenses" class="form-control calc-trigger" value="{{ old('other_expenses', '0.00') }}" placeholder="0.00" aria-describedby="other_help @error('other_expenses') other_error @enderror" @error('other_expenses') aria-invalid="true" @enderror>
                        <div id="other_help" class="form-text">Puncture, phone data, meals</div>
                        @error('other_expenses')
                            <div id="other_error" class="form-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="platform_deductions" class="form-label">Platform Deductions (₹)</label>
                        <input id="platform_deductions" type="number" step="0.01" min="0" name="platform_deductions" class="form-control calc-trigger" value="{{ old('platform_deductions', '50.00') }}" placeholder="0.00" aria-describedby="deductions_help @error('platform_deductions') deductions_error @enderror" @error('platform_deductions') aria-invalid="true" @enderror>
                        <div id="deductions_help" class="form-text">Penalties, bag deposit, fees</div>
                        @error('platform_deductions')
                            <div id="deductions_error" class="form-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hours_worked" class="form-label">Hours Worked <span aria-hidden="true">*</span></label>
                        <input id="hours_worked" type="number" step="0.25" min="0.1" max="24" name="hours_worked" class="form-control calc-trigger" value="{{ old('hours_worked', '8.0') }}" placeholder="8.0" required aria-required="true" aria-describedby="hours_help @error('hours_worked') hours_error @enderror" @error('hours_worked') aria-invalid="true" @enderror>
                        <div id="hours_help" class="form-text">Logged-in shift duration</div>
                        @error('hours_worked')
                            <div id="hours_error" class="form-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Shift Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="e.g. Rainy peak shift, 2 wrong customer delivery penalties, long waiting time..." aria-describedby="notes_help @error('notes') notes_error @enderror" @error('notes') aria-invalid="true" @enderror>{{ old('notes') }}</textarea>
                    <div id="notes_help" class="form-text">Notes will be provided to Gemini AI when requesting an explanation or complaint draft.</div>
                    @error('notes')
                        <div id="notes_error" class="form-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1rem;">
                    Save Shift & Calculate Breakdown &rarr;
                </button>
            </form>
        </div>

        <!-- Live Calculation Preview Sidebar with ARIA live region -->
        <aside aria-label="Live Shift Summary Sidebar">
            <div class="card" style="background: #ffffff; border: 2px solid #a7f3d0; position: sticky; top: 85px;" role="region" aria-label="Real-time shift totals" aria-live="polite">
                <div class="card-header" style="border-bottom-color: #d1fae5;">
                    <h2 class="card-title" style="color: #064e3b; font-size: 1.05rem;">
                        <svg aria-hidden="true" focusable="false" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Live Shift Summary
                    </h2>
                    <span class="badge badge-success">Auto-calculated</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                        <span style="color: var(--gray-600);">Total Income:</span>
                        <strong class="currency" id="previewTotalIncome" style="color: #047857;">₹1,100.00</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                        <span style="color: var(--gray-600);">Total Expenses:</span>
                        <strong class="currency" id="previewTotalExpenses" style="color: var(--danger);">-₹200.00</strong>
                    </div>

                    <hr style="border: none; border-top: 1px dashed var(--gray-200); margin: 0.25rem 0;" aria-hidden="true">

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: var(--gray-900);">Net Take-Home:</span>
                        <strong class="currency" id="previewNetEarnings" style="font-size: 1.35rem; color: #047857;">₹900.00</strong>
                    </div>

                    <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-sm); padding: 0.65rem 0.85rem; margin-top: 0.25rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem;">
                            <span style="color: var(--gray-600);">Effective Hourly Return:</span>
                            <strong class="currency" id="previewHourlyEarnings" style="font-weight: 800; color: #0f172a;">₹112.50 / hr</strong>
                        </div>
                    </div>

                    <div style="font-size: 0.775rem; color: var(--gray-600); margin-top: 0.5rem; line-height: 1.4;">
                        ✓ Server recalculates and persists certified figures in MySQL upon saving.
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateLivePreview() {
        const gross = parseFloat(document.getElementById('gross_earnings').value) || 0;
        const inc = parseFloat(document.getElementById('incentives').value) || 0;
        const fuel = parseFloat(document.getElementById('fuel_expenses').value) || 0;
        const other = parseFloat(document.getElementById('other_expenses').value) || 0;
        const ded = parseFloat(document.getElementById('platform_deductions').value) || 0;
        const hours = parseFloat(document.getElementById('hours_worked').value) || 0;

        const totalIncome = gross + inc;
        const totalExpenses = fuel + other + ded;
        const netEarnings = totalIncome - totalExpenses;
        const hourly = hours > 0 ? (netEarnings / hours) : 0;

        document.getElementById('previewTotalIncome').innerText = '₹' + totalIncome.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewTotalExpenses').innerText = '-₹' + totalExpenses.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewNetEarnings').innerText = '₹' + netEarnings.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewHourlyEarnings').innerText = '₹' + hourly.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / hr';
    }

    document.querySelectorAll('.calc-trigger').forEach(input => {
        input.addEventListener('input', updateLivePreview);
    });

    // Run on load
    updateLivePreview();
</script>
@endpush
