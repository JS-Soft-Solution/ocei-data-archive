@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Pending Approval - Supervisor Applications</h4>
                        <p class="text-muted mb-0">Applications you have submitted and are awaiting review</p>
                    </div>

                    <div class="card-body">
                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('ex-supervisor.operator.pending') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search (Certificate #, NID, Name)" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Search</button>
                                    <a href="{{ route('ex-supervisor.operator.pending') }}"
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
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $app)
                                        <tr>
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
                                            <td>{{ $app->updated_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('ex-supervisor.operator.show', $app) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No pending applications found.</td>
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