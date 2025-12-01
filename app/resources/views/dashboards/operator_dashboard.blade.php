@extends('layouts.app')

@section('title', 'Data Entry Operator Dashboard')

@section('content')
    {{-- Top Statistics Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Draft Applications</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['draft'] }}</p>
                            <span class="badge badge-subtle-secondary rounded-pill fs-11">Total Drafts</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-500">
                                <i class="fas fa-file-alt"></i>
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
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['pending'] }}</p>
                            <span class="badge badge-subtle-info rounded-pill fs-11">Submitted</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-info">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Rejected</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['rejected'] }}</p>
                            <span class="badge badge-subtle-warning rounded-pill fs-11">Need Revision</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Approved</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['approved'] }}</p>
                            <span class="badge badge-subtle-success rounded-pill fs-11">Completed</span>
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
    </div>

    {{-- Permit Type Breakdown --}}
    <div class="row g-0 mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Applications by Permit Type</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border-bottom border-200 pb-3 mb-3">
                                <h6 class="text-primary">Electrician Permits</h6>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-600">Draft:</span>
                                    <span class="fw-semi-bold">{{ $electrician['draft'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-600">Pending:</span>
                                    <span class="fw-semi-bold">{{ $electrician['pending'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-600">Approved:</span>
                                    <span class="fw-semi-bold text-success">{{ $electrician['approved'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-bottom border-200 pb-3 mb-3">
                                <h6 class="text-info">Supervisor Permits</h6>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-600">Draft:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['draft'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-600">Pending:</span>
                                    <span class="fw-semi-bold">{{ $supervisor['pending'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-600">Approved:</span>
                                    <span class="fw-semi-bold text-success">{{ $supervisor['approved'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-bottom border-200 pb-3 mb-3">
                                <h6 class="text-warning">Contractor Permits</h6>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-600">Draft:</span>
                                    <span class="fw-semi-bold">{{ $contractor['draft'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-600">Pending:</span>
                                    <span class="fw-semi-bold">{{ $contractor['pending'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-600">Approved:</span>
                                    <span class="fw-semi-bold text-success">{{ $contractor['approved'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions & Recent Applications --}}
    <div class="row g-0">
        <div class="col-lg-5 pe-lg-2 mb-3">
            <div class="card h-100">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ex-electrician.operator.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>New Electrician Application
                        </a>
                        <a href="{{ route('ex-supervisor.operator.create') }}" class="btn btn-info">
                            <i class="fas fa-plus me-2"></i>New Supervisor Application
                        </a>
                        <a href="{{ route('ex-contractor.operator.create') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>New Contractor Application
                        </a>
                    </div>
                    <hr class="my-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ex-electrician.operator.pending') }}" class="btn btn-sm btn-falcon-default">
                            <i class="fas fa-list me-2"></i>View My Pending
                        </a>
                        <a href="{{ route('ex-electrician.operator.rejected') }}" class="btn btn-sm btn-falcon-default">
                            <i class="fas fa-exclamation-circle me-2"></i>View Rejected
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 ps-lg-2 mb-3">
            <div class="card h-100">
                <div class="card-header bg-body-tertiary d-flex justify-content-between">
                    <h6 class="mb-0">Recent Applications</h6>
                    <a href="{{ route('ex-electrician.operator.index') }}" class="fs-10">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm table-dashboard mb-0 fs-10">
                            <thead class="bg-body-tertiary">
                                <tr>
                                    <th>Type</th>
                                    <th>Certificate No.</th>
                                    <th>Applicant</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_applications as $app)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge badge-subtle-{{ $app['type'] === 'Electrician' ? 'primary' : ($app['type'] === 'Supervisor' ? 'info' : 'warning') }}">
                                                {{ $app['type'] }}
                                            </span>
                                        </td>
                                        <td>{{ $app['certificate_number'] }}</td>
                                        <td>{{ Str::limit($app['applicant_name'], 20) }}</td>
                                        <td>
                                            <span class="badge badge-subtle-{{ $app['status_badge_color'] }}">
                                                {{ $app['status_label'] }}
                                            </span>
                                        </td>
                                        <td>{{ $app['created_at']->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-500">No applications yet</td>
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