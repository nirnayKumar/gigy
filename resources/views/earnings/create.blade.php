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
            <form method="POST" action="{{ route('earnings.store') }}" id="earningsForm">
                @csrf

                <div class="form-group">
                    <label for="work_date" class="form-label">Shift Date *</label>
                    <input id="work_date" type="date" name="work_date" class="form-control" value="{{ old('work_date', date('Y-m-d')) }}" required>
                    @error('work_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="gross_earnings" class="form-label">Gross Earnings (₹) *</label>
                        <input id="gross_earnings" type="number" step="0.01" min="0" name="gross_earnings" class="form-control calc-trigger" value="{{ old('gross_earnings', '1000.00') }}" placeholder="1000.00" required>
                        <div class="form-text">Base trip/order payouts</div>
                        @error('gross_earnings')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="incentives" class="form-label">Incentives / Bonuses (₹)</label>
                        <input id="incentives" type="number" step="0.01" min="0" name="incentives" class="form-control calc-trigger" value="{{ old('incentives', '100.00') }}" placeholder="0.00">
                        <div class="form-text">Surge, rain bonus, target incentives</div>
                        @error('incentives')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="fuel_expenses" class="form-label">Fuel Expenses (₹)</label>
                        <input id="fuel_expenses" type="number" step="0.01" min="0" name="fuel_expenses" class="form-control calc-trigger" value="{{ old('fuel_expenses', '150.00') }}" placeholder="0.00">
                        <div class="form-text">Petrol / Diesel / EV Charging</div>
                        @error('fuel_expenses')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="other_expenses" class="form-label">Other Expenses (₹)</label>
                        <input id="other_expenses" type="number" step="0.01" min="0" name="other_expenses" class="form-control calc-trigger" value="{{ old('other_expenses', '0.00') }}" placeholder="0.00">
                        <div class="form-text">Puncture, phone data, tea/meals</div>
                        @error('other_expenses')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="platform_deductions" class="form-label">Platform Deductions (₹)</label>
                        <input id="platform_deductions" type="number" step="0.01" min="0" name="platform_deductions" class="form-control calc-trigger" value="{{ old('platform_deductions', '50.00') }}" placeholder="0.00">
                        <div class="form-text">Penalties, bag deposit, insurance fees</div>
                        @error('platform_deductions')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hours_worked" class="form-label">Hours Worked *</label>
                        <input id="hours_worked" type="number" step="0.25" min="1" max="24" name="hours_worked" class="form-control calc-trigger" value="{{ old('hours_worked', '8.0') }}" placeholder="8.0" required>
                        <div class="form-text">Total shift logged-in duration</div>
                        @error('hours_worked')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Shift Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="e.g. Rainy peak shift, 2 wrong customer delivery penalties, long waiting time at restaurant...">{{ old('notes') }}</textarea>
                    <div class="form-text">Notes will be provided to Gemini AI when requesting an explanation or complaint draft.</div>
                    @error('notes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1rem;">
                    Save Shift & Calculate Breakdown &rarr;
                </button>
            </form>
        </div>

        <!-- Live Calculation Preview Sidebar -->
        <div>
            <div class="card" style="background: #ffffff; border: 2px solid #a7f3d0; position: sticky; top: 85px;">
                <div class="card-header" style="border-bottom-color: #d1fae5;">
                    <h3 class="card-title" style="color: #065f46; font-size: 1rem;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Live Shift Summary
                    </h3>
                    <span class="badge badge-success">Auto-calculated</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                        <span style="color: var(--gray-600);">Total Income:</span>
                        <strong class="currency" id="previewTotalIncome" style="color: #059669;">₹1,100.00</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                        <span style="color: var(--gray-600);">Total Expenses:</span>
                        <strong class="currency" id="previewTotalExpenses" style="color: var(--danger);">-₹200.00</strong>
                    </div>

                    <hr style="border: none; border-top: 1px dashed var(--gray-200); margin: 0.25rem 0;">

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: var(--gray-900);">Net Take-Home:</span>
                        <strong class="currency" id="previewNetEarnings" style="font-size: 1.35rem; color: #059669;">₹900.00</strong>
                    </div>

                    <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-sm); padding: 0.65rem 0.85rem; margin-top: 0.25rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                            <span style="color: var(--gray-600);">Effective Hourly Return:</span>
                            <strong class="currency" id="previewHourlyEarnings" style="font-weight: 800; color: #0f172a;">₹112.50 / hr</strong>
                        </div>
                    </div>

                    <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.5rem; line-height: 1.4;">
                        ✓ Server recalculates and persists certified figures in MySQL upon saving.
                    </div>
                </div>
            </div>
        </div>
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

    // Run on page load
    updateLivePreview();
</script>
@endpush
