@extends('layouts.app')

@section('title', 'Platform Insights — Cross-Company Delivery Intelligence')

@section('content')
<div>
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-title-wrap">
            <div class="page-header-title">
                <span>Platform Insights</span>
                <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.75rem; vertical-align: middle;">Multi-Company Audit</span>
            </div>
            <p class="page-header-subtitle">
                Objective cross-platform comparison of hourly net take-home, fuel loads, and dispute resolution performance.
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('analytics.index') }}" class="btn btn-secondary btn-sm">&larr; Analytics Overview</a>
        </div>
    </div>

    <!-- Demonstration Notice Banner -->
    <div class="card" style="padding: 0.85rem 1.25rem; background: #fffbeb; border: 1px solid #fef3c7; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.25rem;">ℹ️</span>
            <div style="font-size: 0.825rem; color: #92400e; line-height: 1.5;">
                <strong>Dataset Notice:</strong> Company and role comparisons are generated from Gigy's anonymized synthetic benchmark model (SwiftDrop, QuickKart, CityRunner, FlashByte, UrbanRide). These names are demonstration models representing diverse gig platform archetypes.
            </div>
        </div>
    </div>

    <!-- Multi-Metric Highlights Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="margin-bottom: 0; border-top: 4px solid #059669;">
            <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Highest Median Net Pay</div>
            <div style="font-size: 1.45rem; font-weight: 800; color: #059669; margin-top: 0.25rem;">{{ $insights['highlights']['highest_net'] }}</div>
            <div style="font-size: 0.85rem; color: var(--gray-600); margin-top: 0.2rem;">
                {{ $insights['highlights']['highest_net_val'] }} median take-home
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; border-top: 4px solid #2563eb;">
            <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Lowest Expense Burden</div>
            <div style="font-size: 1.45rem; font-weight: 800; color: #2563eb; margin-top: 0.25rem;">{{ $insights['highlights']['lowest_expense'] }}</div>
            <div style="font-size: 0.85rem; color: var(--gray-600); margin-top: 0.2rem;">
                Only {{ $insights['highlights']['lowest_expense_val'] }} expense-to-gross ratio
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; border-top: 4px solid #8b5cf6;">
            <div style="font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; font-weight: 700;">Best Grievance Resolution</div>
            <div style="font-size: 1.45rem; font-weight: 800; color: #8b5cf6; margin-top: 0.25rem;">{{ $insights['highlights']['best_resolution'] }}</div>
            <div style="font-size: 0.85rem; color: var(--gray-600); margin-top: 0.2rem;">
                {{ $insights['highlights']['best_resolution_val'] }} dispute settlement rate
            </div>
        </div>
    </div>

    <!-- Visual Chart: Hourly Net by Platform -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Median Net Earnings Per Hour by Platform</h3>
            <span style="font-size: 0.8rem; color: var(--gray-600);">Net ₹/hr after fuel & commissions</span>
        </div>
        <div style="height: 260px; position: relative;">
            <canvas id="platformBarChart"></canvas>
        </div>
    </div>

    <!-- Comparative Table with Multi-Metric Sorting -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Comprehensive Platform Comparison Matrix</h3>
            <div style="font-size: 0.825rem; color: var(--gray-600);">
                Sort by:
                <a href="{{ route('analytics.platforms', ['sort' => 'net_per_hour', 'dir' => $currentSort === 'net_per_hour' && $currentDir === 'desc' ? 'asc' : 'desc']) }}" style="color: var(--primary); font-weight: 700; margin-left: 0.4rem;">
                    Hourly Net {{ $currentSort === 'net_per_hour' ? ($currentDir === 'desc' ? '↓' : '↑') : '' }}
                </a>
                &bull;
                <a href="{{ route('analytics.platforms', ['sort' => 'expense_ratio', 'dir' => $currentSort === 'expense_ratio' && $currentDir === 'asc' ? 'desc' : 'asc']) }}" style="color: var(--primary); font-weight: 700; margin-left: 0.4rem;">
                    Expense Ratio {{ $currentSort === 'expense_ratio' ? ($currentDir === 'asc' ? '↑' : '↓') : '' }}
                </a>
                &bull;
                <a href="{{ route('analytics.platforms', ['sort' => 'resolution_rate', 'dir' => $currentSort === 'resolution_rate' && $currentDir === 'desc' ? 'asc' : 'desc']) }}" style="color: var(--primary); font-weight: 700; margin-left: 0.4rem;">
                    Resolution % {{ $currentSort === 'resolution_rate' ? ($currentDir === 'desc' ? '↓' : '↑') : '' }}
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <caption class="sr-only">Detailed multi-company performance and dispute metric matrix</caption>
                <thead>
                    <tr>
                        <th scope="col">Platform</th>
                        <th scope="col">Category</th>
                        <th scope="col">Median Net (₹/hr)</th>
                        <th scope="col">Daily Net Take-Home</th>
                        <th scope="col">Expense Ratio</th>
                        <th scope="col">Total Deliveries</th>
                        <th scope="col">Grievance Disputes</th>
                        <th scope="col">Rate per 1k Trips</th>
                        <th scope="col">Dispute Resolution %</th>
                        <th scope="col">Avg Resolution Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insights['companies'] as $comp)
                        <tr>
                            <td>
                                <strong style="font-size: 0.95rem; color: var(--gray-900);">{{ $comp['name'] }}</strong>
                                <div style="font-size: 0.725rem; color: var(--gray-600);">{{ $comp['worker_count'] }} observed workers</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $comp['category'] }}</span>
                            </td>
                            <td>
                                <strong class="currency" style="color: #059669; font-size: 1.05rem;">
                                    ₹{{ number_format($comp['median_net_per_hour'], 2) }}/hr
                                </strong>
                            </td>
                            <td>
                                <span class="currency">₹{{ number_format($comp['avg_daily_net'], 2) }}</span>
                                <div style="font-size: 0.7rem; color: var(--gray-600);">{{ $comp['avg_daily_hours'] }} hrs/shift</div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: {{ $comp['expense_ratio'] > 25 ? 'var(--danger)' : '#059669' }};">
                                    {{ $comp['expense_ratio'] }}%
                                </span>
                            </td>
                            <td>
                                <span>{{ number_format($comp['total_deliveries']) }}</span>
                            </td>
                            <td>
                                <span>{{ $comp['total_complaints'] }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $comp['complaint_rate_per_1000'] > 15 ? 'badge-warning' : 'badge-neutral' }}">
                                    {{ $comp['complaint_rate_per_1000'] }}/1k
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $comp['resolution_rate'] >= 60 ? 'badge-success' : 'badge-warning' }}">
                                    {{ $comp['resolution_rate'] }}%
                                </span>
                            </td>
                            <td>
                                <span>{{ $comp['avg_resolution_hours'] }} hrs</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('platformBarChart').getContext('2d');
        const chartData = @json($insights['chart_data']);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Median Net Earnings (₹/hr)',
                    data: chartData.hourly_rates,
                    backgroundColor: chartData.colors,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return '₹' + v; }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
