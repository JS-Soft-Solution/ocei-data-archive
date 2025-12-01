@extends('layouts.app')

@section('title', 'Secretary Dashboard')

@section('content')
    {{-- Top Statistics Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Pending Final Approval</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['pending'] }}</p>
                            <span class="badge badge-subtle-warning rounded-pill fs-11">Awaiting Action</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-warning">
                                <i class="fas fa-stamp"></i>
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
                            <span class="badge badge-subtle-success rounded-pill fs-11">Finalized</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-success">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Electrician - Pending</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $electrician['pending'] }}</p>
                            <span class="badge badge-subtle-primary rounded-pill fs-11">Electrician</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-primary">
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
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $supervisor['pending'] + $contractor['pending'] }}
                            </p>
                            <span class="badge badge-subtle-info rounded-pill fs-11">Combined</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-info">
                                <i class="fas fa-hard-hat"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Summary --}}
    <div class="row g-0 mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Monthly Approval Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center border-end">
                                <h6 class="text-primary">Electrician</h6>
                                <h3 class="mb-0">{{ $electrician['approved_this_month'] }}</h3>
                                <small class="text-500">Approved this month</small>
                                <div class="mt-2">
                                    <a href="{{ route('ex-electrician.secretary.pending') }}"
                                        class="btn btn-sm btn-primary">
                                        Review Pending ({{ $electrician['pending'] }})
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center border-end">
                                <h6 class="text-info">Supervisor</h6>
                                <h3 class="mb-0">{{ $supervisor['approved_this_month'] }}</h3>
                                <small class="text-500">Approved this month</small>
                                <div class="mt-2">
                                    <a href="{{ route('ex-supervisor.secretary.pending') }}" class="btn btn-sm btn-info">
                                        Review Pending ({{ $supervisor['pending'] }})
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-warning">Contractor</h6>
                                <h3 class="mb-0">{{ $contractor['approved_this_month'] }}</h3>
                                <small class="text-500">Approved this month</small>
                                <div class="mt-2">
                                    <a href="{{ route('ex-contractor.secretary.pending') }}" class="btn btn-sm btn-warning">
                                        Review Pending ({{ $contractor['pending'] }})
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Applications Table --}}
    <div class="row g-0">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Applications Pending Final Approval</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('ex-electrician.secretary.approved') }}" class="btn btn-sm btn-falcon-default">
                            View Approved
                        </a>
                        <a href="{{ route('ex-electrician.secretary.rejected') }}" class="btn btn-sm btn-falcon-default">
                            View Rejected
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
                                    <th>Verified By OA</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending_applications as $app)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge badge-subtle-{{ $app['type'] === 'Electrician' ? 'primary' : ($app['type'] === 'Supervisor' ? 'info' : 'warning') }}">
                                                {{ $app['type'] }}
                                            </span>
                                        </td>
                                        <td>{{ $app['certificate_number'] }}</td>
                                        <td>{{ Str::limit($app['applicant_name'], 30) }}</td>
                                        <td>{{ $app['submitted_at']?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <a href="{{ $app['view_url'] }}" class="btn btn-sm btn-falcon-primary">
                                                <i class="fas fa-stamp"></i> Review & Approve
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-500">
                                            <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                                            <p>No pending applications for final approval!</p>
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