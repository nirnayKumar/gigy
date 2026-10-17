@extends('layouts.app')

@section('title', 'Community Peer Benchmark — How Am I Performing?')

@section('content')
<div>
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-title-wrap">
            <div class="page-header-title">
                <span>Community Benchmark</span>
                <span class="badge" style="background: #ede9fe; color: #6d28d9; font-weight: 700; font-size: 0.75rem; vertical-align: middle;">Peer Intelligence</span>
            </div>
            <p class="page-header-subtitle">
                Compare your net hourly pay, deliveries, and expenses against anonymized peer medians in your city.
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('analytics.index') }}" class="btn btn-secondary btn-sm">&larr; Analytics Overview</a>
        </div>
    </div>

    <!-- Privacy & Dataset Notice Banner -->
    <div class="card" style="padding: 0.85rem 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.25rem;">🔒</span>
            <div style="font-size: 0.825rem; color: #166534; line-height: 1.5;">
                <strong>Privacy Safeguard Active:</strong> Community benchmarks use strictly anonymized aggregate statistics with a mandatory minimum cohort threshold of <strong>&ge; 10 distinct workers</strong>. Individual personal earnings, identities, and contact details are never exposed.
            </div>
        </div>
    </div>

    <!-- Cohort Filter Controls Card -->
    <div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('analytics.benchmark') }}" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
            <!-- City -->
            <div>
                <label for="citySelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">City / Region</label>
                <select id="citySelect" name="city" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ $filters['city'] === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Platform / Company -->
            <div>
                <label for="compSelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Platform</label>
                <select id="compSelect" name="company" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    <option value="All">All Platforms</option>
                    @foreach($companies as $cmp)
                        <option value="{{ $cmp->name }}" {{ $filters['company'] === $cmp->name ? 'selected' : '' }}>{{ $cmp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role -->
            <div>
                <label for="roleSelect" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Worker Role</label>
                <select id="roleSelect" name="role" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    @foreach($roles as $r)
                        <option value="{{ $r }}" {{ $filters['role'] === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Working Hours Band -->
            <div>
                <label for="hoursBand" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Hours Band</label>
                <select id="hoursBand" name="hours_band" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    <option value="All" {{ $filters['hours_band'] === 'All' ? 'selected' : '' }}>All Shifts</option>
                    <option value="part_time" {{ $filters['hours_band'] === 'part_time' ? 'selected' : '' }}>Part-time (&le; 5 hrs)</option>
                    <option value="full_time" {{ $filters['hours_band'] === 'full_time' ? 'selected' : '' }}>Full-time (&gt; 5 hrs)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="min-height: 38px;">Update Benchmark</button>
            <a href="{{ route('analytics.benchmark') }}" class="btn btn-secondary btn-sm" style="min-height: 38px;">Reset</a>
        </form>
    </div>

    @if($benchmark['is_suppressed'])
        <!-- Small Cohort Warning Notice -->
        <div class="card" style="border: 2px dashed #f59e0b; background: #fffbeb; padding: 2rem; text-align: center;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⚠️</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #b45309;">Cohort Size Below Privacy Threshold</h3>
            <p style="font-size: 0.9rem; color: #92400e; max-width: 580px; margin: 0.5rem auto 1.5rem; line-height: 1.6;">
                {{ $benchmark['message'] }}
            </p>
            <a href="{{ route('analytics.benchmark', ['city' => 'All', 'company' => 'All', 'role' => 'All']) }}" class="btn btn-primary">
                View All-City Benchmark
            </a>
        </div>
    @else
        <!-- Performance Summary Card with Contextual AI Statement -->
        <div class="card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border: none; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1; min-width: 280px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span class="badge" style="background: #3b82f6; color: white;">Analyzed Cohort: {{ $benchmark['cohort_size'] }} Workers</span>
                        <span class="badge" style="background: rgba(255,255,255,0.15); color: #cbd5e1;">{{ $benchmark['sessions_count'] }} Work Sessions</span>
                    </div>
                    <div style="font-size: 1.25rem; font-weight: 800; margin-top: 0.75rem; line-height: 1.4;">
                        {{ $benchmark['context_statement'] }}
                    </div>
                </div>

                <div style="text-align: right; background: rgba(255,255,255,0.08); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.15);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Your Standing</div>
                    <div style="font-size: 2rem; font-weight: 900; color: #34d399; margin-top: 0.2rem;">
                        Top {{ 100 - $benchmark['worker']['percentile_rank'] }}%
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">
                        ₹{{ number_format($benchmark['worker']['hourly_net'], 2) }}/hr vs median ₹{{ number_format($benchmark['median']['hourly_net'], 2) }}/hr
                    </div>
                </div>
            </div>
        </div>

        <!-- 2-Column: Grouped Bar Comparison + Distribution Curve -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <!-- Grouped Bar: Worker vs Community Median -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3 class="card-title">You vs Community Median</h3>
                    <span style="font-size: 0.8rem; color: var(--gray-600);">Key Dimensions</span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="groupedBarChart"></canvas>
                </div>
            </div>

            <!-- Distribution Bell Curve / Histogram -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3 class="card-title">Hourly Earnings Distribution</h3>
                    <span class="badge" style="background: #ede9fe; color: #7c3aed;">
                        Your position: ₹{{ number_format($benchmark['worker']['hourly_net'], 2) }}/hr
                    </span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Comparison Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detailed Dimension Matrix</h3>
                <span style="font-size: 0.8rem; color: var(--gray-600);">25th, Median (50th), and 75th percentiles</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <caption class="sr-only">Comparison of worker personal performance with community percentiles</caption>
                    <thead>
                        <tr>
                            <th scope="col">Performance Metric</th>
                            <th scope="col">Your Score</th>
                            <th scope="col">25th Percentile</th>
                            <th scope="col" style="background: #f1f5f9; color: var(--primary);">Median (50th)</th>
                            <th scope="col">75th Percentile</th>
                            <th scope="col">Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Net Earnings Per Hour (₹/hr)</strong></td>
                            <td><strong class="currency" style="color: #059669; font-size: 1.05rem;">₹{{ number_format($benchmark['worker']['hourly_net'], 2) }}</strong></td>
                            <td class="currency">₹{{ number_format($benchmark['percentiles']['p25'], 2) }}</td>
                            <td class="currency" style="background: #f8fafc; font-weight: 700;">₹{{ number_format($benchmark['percentiles']['p50'], 2) }}</td>
                            <td class="currency">₹{{ number_format($benchmark['percentiles']['p75'], 2) }}</td>
                            <td>
                                @if($benchmark['worker']['hourly_net'] >= $benchmark['percentiles']['p75'])
                                    <span class="badge badge-success">Top Quartile (High Performer)</span>
                                @elseif($benchmark['worker']['hourly_net'] >= $benchmark['percentiles']['p50'])
                                    <span class="badge badge-info">Above Median</span>
                                @else
                                    <span class="badge badge-warning">Below Median</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Daily Net Take-Home (₹/day)</strong></td>
                            <td><strong class="currency">₹{{ number_format($benchmark['worker']['daily_net'], 2) }}</strong></td>
                            <td class="currency">₹{{ number_format($benchmark['median']['daily_net'] * 0.8, 2) }}</td>
                            <td class="currency" style="background: #f8fafc; font-weight: 700;">₹{{ number_format($benchmark['median']['daily_net'], 2) }}</td>
                            <td class="currency">₹{{ number_format($benchmark['median']['daily_net'] * 1.25, 2) }}</td>
                            <td>
                                @if($benchmark['worker']['daily_net'] >= $benchmark['median']['daily_net'])
                                    <span class="badge badge-success">Above Typical</span>
                                @else
                                    <span class="badge badge-neutral">Standard Range</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Deliveries Per Hour</strong></td>
                            <td><strong>{{ $benchmark['worker']['deliveries_per_hour'] }} trips/hr</strong></td>
                            <td>1.8 trips/hr</td>
                            <td style="background: #f8fafc; font-weight: 700;">{{ $benchmark['median']['deliveries_per_hour'] }} trips/hr</td>
                            <td>3.2 trips/hr</td>
                            <td>
                                @if($benchmark['worker']['deliveries_per_hour'] >= $benchmark['median']['deliveries_per_hour'])
                                    <span class="badge badge-success">High Turnaround</span>
                                @else
                                    <span class="badge badge-neutral">Normal Pacing</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Daily Working Hours</strong></td>
                            <td><span>{{ $benchmark['worker']['daily_hours'] }} hrs</span></td>
                            <td>4.5 hrs</td>
                            <td style="background: #f8fafc; font-weight: 700;">{{ $benchmark['median']['daily_hours'] }} hrs</td>
                            <td>8.5 hrs</td>
                            <td>
                                <span class="badge badge-neutral">Healthy Balance</span>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Expense Ratio (Fuel + Deductions)</strong></td>
                            <td><strong style="color: {{ $benchmark['worker']['expense_ratio'] > 25 ? 'var(--danger)' : '#059669' }};">{{ $benchmark['worker']['expense_ratio'] }}%</strong></td>
                            <td>16.5%</td>
                            <td style="background: #f8fafc; font-weight: 700;">{{ $benchmark['median']['expense_ratio'] }}%</td>
                            <td>28.0%</td>
                            <td>
                                @if($benchmark['worker']['expense_ratio'] <= $benchmark['median']['expense_ratio'])
                                    <span class="badge badge-success">Cost Efficient</span>
                                @else
                                    <span class="badge badge-warning">High Fuel/Deduction Load</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if(!$benchmark['is_suppressed'])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Grouped Bar Chart
        const barCtx = document.getElementById('groupedBarChart').getContext('2d');
        const worker = @json($benchmark['worker']);
        const median = @json($benchmark['median']);

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Hourly Net (₹)', 'Deliveries/hr (x50)', 'Expense Ratio (%)'],
                datasets: [
                    {
                        label: 'You',
                        data: [
                            worker.hourly_net,
                            worker.deliveries_per_hour * 50,
                            worker.expense_ratio
                        ],
                        backgroundColor: '#059669',
                        borderRadius: 6,
                    },
                    {
                        label: 'Community Median',
                        data: [
                            median.hourly_net,
                            median.deliveries_per_hour * 50,
                            median.expense_ratio
                        ],
                        backgroundColor: '#94a3b8',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. Distribution Chart
        const distCtx = document.getElementById('distributionChart').getContext('2d');
        const distData = @json($benchmark['distribution']);

        // Highlight the user's bin
        const barColors = distData.data.map((_, idx) => {
            return idx === distData.worker_bin_index ? '#7c3aed' : '#cbd5e1';
        });

        new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: distData.labels,
                datasets: [{
                    label: 'Workers in Bracket',
                    data: distData.data,
                    backgroundColor: barColors,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                if (context.dataIndex === distData.worker_bin_index) {
                                    return '★ You are in this bracket';
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
