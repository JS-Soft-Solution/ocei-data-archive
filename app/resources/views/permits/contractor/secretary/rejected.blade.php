@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>My Rejected Applications (Not Yet Resubmitted)</h4>
                <span class="badge bg-danger">{{ $applications->total() }} Awaiting Resubmission</span>
            </div>
            <div class="card-body">
                {{-- Search & Filter --}}
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" placeholder="From"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control" placeholder="To"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

        {{-- Per-Page Selector --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            @include('components.per-page-selector')
            <div>
                <span class="text-muted">Total: <strong>{{ $applications->total() }}</strong> applications</span>
            </div>
        </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Old Certificate #</th>
                                <th>Applicant Name</th>
                                <th>Mobile</th>
                                <th>Verified By (OA)</th>
                                <th>Rejected Date</th>
                                <th>Reject Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{ $app->old_certificate_number }}</td>
                                    <td>{{ $app->applicant_name_en }}</td>
                                    <td>{{ $app->mobile_no }}</td>
                                    <td>{{ $app->verifiedByOfficeAssistant?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $app->rejected_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#reasonModal{{ $app->id }}">
                                            <i class="fas fa-info-circle"></i> View Reason
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('ex-contractor.secretary.show', $app) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>

                                {{-- Reason Modal --}}
                                <div class="modal fade" id="reasonModal{{ $app->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Rejection Reason</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Certificate:</strong> {{ $app->old_certificate_number }}</p>
                                                <p><strong>Applicant:</strong> {{ $app->applicant_name_en }}</p>
                                                <hr>
                                                <p>{{ $app->reject_reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No rejected applications awaiting resubmission.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $applications->links() }}
            </div>
        </div>
    </div>
@endsection