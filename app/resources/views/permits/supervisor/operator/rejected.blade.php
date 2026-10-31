@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-exclamation-triangle text-warning"></i> Rejected Applications - Supervisor
                        </h4>
                        <p class="text-muted mb-0">Applications that were rejected and need to be fixed and resubmitted</p>
                    </div>

                    <div class="card-body">
                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('ex-supervisor.operator.rejected') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search (Certificate #, NID, Name)" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Search</button>
                                    <a href="{{ route('ex-supervisor.operator.rejected') }}"
                                        class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        {{-- Applications Table --}}
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
                                        <th>Status</th>
                                        <th>Rejected By</th>
                                        <th>Rejection Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $app)
                                        <tr class="table-warning">
                                            <td>{{ $app->old_certificate_number }}</td>
                                            <td>
                                                {{ $app->applicant_name_en }}<br>
                                                <small class="text-muted">{{ $app->applicant_name_bn }}</small>
                                            </td>
                                            <td>{{ $app->mobile_no }}</td>
                                            <td>
                                                <span class="badge bg-{{ $app->status_badge_color }}">
                                                    {{ $app->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $app->rejectedBy?->name ?? 'System' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#reasonModal{{ $app->id }}">
                                                    <i class="fas fa-eye"></i> View Reason
                                                </button>
                                            </td>
                                            <td>
                                                <a href="{{ route('ex-supervisor.operator.edit', $app) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit & Resubmit
                                                </a>
                                            </td>
                                        </tr>

                                        {{-- Rejection Reason Modal --}}
                                        <div class="modal fade" id="reasonModal{{ $app->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Rejection Reason</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Rejected by:</strong>
                                                            {{ $app->rejectedBy?->name ?? 'System' }}</p>
                                                        <p><strong>Rejection Date:</strong>
                                                            {{ $app->rejected_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                                                        <p><strong>Reason:</strong></p>
                                                        <div class="alert alert-danger">
                                                            {{ $app->reject_reason ?? 'No reason provided' }}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <a href="{{ route('ex-supervisor.operator.edit', $app) }}"
                                                            class="btn btn-primary">
                                                            <i class="fas fa-edit"></i> Edit Application
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-success">
                                                <i class="fas fa-check-circle"></i> Great! No rejected applications.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection