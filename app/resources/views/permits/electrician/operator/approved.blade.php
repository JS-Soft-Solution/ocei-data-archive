@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Approved Applications</h4>
                <span class="badge bg-success">{{ $applications->total() }} Approved</span>
            </div>
            <div class="card-body">
                {{-- Search & Filter --}}
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name, mobile, NID..." value="{{ request('search') }}">
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
                                <th>NID</th>
                                <th>Verified By (OA)</th>
                                <th>Approved By (Secretary)</th>
                                <th>Approval Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{ $app->old_certificate_number }}</td>
                                    <td>{{ $app->applicant_name_en }}</td>
                                    <td>{{ $app->mobile_no }}</td>
                                    <td>{{ $app->nid_number }}</td>
                                    <td>{{ $app->verifiedByOfficeAssistant?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $app->approvedBySecretary?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $app->approved_at_secretary?->format('Y-m-d') ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('ex-electrician.operator.show', $app) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No approved applications found.</td>
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