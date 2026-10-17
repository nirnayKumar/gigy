@extends('layouts.app')

@section('title', 'Gig Worker Intelligence & Performance Analytics')

@section('content')
<div>
    <!-- Top Header & Action Controls -->
    <div class="page-header">
        <div class="page-header-title-wrap">
            <div class="page-header-title">
                <span>Intelligence & Analytics</span>
                <span class="badge badge-info" style="font-size: 0.75rem; vertical-align: middle;">Gigy 2.0</span>
                @if($kpis['is_using_demo_fallback'])
                    <span class="badge badge-warning" style="font-size: 0.725rem;" title="Showing benchmark demo performance for initial onboarding evaluation">
                        Demo Sandbox Mode
                    </span>
                @endif
            </div>
            <p class="page-header-subtitle">
                Cross-platform earnings intelligence, delivery efficiency, and community benchmark comparison.
            </p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('analytics.export', request()->all()) }}" class="btn btn-secondary btn-sm" aria-label="Export analytics report to CSV">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export CSV</span>
            </a>
            <a href="{{ route('earnings.create') }}" class="btn btn-primary btn-sm">
                <span>+ Log Shift</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('analytics.index') }}" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
            <!-- Range Quick Filters -->
            <div>
                <label for="rangeSelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Time Period</label>
                <select id="rangeSelect" name="range" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;" onchange="toggleCustomDates(this.value)">
                    <option value="today" {{ $filters['range'] === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="7d" {{ $filters['range'] === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30d" {{ $filters['range'] === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90d" {{ $filters['range'] === '90d' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="custom" {{ $filters['range'] === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
            </div>

            <!-- Custom Dates (hidden unless custom selected) -->
            <div id="customDatesWrap" style="display: {{ $filters['range'] === 'custom' ? 'flex' : 'none' }}; gap: 0.5rem;">
                <div>
                    <label for="fromInput" class="form-label" style="font-size: 0.775rem;">From</label>
                    <input id="fromInput" type="date" name="from" value="{{ $filters['from'] }}" class="form-control" style="min-height: 38px; padding: 0.4rem 0.5rem; font-size: 0.85rem;">
                </div>
                <div>
                    <label for="toInput" class="form-label" style="font-size: 0.775rem;">To</label>
                    <input id="toInput" type="date" name="to" value="{{ $filters['to'] }}" class="form-control" style="min-height: 38px; padding: 0.4rem 0.5rem; font-size: 0.85rem;">
                </div>
            </div>

            <!-- City Filter -->
            <div>
                <label for="citySelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">City</label>
                <select id="citySelect" name="city" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ $filters['city'] === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Company Filter -->
            <div>
                <label for="compSelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Platform</label>
                <select id="compSelect" name="company" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    <option value="All">All Platforms</option>
                    @foreach($companies as $cmp)
                        <option value="{{ $cmp->name }}" {{ $filters['company'] === $cmp->name ? 'selected' : '' }}>{{ $cmp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role Filter -->
            <div>
                <label for="roleSelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Worker Role</label>
                <select id="roleSelect" name="role" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    @foreach($roles as $r)
                        <option value="{{ $r }}" {{ $filters['role'] === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="min-height: 38px;">Apply Filters</button>
            <a href="{{ route('analytics.index') }}" class="btn btn-secondary btn-sm" style="min-height: 38px;">Reset</a>
        </form>
    </div>

    <!-- Top-Level KPI Metric Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <!-- Card 1: Total Net Earnings -->
        <div class="card" style="margin-bottom: 0; border-left: 4px solid #059669;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="metric-label">Net Take-Home ({{ $filters['label'] }})</span>
                <span style="font-size: 1.25rem;">💰</span>
            </div>
            <div class="metric-value currency" style="color: #059669; margin-top: 0.25rem;">₹{{ number_format($kpis['net_earnings'], 2) }}</div>
            <div class="metric-sub" style="display: flex; align-items: center; gap: 0.4rem;">
                @if($kpis['net_change_percent'] >= 0)
                    <span style="color: #059669; font-weight: 700;">↑ {{ $kpis['net_change_percent'] }}%</span>
                @else
                    <span style="color: var(--danger); font-weight: 700;">↓ {{ abs($kpis['net_change_percent']) }}%</span>
                @endif
                <span>vs previous period</span>
            </div>
        </div>

        <!-- Card 2: Hourly Rate -->
        <div class="card" style="margin-bottom: 0; border-left: 4px solid #d97706;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="metric-label">Effective Hourly Rate</span>
                <span style="font-size: 1.25rem;">⏱️</span>
            </div>
            <div class="metric-value currency" style="color: #d97706; margin-top: 0.25rem;">₹{{ number_format($kpis['hourly_net'], 2) }}/hr</div>
            <div class="metric-sub">
                {{ number_format($kpis['total_hours'], 1) }} total work hours logged
            </div>
        </div>

        <!-- Card 3: Completed Deliveries -->
        <div class="card" style="margin-bottom: 0; border-left: 4px solid #2563eb;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="metric-label">Completed Deliveries</span>
                <span style="font-size: 1.25rem;">📦</span>
            </div>
            <div class="metric-value" style="color: #2563eb; margin-top: 0.25rem;">{{ number_format($kpis['total_deliveries']) }}</div>
            <div class="metric-sub">
                Avg ₹{{ number_format($kpis['earnings_per_delivery'], 2) }} per trip
            </div>
        </div>

        <!-- Card 4: Expense Ratio -->
        <div class="card" style="margin-bottom: 0; border-left: 4px solid {{ $kpis['expense_ratio'] > 25 ? 'var(--danger)' : '#059669' }};">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="metric-label">Expense Ratio (Fuel/Ded)</span>
                <span style="font-size: 1.25rem;">⛽</span>
            </div>
            <div class="metric-value" style="color: {{ $kpis['expense_ratio'] > 25 ? 'var(--danger)' : '#059669' }}; margin-top: 0.25rem;">{{ $kpis['expense_ratio'] }}%</div>
            <div class="metric-sub">
                ₹{{ number_format($kpis['total_expenses'], 2) }} total costs
            </div>
        </div>

        <!-- Card 5: Peer Benchmark Percentile -->
        <div class="card" style="margin-bottom: 0; border-left: 4px solid #8b5cf6;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="metric-label">Community Benchmark</span>
                <span style="font-size: 1.25rem;">🏆</span>
            </div>
            @if(!$benchmark['is_suppressed'])
                <div class="metric-value" style="color: #8b5cf6; margin-top: 0.25rem;">Top {{ 100 - $benchmark['worker']['percentile_rank'] }}%</div>
                <div class="metric-sub">
                    Cohort median: ₹{{ number_format($benchmark['median']['hourly_net'], 2) }}/hr
                </div>
            @else
                <div class="metric-value" style="font-size: 1.1rem; color: var(--gray-700); margin-top: 0.25rem;">Private Cohort</div>
                <div class="metric-sub">Broaden filters (&ge;10 peers)</div>
            @endif
        </div>
    </div>

    <!-- Active Earnings Goal Banner (if goal active) -->
    @if($activeGoal)
        <div class="card" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); color: white; border: none; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1; min-width: 250px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 1.25rem;">🎯</span>
                        <h2 style="font-size: 1.15rem; font-weight: 800; color: white;">Active {{ ucfirst($activeGoal->type) }} Goal: ₹{{ number_format($activeGoal->target_amount, 2) }}</h2>
                        <span class="badge" style="background: rgba(255,255,255,0.2); color: white; font-size: 0.7rem;">
                            {{ $activeGoal->start_date->format('d M') }} – {{ $activeGoal->end_date->format('d M') }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-top: 0.6rem; opacity: 0.9;">
                        <span>Achieved: <strong>₹{{ number_format($activeGoal->achieved_amount, 2) }}</strong> ({{ $activeGoal->progress_percent }}%)</span>
                        <span>Remaining: <strong>₹{{ number_format($activeGoal->remaining_amount, 2) }}</strong></span>
                    </div>
                    <!-- Accessible Progress Bar -->
                    <div role="progressbar" aria-valuenow="{{ $activeGoal->progress_percent }}" aria-valuemin="0" aria-valuemax="100" aria-label="Earnings goal progress" style="background: rgba(255,255,255,0.2); border-radius: 9999px; height: 10px; margin-top: 0.4rem; overflow: hidden;">
                        <div style="background: #34d399; height: 100%; width: {{ $activeGoal->progress_percent }}%; border-radius: 9999px; transition: width 0.4s ease;"></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('goals.destroy', $activeGoal) }}" onsubmit="return confirm('Archive current goal?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);">
                        Cancel / Reset Goal
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Quick Goal Setter Form Card -->
        <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; background: var(--gray-50); border: 1.5px dashed var(--gray-300);">
            <form method="POST" action="{{ route('goals.store') }}" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                @csrf
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.35rem;">🎯</span>
                    <div>
                        <strong style="color: var(--gray-900); font-size: 0.95rem;">Set a Target Take-Home Goal</strong>
                        <p style="font-size: 0.8rem; color: var(--gray-600);">Track your daily or weekly progress with pacing indicators.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    <select name="type" class="form-control" style="width: auto; min-height: 36px; padding: 0.35rem 0.6rem; font-size: 0.85rem;" aria-label="Goal Frequency">
                        <option value="weekly">Weekly Target</option>
                        <option value="daily">Daily Target</option>
                        <option value="monthly">Monthly Target</option>
                    </select>

                    <div style="position: relative;">
                        <span style="position: absolute; left: 10px; top: 8px; color: var(--gray-600); font-weight: 700;">₹</span>
                        <input type="number" step="100" min="500" name="target_amount" placeholder="Target (e.g. 7500)" required class="form-control" style="width: 160px; min-height: 36px; padding-left: 1.6rem; font-size: 0.85rem;" aria-label="Target Amount in Rupees">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="min-height: 36px;">Set Goal</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Main Visualizations Grid -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Chart 1: Earnings Over Time (Line Chart) -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <span>📈 Earnings & Take-Home Over Time</span>
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.15rem;">
                        Daily Gross, Deductions/Expenses, and Net Pocket Profit vs Peer Community Benchmark.
                    </p>
                </div>
                <div style="display: flex; gap: 0.4rem; font-size: 0.8rem;">
                    <span class="badge" style="background: #ecfdf5; color: #047857;">● Net Profit</span>
                    <span class="badge" style="background: #fef2f2; color: #b91c1c;">● Expenses</span>
                    <span class="badge" style="background: #f5f3ff; color: #7c3aed;">--- Community Median</span>
                </div>
            </div>
            <div style="height: 320px; position: relative;">
                <canvas id="earningsOverTimeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 2-Column Visualizations: Donut Breakdowns + Delivery Efficiency -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Donut: Income vs Expense Streams -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Income & Expense Structure</h3>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
                <div style="text-align: center;">
                    <div style="font-size: 0.775rem; font-weight: 700; color: var(--gray-700); margin-bottom: 0.4rem;">INCOME SOURCES</div>
                    <div style="height: 180px; position: relative;">
                        <canvas id="incomeDonutChart"></canvas>
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 0.775rem; font-weight: 700; color: var(--gray-700); margin-bottom: 0.4rem;">EXPENSE ALLOCATION</div>
                    <div style="height: 180px; position: relative;">
                        <canvas id="expenseDonutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Efficiency Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Delivery Efficiency Diagnostics</h3>
            </div>
            @if($efficiency['has_data'])
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                    <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                        <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Deliveries Per Hour</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-top: 0.2rem;">{{ $efficiency['deliveries_per_hour'] }} <span style="font-size: 0.9rem; font-weight: 500;">trips/hr</span></div>
                        <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">Speed of order turnaround</div>
                    </div>

                    <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                        <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Avg Trip Duration</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #2563eb; margin-top: 0.2rem;">{{ $efficiency['avg_time_minutes'] }} <span style="font-size: 0.9rem; font-weight: 500;">mins</span></div>
                        <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">Pickup to customer drop-off</div>
                    </div>

                    <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                        <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Completion Rate</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #059669; margin-top: 0.2rem;">{{ $efficiency['completion_rate'] }}%</div>
                        <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">{{ $efficiency['completed_count'] }} successful orders</div>
                    </div>

                    <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                        <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Cancellation Rate</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: {{ $efficiency['cancellation_rate'] > 5 ? 'var(--danger)' : '#10b981' }}; margin-top: 0.2rem;">{{ $efficiency['cancellation_rate'] }}%</div>
                        <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">Customer/store cancellations</div>
                    </div>
                </div>
            @else
                <div class="empty-state" style="padding: 2rem 1rem;">
                    <p style="font-weight: 600; color: var(--gray-700);">No individual delivery records logged yet.</p>
                    <p style="font-size: 0.85rem; margin-top: 0.25rem;">Trip efficiency metrics populate automatically as shift delivery counts are logged.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Hourly Earnings Heatmap -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <span>🔥 Hourly Earning Intensity Heatmap</span>
                </h3>
                <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.15rem;">
                    Historical earnings intensity by Day of Week & Hour of Day (₹/hour). Darker green highlights stronger incentive & demand windows.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--gray-600);">
                <span>Lower Rate</span>
                <span style="display: inline-block; width: 12px; height: 12px; background: #e2e8f0; border-radius: 2px;"></span>
                <span style="display: inline-block; width: 12px; height: 12px; background: #86efac; border-radius: 2px;"></span>
                <span style="display: inline-block; width: 12px; height: 12px; background: #22c55e; border-radius: 2px;"></span>
                <span style="display: inline-block; width: 12px; height: 12px; background: #15803d; border-radius: 2px;"></span>
                <span>Peak Surge</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" style="font-size: 0.75rem; text-align: center;">
                <caption class="sr-only">Day of week versus hour of day earnings intensity heatmap</caption>
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">Day</th>
                        @for($h = 0; $h < 24; $h++)
                            <th scope="col" style="padding: 4px; font-size: 0.7rem;">{{ sprintf('%02d', $h) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($heatmap['days'] as $dayIdx => $dayName)
                        <tr>
                            <td style="font-weight: 700; background: var(--gray-50); text-align: left; padding: 4px 8px;">{{ $dayName }}</td>
                            @for($h = 0; $h < 24; $h++)
                                @php
                                    $cell = $heatmap['matrix'][$dayIdx][$h];
                                    $rate = $cell['rate'];
                                    $count = $cell['count'];
                                    // Determine background color intensity
                                    if ($rate <= 0) {
                                        $bg = '#f8fafc';
                                        $text = '#94a3b8';
                                    } elseif ($rate < 120) {
                                        $bg = '#dcfce7';
                                        $text = '#166534';
                                    } elseif ($rate < 160) {
                                        $bg = '#86efac';
                                        $text = '#14532d';
                                    } elseif ($rate < 200) {
                                        $bg = '#22c55e';
                                        $text = '#ffffff';
                                    } else {
                                        $bg = '#15803d';
                                        $text = '#ffffff';
                                    }
                                @endphp
                                <td style="background: {{ $bg }}; color: {{ $text }}; padding: 6px 2px; font-weight: 600; border: 1px solid #ffffff;"
                                    title="{{ $dayName }} {{ sprintf('%02d:00', $h) }} - Avg ₹{{ number_format($rate, 2) }}/hr across {{ $count }} recorded session(s)">
                                    {{ $rate > 0 ? (int)$rate : '-' }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.5rem; text-align: right;">
            💡 <em>Note: Historical earnings represent observed past shifts and do not guarantee future earnings.</em>
        </p>
    </div>

    <!-- Community Benchmark Callout Banner -->
    <div class="card" style="border: 2px solid #8b5cf6; background: #faf5ff; padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">📊</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #5b21b6; margin-bottom: 0;">Community Peer Benchmark</h3>
                    <span class="badge" style="background: #ede9fe; color: #6d28d9;">Cohort Size: {{ $benchmark['cohort_size'] }} Workers</span>
                </div>
                <p style="font-size: 0.875rem; color: #4c1d95; margin-top: 0.35rem; line-height: 1.5;">
                    {{ $benchmark['context_statement'] }}
                </p>
            </div>
            <a href="{{ route('analytics.benchmark', request()->all()) }}" class="btn btn-primary" style="background: #7c3aed; border-color: #6d28d9;">
                Explore Full Benchmark & Distribution &rarr;
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustomDates(val) {
        document.getElementById('customDatesWrap').style.display = (val === 'custom') ? 'flex' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Line Chart: Earnings Over Time
        const overTimeCtx = document.getElementById('earningsOverTimeChart').getContext('2d');
        const overTimeData = @json($overTimeSeries);

        new Chart(overTimeCtx, {
            type: 'line',
            data: {
                labels: overTimeData.labels,
                datasets: [
                    {
                        label: 'Net Take-Home (₹)',
                        data: overTimeData.net,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.08)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2.5,
                        pointRadius: 3,
                    },
                    {
                        label: 'Total Expenses (₹)',
                        data: overTimeData.expenses,
                        borderColor: '#ef4444',
                        backgroundColor: 'transparent',
                        borderDash: [4, 4],
                        borderWidth: 2,
                        pointRadius: 2,
                    },
                    {
                        label: 'Peer Community Median (₹)',
                        data: overTimeData.peer_benchmark,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'transparent',
                        borderDash: [6, 4],
                        borderWidth: 1.8,
                        pointRadius: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₹' + Number(context.raw).toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });

        // 2. Donut: Income Breakdown
        const incomeCtx = document.getElementById('incomeDonutChart').getContext('2d');
        const incomeData = @json($breakdowns['income']);

        new Chart(incomeCtx, {
            type: 'doughnut',
            data: {
                labels: incomeData.labels,
                datasets: [{
                    data: incomeData.data,
                    backgroundColor: incomeData.colors,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });

        // 3. Donut: Expense Breakdown
        const expenseCtx = document.getElementById('expenseDonutChart').getContext('2d');
        const expenseData = @json($breakdowns['expenses']);

        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: expenseData.labels,
                datasets: [{
                    data: expenseData.data,
                    backgroundColor: expenseData.colors,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endpush
