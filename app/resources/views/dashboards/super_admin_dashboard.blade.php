@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
    {{-- Top Statistics Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Total Applications</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['applications'] }}</p>
                            <span class="badge badge-subtle-primary rounded-pill fs-11">All Permits</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-primary">
                                <i class="fas fa-database"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">System Users</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['users'] }}</p>
                            <span class="badge badge-subtle-info rounded-pill fs-11">Active</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-info">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Approved This Month</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['approved_this_month'] }}</p>
                            <span class="badge badge-subtle-success rounded-pill fs-11">{{ now()->format('F') }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Pending Review</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                {{ $electrician['pending_oa'] + $supervisor['pending_oa'] + $contractor['pending_oa'] +
        $electrician['pending_secretary'] + $supervisor['pending_secretary'] + $contractor['pending_secretary'] }}
                            </p>
                            <span class="badge badge-subtle-warning rounded-pill fs-11">All Stages</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-warning">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permit Type Overview --}}
    <div class="row g-0 mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Applications by Permit Type</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border-bottom border-200 pb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-primary-subtle">
                                            <span class="text-primary"><i class="fas fa-bolt"></i></span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $electrician['total'] }}</h5>
                                        <h6 class="text-primary mb-0">Electrician Permits</h6>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10 mt-2">
                                    <span>Draft:</span>
                                    <span class="fw-semi-bold">{{ $electrician['draft'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10">
                                    <span>Pending OA:</span>
                                    <span class="fw-semi-bold">{{ $electrician['pending_oa'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10">
                                    <span>Pending Secretary:</span>
                                    <span class="fw-semi-bold">{{ $electrician['pending_secretary'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-success fs-10">
                                    <span>Approved:</span>
                                    <span class="fw-semi-bold">{{ $electrician['approved'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-danger fs-10">
                                    <span>Rejected:</span>
                                    <span class="fw-semi-bold">{{ $electrician['rejected'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-bottom border-200 pb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-info-subtle">
                                            <span class="text-info"><i class="fas fa-users"></i></span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $supervisor['total'] }}</h5>
                                        <h6 class="text-info mb-0">Supervisor Permits</h6>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10 mt-2">
                                    <span>Draft:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['draft'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10">
                                    <span>Pending OA:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['pending_oa'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10">
                                    <span>Pending Secretary:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['pending_secretary'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-success fs-10">
                                    <span>Approved:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['approved'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-danger fs-10">
                                    <span>Rejected:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['rejected'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-bottom border-200 pb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-warning-subtle">
                                            <span class="text-warning"><i class="fas fa-hard-hat"></i></span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $contractor['total'] }}</h5>
                                        <h6 class="text-warning mb-0">Contractor Permits</h6>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10 mt-2">
                                    <span>Draft:</span>
                                    <span class="fw-semi-bold">{{ $contractor['draft'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10">
                                    <span>Pending OA:</span>
                                    <span class="fw-semi-bold">{{ $contractor['pending_oa'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-600 fs-10">
                                    <span>Pending Secretary:</span>
                                    <span class="fw-semi-bold">{{ $contractor['pending_secretary'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-success fs-10">
                                    <span>Approved:</span>
                                    <span class="fw-semi-bold">{{ $contractor['approved'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-danger fs-10">
                                    <span>Rejected:</span>
                                    <span class="fw-semi-bold">{{ $contractor['rejected'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Access Links --}}
    <div class="row g-0">
        <div class="col-lg-6 pe-lg-2 mb-3">
            <div class="card h-100">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">System Management</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('users.index') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-users-cog me-2 text-primary"></i>
                                <span>User Management</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $total['users'] }}</span>
                        </a>
                        <a href="{{ route('ex-electrician.admin.index') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-bolt me-2 text-primary"></i>
                                <span>Electrician Admin Panel</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill">{{ $electrician['total'] }}</span>
                        </a>
                        <a href="{{ route('ex-supervisor.admin.index') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-users me-2 text-info"></i>
                                <span>Supervisor Admin Panel</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill">{{ $supervisor['total'] }}</span>
                        </a>
                        <a href="{{ route('ex-contractor.admin.index') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-hard-hat me-2 text-warning"></i>
                                <span>Contractor Admin Panel</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill">{{ $contractor['total'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 ps-lg-2 mb-3">
            <div class="card h-100">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Reports & Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('ex-electrician.reports.index') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            <span>Electrician Reports</span>
                        </a>
                        <a href="{{ route('ex-supervisor.reports.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2 text-info"></i>
                            <span>Supervisor Reports</span>
                        </a>
                        <a href="{{ route('ex-contractor.reports.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2 text-warning"></i>
                            <span>Contractor Reports</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-export me-2 text-success"></i>
                            <span>Export All Data</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Breakdown Chart --}}
    <div class="row g-0">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">System Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <h3 class="text-secondary">
                                {{ $electrician['draft'] + $supervisor['draft'] + $contractor['draft'] }}
                            </h3>
                            <small class="text-600">Draft</small>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-info">
                                {{ $electrician['pending_oa'] + $supervisor['pending_oa'] + $contractor['pending_oa'] }}
                            </h3>
                            <small class="text-600">Pending OA</small>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-primary">
                                {{ $electrician['pending_secretary'] + $supervisor['pending_secretary'] + $contractor['pending_secretary'] }}
                            </h3>
                            <small class="text-600">Pending Secretary</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success">
                                {{ $electrician['approved'] + $supervisor['approved'] + $contractor['approved'] }}
                            </h3>
                            <small class="text-600">Approved (Final)</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-danger">
                                {{ $electrician['rejected'] + $supervisor['rejected'] + $contractor['rejected'] }}
                            </h3>
                            <small class="text-600">Rejected</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection