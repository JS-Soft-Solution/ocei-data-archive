@extends('layouts.app')

@section('title', 'Operator Performance Report')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class="fas fa-user-edit text-primary me-2"></i>Operator Performance Report</h3>
            <p class="text-muted mb-0">
                Performance metrics and entry history for <strong>{{ $targetUser->name }}</strong> ({{ $targetUser->email }})
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <a href="{{ route('reports.operator') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('reports.operator') }}" class="row g-2 align-items-end">
                <div class="col-6 col-sm-3 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-certificate me-1"></i>Permit Type</label>
                    <select name="permit_type" class="form-select form-select-sm">
                        <option value="all" {{ $permitType === 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="electrician" {{ $permitType === 'electrician' ? 'selected' : '' }}>Electrician</option>
                        <option value="supervisor" {{ $permitType === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="contractor" {{ $permitType === 'contractor' ? 'selected' : '' }}>Contractor</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-calendar-day me-1"></i>From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="{{ $dateFrom }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-calendar-day me-1"></i>To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="{{ $dateTo }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-6 col-sm-3 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.operator') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2c7be5 !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Total Entries</p>
                    <h2 class="mb-0 fw-bolder text-primary">{{ number_format($stats['total']) }}</h2>
                    <small class="text-muted">Filtered timeframe</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #00d27a !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Final Approved</p>
                    <h2 class="mb-0 fw-bolder text-success">{{ number_format($stats['approved']) }}</h2>
                    <small class="text-muted">Fully verified & approved</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f5803e !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">In Review</p>
                    <h2 class="mb-0 fw-bolder text-warning">{{ number_format($stats['submitted']) }}</h2>
                    <small class="text-muted">Awaiting OA / Secretary</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e63757 !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Rejected / Returned</p>
                    <h2 class="mb-0 fw-bolder text-danger">{{ number_format($stats['rejected']) }}</h2>
                    <small class="text-muted">Needs correction</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats: Today, Month, Drafts --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-11 fw-bold text-uppercase">Entered Today</span>
                        <h4 class="mb-0 fw-bold text-dark mt-1">{{ number_format($stats['today']) }}</h4>
                    </div>
                    <div class="fs-4 text-info"><i class="fas fa-calendar-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-11 fw-bold text-uppercase">Entered This Month</span>
                        <h4 class="mb-0 fw-bold text-dark mt-1">{{ number_format($stats['this_month']) }}</h4>
                    </div>
                    <div class="fs-4 text-primary"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-11 fw-bold text-uppercase">Drafts Pending Upload</span>
                        <h4 class="mb-0 fw-bold text-dark mt-1">{{ number_format($stats['draft']) }}</h4>
                    </div>
                    <div class="fs-4 text-secondary"><i class="fas fa-edit"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 14-Day Activity Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line text-primary me-2"></i>Daily Data Entry Trend (Last 14 Days)</h6>
        </div>
        <div class="card-body p-3">
            <div id="dailyVolumeChart" style="height: 250px; width: 100%;"></div>
        </div>
    </div>

    {{-- Recent Entries Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="fas fa-list text-primary me-2"></i>Recent Applications Entered by {{ $targetUser->name }}</h6>
            <span class="badge bg-secondary">Latest {{ count($recentEntries) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Certificate #</th>
                            <th>Name / Company</th>
                            <th>Book No</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEntries as $entry)
                            <tr>
                                <td><span class="badge bg-info-subtle text-info">{{ $entry->permit_type_label ?? 'Permit' }}</span></td>
                                <td class="fw-bold">{{ $entry->old_certificate_number }}</td>
                                <td>
                                    {{ $entry->applicant_name_en ?? $entry->company_name_en ?? '-' }}
                                    @if(!empty($entry->applicant_name_bn) || !empty($entry->company_name_bn))
                                        <br><small class="text-muted">{{ $entry->applicant_name_bn ?? $entry->company_name_bn }}</small>
                                    @endif
                                </td>
                                <td>{{ $entry->book_number ?? '-' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $entry->class ?? '-' }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $entry->status_badge_color ?? 'secondary' }}">
                                        {{ $entry->status_label ?? $entry->status }}
                                    </span>
                                </td>
                                <td class="text-muted fs-11">
                                    {{ $entry->created_at ? $entry->created_at->format('d-m-Y h:i A') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No entries found for this operator</td>
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
    var chartEl = document.getElementById('dailyVolumeChart');
    if (chartEl && typeof echarts !== 'undefined') {
        var chart = echarts.init(chartEl);
        var dates = @json(array_keys($dailyVolume));
        var values = @json(array_values($dailyVolume));

        chart.setOption({
            tooltip: { trigger: 'axis' },
            grid: { left: '3%', right: '3%', bottom: '10%', top: '10%', containLabel: true },
            xAxis: { type: 'category', data: dates },
            yAxis: { type: 'value', minInterval: 1 },
            series: [{
                name: 'Entries',
                type: 'line',
                smooth: true,
                data: values,
                itemStyle: { color: '#2c7be5' },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(44, 123, 229, 0.4)' },
                        { offset: 1, color: 'rgba(44, 123, 229, 0.02)' }
                    ])
                },
                label: { show: true, position: 'top', fontSize: 10 }
            }]
        });

        window.addEventListener('resize', function () {
            chart.resize();
        });
    }
});
</script>
@endpush
