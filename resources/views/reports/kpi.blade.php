@extends('layouts.app')

@section('title', 'KPI Reports & Executive Analytics')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Executive KPI & Analytics Dashboard</h3>
            <p class="text-muted mb-0">High-level performance indicators, processing efficiency, and archive statistics</p>
        </div>
        <div class="mt-2 mt-md-0">
            <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print / Export
            </button>
            <a href="{{ route('reports.kpi') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('reports.kpi') }}" class="row g-2 align-items-end">
                <div class="col-6 col-sm-4 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-calendar-alt me-1"></i>Year</label>
                    <select name="year" class="form-select form-select-sm">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-certificate me-1"></i>Permit Type</label>
                    <select name="permit_type" class="form-select form-select-sm">
                        <option value="all" {{ $permitType === 'all' ? 'selected' : '' }}>All Permits</option>
                        <option value="electrician" {{ $permitType === 'electrician' ? 'selected' : '' }}>Electrician</option>
                        <option value="supervisor" {{ $permitType === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="contractor" {{ $permitType === 'contractor' ? 'selected' : '' }}>Contractor</option>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>Division</label>
                    <select name="division" class="form-select form-select-sm">
                        <option value="">All Divisions</option>
                        @foreach($divisionsList as $div)
                            <option value="{{ $div }}" {{ $division === $div ? 'selected' : '' }}>{{ $div }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Apply Filter
                    </button>
                    <a href="{{ route('reports.kpi') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Top KPI Stat Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Applications --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2c7be5 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Total Applications</p>
                            <h2 class="mb-0 fw-bolder text-primary">{{ number_format($totals['total']) }}</h2>
                        </div>
                        <div class="fs-4 text-primary bg-primary-subtle p-3 rounded-circle">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                    <p class="text-muted fs-11 mb-0 mt-2">In Year <strong>{{ $year }}</strong></p>
                </div>
            </div>
        </div>

        {{-- Approval Rate --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #00d27a !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Approval Rate</p>
                            <h2 class="mb-0 fw-bolder text-success">{{ $approvalRate }}%</h2>
                        </div>
                        <div class="fs-4 text-success bg-success-subtle p-3 rounded-circle">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <p class="text-muted fs-11 mb-0 mt-2">
                        <span class="fw-bold text-success">{{ number_format($totals['approved']) }}</span> finalized permits
                    </p>
                </div>
            </div>
        </div>

        {{-- Pending Backlog --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f5803e !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Pending Backlog</p>
                            <h2 class="mb-0 fw-bolder text-warning">{{ number_format($totals['total_pending']) }}</h2>
                        </div>
                        <div class="fs-4 text-warning bg-warning-subtle p-3 rounded-circle">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <p class="text-muted fs-11 mb-0 mt-2">
                        OA: <strong>{{ number_format($totals['pending_oa']) }}</strong> | Sec: <strong>{{ number_format($totals['pending_secretary']) }}</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- Rejection Rate --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e63757 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Rejection Rate</p>
                            <h2 class="mb-0 fw-bolder text-danger">{{ $rejectionRate }}%</h2>
                        </div>
                        <div class="fs-4 text-danger bg-danger-subtle p-3 rounded-circle">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <p class="text-muted fs-11 mb-0 mt-2">
                        <span class="fw-bold text-danger">{{ number_format($totals['rejected']) }}</span> returned for correction
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1: Monthly Throughput & Status Distribution --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Monthly Throughput Trend ({{ $year }})</h6>
                    <span class="badge bg-primary-subtle text-primary">Applications vs Approvals</span>
                </div>
                <div class="card-body p-3">
                    <div id="monthlyChart" style="height: 320px; width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-chart-pie text-success me-2"></i>Status Pipeline</h6>
                </div>
                <div class="card-body p-3">
                    <div id="statusChart" style="height: 320px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2: Geographic & Class & Queue Age --}}
    <div class="row g-3 mb-4">
        {{-- Division Breakdown --}}
        <div class="col-12 col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-globe-asia text-info me-2"></i>Geographic Distribution (Divisions)</h6>
                </div>
                <div class="card-body p-3">
                    <div id="divisionChart" style="height: 280px; width: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Class Breakdown --}}
        <div class="col-12 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-layer-group text-warning me-2"></i>By Class</h6>
                </div>
                <div class="card-body p-3">
                    <div id="classChart" style="height: 280px; width: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Queue Age Breakdown --}}
        <div class="col-12 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-hourglass-half text-danger me-2"></i>Pending Queue Aging</h6>
                </div>
                <div class="card-body p-3">
                    <div id="pendingAgeChart" style="height: 280px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top 10 Operator Productivity Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="fas fa-users-cog text-primary me-2"></i>Top Operator Productivity Leaderboard ({{ $year }})</h6>
            <span class="badge bg-secondary">{{ count($topOperators) }} Operators</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Rank</th>
                            <th>Operator Name</th>
                            <th>Email</th>
                            <th class="text-end" width="140">Total Entries</th>
                            <th width="240">Contribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $maxEntries = !empty($topOperators) ? max(array_column($topOperators, 'count')) : 1;
                        @endphp
                        @forelse($topOperators as $index => $item)
                            @php
                                $pct = $maxEntries > 0 ? round(($item['count'] / $maxEntries) * 100) : 0;
                            @endphp
                            <tr>
                                <td class="fw-bold">
                                    @if($index === 0)
                                        <span class="badge bg-warning text-dark"><i class="fas fa-trophy"></i> #1</span>
                                    @elseif($index === 1)
                                        <span class="badge bg-secondary"><i class="fas fa-medal"></i> #2</span>
                                    @elseif($index === 2)
                                        <span class="badge bg-info"><i class="fas fa-medal"></i> #3</span>
                                    @else
                                        #{{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="fw-semibold">
                                    {{ $item['user'] ? $item['user']->name : 'User #' . ($item['user']->id ?? 'Unknown') }}
                                </td>
                                <td class="text-muted">{{ $item['user'] ? $item['user']->email : '-' }}</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($item['count']) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-fill" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-muted fs-11" style="min-width: 35px;">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No operator entry data found for {{ $year }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendors/echarts/echarts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof echarts === 'undefined') {
        console.warn('ECharts library is not loaded');
        return;
    }

    var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // 1. Monthly Throughput Chart
    var monthlyEl = document.getElementById('monthlyChart');
    if (monthlyEl) {
        var monthlyChart = echarts.init(monthlyEl);
        monthlyChart.setOption({
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
            legend: { data: ['New Applications', 'Final Approvals'], bottom: 0 },
            grid: { left: '3%', right: '4%', bottom: '12%', top: '8%', containLabel: true },
            xAxis: { type: 'category', data: monthNames },
            yAxis: { type: 'value', minInterval: 1 },
            series: [
                {
                    name: 'New Applications',
                    type: 'bar',
                    data: @json(array_values($monthlyApplications)),
                    itemStyle: { color: '#2c7be5', borderRadius: [4, 4, 0, 0] }
                },
                {
                    name: 'Final Approvals',
                    type: 'bar',
                    data: @json(array_values($monthlyApprovals)),
                    itemStyle: { color: '#00d27a', borderRadius: [4, 4, 0, 0] }
                }
            ]
        });
    }

    // 2. Status Distribution Pie Chart
    var statusEl = document.getElementById('statusChart');
    if (statusEl) {
        var statusChart = echarts.init(statusEl);
        var statusData = [
            { name: 'Draft', value: {{ $statusDistribution['draft'] }}, itemStyle: { color: '#748194' } },
            { name: 'Sent to OA', value: {{ $statusDistribution['submitted_to_office_assistant'] }}, itemStyle: { color: '#27bcfd' } },
            { name: 'Sent to Secretary', value: {{ $statusDistribution['submitted_to_secretary'] }}, itemStyle: { color: '#2c7be5' } },
            { name: 'Final Approved', value: {{ $statusDistribution['secretary_approved_final'] }}, itemStyle: { color: '#00d27a' } },
            { name: 'OA Rejected', value: {{ $statusDistribution['office_assistant_rejected'] }}, itemStyle: { color: '#f5803e' } },
            { name: 'Sec Rejected', value: {{ $statusDistribution['secretary_rejected'] }}, itemStyle: { color: '#e63757' } }
        ];

        statusChart.setOption({
            tooltip: { trigger: 'item', formatter: '{b}: <strong>{c}</strong> ({d}%)' },
            legend: { orient: 'horizontal', bottom: 0, textStyle: { fontSize: 10 } },
            series: [{
                type: 'pie',
                radius: ['45%', '70%'],
                avoidLabelOverlap: false,
                label: { show: false },
                data: statusData
            }]
        });
    }

    // 3. Division Chart (Horizontal Bar)
    var divEl = document.getElementById('divisionChart');
    if (divEl) {
        var divChart = echarts.init(divEl);
        var rawDivs = @json($divisionDistribution);
        var divLabels = Object.keys(rawDivs);
        var divValues = Object.values(rawDivs);

        divChart.setOption({
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
            grid: { left: '3%', right: '8%', bottom: '5%', top: '5%', containLabel: true },
            xAxis: { type: 'value', minInterval: 1 },
            yAxis: { type: 'category', data: divLabels.reverse() },
            series: [{
                type: 'bar',
                data: divValues.reverse(),
                itemStyle: { color: '#27bcfd', borderRadius: [0, 4, 4, 0] },
                label: { show: true, position: 'right', fontSize: 10 }
            }]
        });
    }

    // 4. Class Distribution Pie Chart
    var classEl = document.getElementById('classChart');
    if (classEl) {
        var classChart = echarts.init(classEl);
        var rawClass = @json($classDistribution);
        var classData = Object.entries(rawClass).map(function(item) {
            return { name: item[0], value: item[1] };
        });

        classChart.setOption({
            tooltip: { trigger: 'item', formatter: 'Class {b}: <strong>{c}</strong> ({d}%)' },
            series: [{
                type: 'pie',
                radius: '70%',
                data: classData,
                label: { formatter: '{b}\n{d}%', fontSize: 11 }
            }]
        });
    }

    // 5. Pending Queue Age
    var ageEl = document.getElementById('pendingAgeChart');
    if (ageEl) {
        var ageChart = echarts.init(ageEl);
        ageChart.setOption({
            tooltip: { trigger: 'item', formatter: '{b}: <strong>{c}</strong> ({d}%)' },
            legend: { bottom: 0 },
            series: [{
                type: 'pie',
                radius: ['40%', '65%'],
                data: [
                    { name: '< 7 Days', value: {{ $pendingAge['less_7'] }}, itemStyle: { color: '#00d27a' } },
                    { name: '7 - 30 Days', value: {{ $pendingAge['between_7_30'] }}, itemStyle: { color: '#f5803e' } },
                    { name: '> 30 Days (Backlog)', value: {{ $pendingAge['more_30'] }}, itemStyle: { color: '#e63757' } }
                ],
                label: { formatter: '{b}: {c}', fontSize: 10 }
            }]
        });
    }

    // Responsive window resizing for all charts
    window.addEventListener('resize', function () {
        if (typeof monthlyChart !== 'undefined') monthlyChart.resize();
        if (typeof statusChart !== 'undefined') statusChart.resize();
        if (typeof divChart !== 'undefined') divChart.resize();
        if (typeof classChart !== 'undefined') classChart.resize();
        if (typeof ageChart !== 'undefined') ageChart.resize();
    });
});
</script>
@endpush
