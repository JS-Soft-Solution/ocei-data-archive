@extends('layouts.app')

@section('title', 'Office Assistant Dashboard')

@section('content')
    {{-- Top Statistics Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Pending Review</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['pending'] }}</p>
                            <span class="badge badge-subtle-warning rounded-pill fs-11">Awaiting Review</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-warning">
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
                    <h6 class="mb-0 mt-2">Approved Today</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                {{ $electrician['approved_today'] + $supervisor['approved_today'] + $contractor['approved_today'] }}
                            </p>
                            <span class="badge badge-subtle-success rounded-pill fs-11">Verified</span>
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
                    <h6 class="mb-0 mt-2">Rejected Today</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                {{ $electrician['rejected_today'] + $supervisor['rejected_today'] + $contractor['rejected_today'] }}
                            </p>
                            <span class="badge badge-subtle-danger rounded-pill fs-11">Sent Back</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Processed This Month</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $total['processed_this_month'] }}</p>
                            <span class="badge badge-subtle-info rounded-pill fs-11">Total</span>
                        </div>
                        <div class="col-auto">
                            <div class="fs-6 text-info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permit Type Pending Breakdown --}}
    <div class="row g-0 mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h6 class="mb-0">Pending by Permit Type</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h2 class="text-primary">{{ $electrician['pending'] }}</h2>
                            <p class="text-600">Electrician Permits</p>
                            <a href="{{ route('ex-electrician.office-assistant.pending') }}" class="btn btn-sm btn-primary">
                                Review Now
                            </a>
                        </div>
                        <div class="col-md-4 text-center">
                            <h2 class="text-info">{{ $supervisor['pending'] }}</h2>
                            <p class="text-600">Supervisor Permits</p>
                            <a href="{{ route('ex-supervisor.office-assistant.pending') }}" class="btn btn-sm btn-info">
                                Review Now
                            </a>
                        </div>
                        <div class="col-md-4 text-center">
                            <h2 class="text-warning">{{ $contractor['pending'] }}</h2>
                            <p class="text-600">Contractor Permits</p>
                            <a href="{{ route('ex-contractor.office-assistant.pending') }}" class="btn btn-sm btn-warning">
                                Review Now
                            </a>
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
                    <h6 class="mb-0">Pending Applications (All Types)</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('ex-electrician.office-assistant.approved') }}"
                            class="btn btn-sm btn-falcon-default">
                            View Approved
                        </a>
                        <a href="{{ route('ex-electrician.office-assistant.rejected') }}"
                            class="btn btn-sm btn-falcon-default">
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
                                    <th>Submitted At</th>
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
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-500">
                                            <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                                            <p>No pending applications! All caught up.</p>
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