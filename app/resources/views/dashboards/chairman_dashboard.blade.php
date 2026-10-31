@extends('layouts.app')

@section('title', 'Chairman Dashboard')

@section('content')
    {{-- Top Statistics Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Total Approved</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['approved'] }}</p>
                            <span class="badge badge-subtle-success rounded-pill fs-11">All Time</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-success">
                                <i class="fas fa-certificate"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">This Month</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['approved_this_month'] }}</p>
                            <span class="badge badge-subtle-primary rounded-pill fs-11">{{ now()->format('F Y') }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-primary">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Electrician Permits</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $electrician['total_approved'] }}</p>
                            <span class="badge badge-subtle-info rounded-pill fs-11">Total</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-info">
                                <i class="fas fa-bolt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Supervisor & Contractor</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                {{ $supervisor['total_approved'] + $contractor['total_approved'] }}</p>
                            <span class="badge badge-subtle-warning rounded-pill fs-11">Combined</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-warning">
                                <i class="fas fa-hard-hat"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permit Type Breakdown --}}
    <div class="row g-0 mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Approved Permits by Type</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center border-end">
                                <div class="avatar avatar-4xl">
                                    <div class="avatar-name rounded-circle bg-primary-subtle">
                                        <span class="fs-2 text-primary"><i class="fas fa-bolt"></i></span>
                                    </div>
                                </div>
                                <h3 class="mt-3 mb-0">{{ $electrician['total_approved'] }}</h3>
                                <h6 class="text-primary">Electrician Permits</h6>
                                <small class="text-500">{{ $electrician['approved_this_month'] }} this month</small>
                                <div class="mt-3">
                                    <a href="{{ route('ex-electrician.chairman.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center border-end">
                                <div class="avatar avatar-4xl">
                                    <div class="avatar-name rounded-circle bg-info-subtle">
                                        <span class="fs-2 text-info"><i class="fas fa-users"></i></span>
                                    </div>
                                </div>
                                <h3 class="mt-3 mb-0">{{ $supervisor['total_approved'] }}</h3>
                                <h6 class="text-info">Supervisor Permits</h6>
                                <small class="text-500">{{ $supervisor['approved_this_month'] }} this month</small>
                                <div class="mt-3">
                                    <a href="{{ route('ex-supervisor.chairman.index') }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="avatar avatar-4xl">
                                    <div class="avatar-name rounded-circle bg-warning-subtle">
                                        <span class="fs-2 text-warning"><i class="fas fa-hard-hat"></i></span>
                                    </div>
                                </div>
                                <h3 class="mt-3 mb-0">{{ $contractor['total_approved'] }}</h3>
                                <h6 class="text-warning">Contractor Permits</h6>
                                <small class="text-500">{{ $contractor['approved_this_month'] }} this month</small>
                                <div class="mt-3">
                                    <a href="{{ route('ex-contractor.chairman.index') }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Approvals --}}
    <div class="row g-0">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recently Approved Applications</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('ex-electrician.reports.index') }}" class="btn btn-sm btn-falcon-default">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm table-dashboard mb-0 fs-10">
                            <thead class="bg-body-tertiary">
                                <tr>
                                    <th>Permit Type</th>
                                    <th>Certificate No.</th>
                                    <th>Applicant Name</th>
                                    <th>Approved At</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_approvals as $app)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge badge-subtle-{{ $app['type'] === 'Electrician' ? 'primary' : ($app['type'] === 'Supervisor' ? 'info' : 'warning') }}">
                                                {{ $app['type'] }}
                                            </span>
                                        </td>
                                        <td>{{ $app['certificate_number'] }}</td>
                                        <td>{{ Str::limit($app['applicant_name'], 30) }}</td>
                                        <td>{{ $app['approved_at']?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <a href="{{ $app['view_url'] }}" class="btn btn-sm btn-falcon-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-500">
                                            <i class="fas fa-inbox text-secondary fs-3 mb-2"></i>
                                            <p>No recent approvals</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection