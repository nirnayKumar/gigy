@extends('layouts.app')

@section('title', 'Worker Issues & Complaint Trends — Grievance Analytics')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--gray-900);">Worker Issues & Complaint Trends</h1>
                <span class="badge" style="background: #fef2f2; color: #b91c1c; font-weight: 700;">Grievance Intelligence</span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.25rem;">
                Track which problems workers are reporting across delivery platforms, resolution pacing, and systemic payment discrepancies.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm">+ Draft New Dispute</a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('analytics.complaints') }}" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label for="compFilter" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Platform</label>
                <select id="compFilter" name="company" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    <option value="All">All Platforms</option>
                    @foreach($companies as $cmp)
                        <option value="{{ $cmp->name }}" {{ $filters['company'] === $cmp->name ? 'selected' : '' }}>{{ $cmp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="catFilter" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Issue Category</label>
                <select id="catFilter" name="category" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $filters['category'] === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="statusFilter" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Status</label>
                <select id="statusFilter" name="status" class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                    <option value="All" {{ $filters['status'] === 'All' ? 'selected' : '' }}>All Statuses</option>
                    <option value="resolved" {{ $filters['status'] === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="submitted" {{ $filters['status'] === 'submitted' ? 'selected' : '' }}>Submitted / Open</option>
                    <option value="draft" {{ $filters['status'] === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="cancelled" {{ $filters['status'] === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label for="searchFilter" class="form-label" style="font-size: 0.775rem; text-transform: uppercase;">Keyword Search</label>
                <input id="searchFilter" type="text" name="q" value="{{ $search }}" placeholder="Search description..." class="form-control" style="min-height: 38px; padding: 0.4rem 0.75rem; font-size: 0.85rem; width: 170px;">
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="min-height: 38px;">Filter Analytics</button>
            <a href="{{ route('analytics.complaints') }}" class="btn btn-secondary btn-sm" style="min-height: 38px;">Reset</a>
        </form>
    </div>

    <!-- Data-Driven Insights Banner -->
    <div class="card" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; border: none; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">💡</span>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #a5b4fc; font-weight: 700;">Systemic Dispute Insights</div>
                <div style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.9rem; line-height: 1.5;">
                    @foreach($analytics['insights'] as $insight)
                        <div>&bull; {{ $insight }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Top Complaint KPIs Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="margin-bottom: 0;">
            <span class="metric-label">Total Disputes Logged</span>
            <div class="metric-value" style="color: var(--gray-900); margin-top: 0.25rem;">{{ number_format($analytics['total_complaints']) }}</div>
            <div class="metric-sub">{{ $analytics['submitted_count'] }} currently pending response</div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <span class="metric-label">Resolution Settlement Rate</span>
            <div class="metric-value" style="color: #059669; margin-top: 0.25rem;">{{ $analytics['resolution_rate'] }}%</div>
            <div class="metric-sub">{{ $analytics['resolved_count'] }} successfully resolved</div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <span class="metric-label">Median Resolution Pacing</span>
            <div class="metric-value" style="color: #2563eb; margin-top: 0.25rem;">{{ $analytics['median_resolution_hours'] }} hrs</div>
            <div class="metric-sub">Average turnaround time</div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <span class="metric-label">Active Platform Cohort</span>
            <div class="metric-value" style="color: #7c3aed; margin-top: 0.25rem;">{{ $companies->count() }} Platforms</div>
            <div class="metric-sub">Synthetic audit dataset</div>
        </div>
    </div>

    <!-- 2-Column: Category Ranking Bar + Status Donut -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Category Ranking Bar Chart -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Top Reported Grievance Categories</h3>
                <span style="font-size: 0.8rem; color: var(--gray-600);">Volume ranking</span>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="categoryBarChart"></canvas>
            </div>
        </div>

        <!-- Resolution Status Donut Chart -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Dispute Status Distribution</h3>
                <span style="font-size: 0.8rem; color: var(--gray-600);">Current lifecycle</span>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="statusDonutChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Searchable Complaint Explorer Table -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Anonymized Complaint Explorer</h3>
                <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.15rem;">
                    Browse recorded grievance reports with status tracking and resolution durations.
                </p>
            </div>
            <span class="badge badge-neutral">{{ $complaints->total() }} records matched</span>
        </div>

        @if($complaints->isEmpty())
            <div class="empty-state">
                <p style="font-weight: 600; color: var(--gray-700);">No complaints matched the filter criteria.</p>
                <p style="font-size: 0.85rem; margin-top: 0.25rem;">Try adjusting the keyword search or broadening the platform filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <caption class="sr-only">List of anonymized complaint records and resolution duration</caption>
                    <thead>
                        <tr>
                            <th scope="col">Platform</th>
                            <th scope="col">Category</th>
                            <th scope="col">Incident Date</th>
                            <th scope="col">Discrepancy (₹)</th>
                            <th scope="col">Status</th>
                            <th scope="col">Resolution Duration</th>
                            <th scope="col">Role & City</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $c)
                            <tr>
                                <td>
                                    <strong style="color: var(--gray-900);">{{ $c->company_name }}</strong>
                                </td>
                                <td>
                                    <span>{{ $c->issue_category }}</span>
                                </td>
                                <td>
                                    <span>{{ $c->incident_date ? $c->incident_date->format('d M Y') : 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($c->expected_amount !== null)
                                        <span class="currency" style="color: var(--danger); font-weight: 700;">
                                            ₹{{ number_format($c->expected_amount - ($c->received_amount ?? 0), 2) }}
                                        </span>
                                    @else
                                        <span style="color: var(--gray-600);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $c->status === 'resolved' ? 'badge-success' : ($c->status === 'submitted' ? 'badge-info' : 'badge-warning') }}">
                                        {{ $c->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($c->resolution_time_hours)
                                        <span>{{ $c->resolution_time_hours }} hrs</span>
                                    @else
                                        <span style="color: var(--gray-600);">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.775rem; color: var(--gray-700);">
                                        {{ $c->worker_role ?? 'Courier' }} &bull; {{ $c->city ?? 'Bengaluru' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav aria-label="Complaint explorer pagination" style="margin-top: 1.25rem;">
                {{ $complaints->links() }}
            </nav>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Horizontal Bar Chart: Category Rankings
        const catCtx = document.getElementById('categoryBarChart').getContext('2d');
        const catData = @json($analytics['category_chart']);

        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: catData.labels,
                datasets: [{
                    label: 'Reported Incidents',
                    data: catData.data,
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // 2. Donut: Status Distribution
        const statusCtx = document.getElementById('statusDonutChart').getContext('2d');
        const statusData = @json($analytics['status_chart']);

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.data,
                    backgroundColor: statusData.colors,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endpush
