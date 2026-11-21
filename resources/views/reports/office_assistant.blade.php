@extends('layouts.app')

@section('title', 'Office Assistant Verification Report')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class="fas fa-tasks text-primary me-2"></i>Office Assistant Verification Report</h3>
            <p class="text-muted mb-0">
                Review and verification throughput for <strong>{{ $targetUser->name }}</strong> ({{ $targetUser->email }})
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <a href="{{ route('reports.office-assistant') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('reports.office-assistant') }}" class="row g-2 align-items-end">
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
                    <a href="{{ route('reports.office-assistant') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #00d27a !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Total Verified</p>
                    <h2 class="mb-0 fw-bolder text-success">{{ number_format($stats['verified_total']) }}</h2>
                    <small class="text-muted">Forwarded to Secretary</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2c7be5 !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Verification Rate</p>
                    <h2 class="mb-0 fw-bolder text-primary">{{ $verificationRate }}%</h2>
                    <small class="text-muted">Of reviewed records passed</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e63757 !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Total Rejected</p>
                    <h2 class="mb-0 fw-bolder text-danger">{{ number_format($stats['rejected_total']) }}</h2>
                    <small class="text-muted">Returned to Operator</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f5803e !important;">
                <div class="card-body">
                    <p class="text-uppercase text-muted fw-bold fs-11 mb-1">Currently Pending</p>
                    <h2 class="mb-0 fw-bolder text-warning">{{ number_format($stats['pending_now']) }}</h2>
                    <small class="text-muted">Awaiting review</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Quick Numbers --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm p-3 bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-11 fw-bold text-uppercase">Verified Today</span>
                        <h4 class="mb-0 fw-bold text-success mt-1">{{ number_format($stats['verified_today']) }}</h4>
                    </div>
                    <div class="fs-3 text-success"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm p-3 bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-11 fw-bold text-uppercase">Rejected Today</span>
                        <h4 class="mb-0 fw-bold text-danger mt-1">{{ number_format($stats['rejected_today']) }}</h4>
                    </div>
                    <div class="fs-3 text-danger"><i class="fas fa-undo"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown by Permit Type Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-layer-group text-primary me-2"></i>Throughput by Permit Type</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Permit Type</th>
                            <th class="text-center">Currently Pending</th>
                            <th class="text-center">Verified / Approved</th>
                            <th class="text-center">Rejected Back</th>
                            <th class="text-end">Total Processed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['electrician' => 'Electrician Permits', 'supervisor' => 'Supervisor Permits', 'contractor' => 'Contractor Permits'] as $key => $title)
                            @php
                                $proc = $byType[$key]['verified'] + $byType[$key]['rejected'];
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $title }}</td>
                                <td class="text-center"><span class="badge bg-warning">{{ number_format($byType[$key]['pending']) }}</span></td>
                                <td class="text-center text-success fw-bold">{{ number_format($byType[$key]['verified']) }}</td>
                                <td class="text-center text-danger fw-bold">{{ number_format($byType[$key]['rejected']) }}</td>
                                <td class="text-end fw-bolder">{{ number_format($proc) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
