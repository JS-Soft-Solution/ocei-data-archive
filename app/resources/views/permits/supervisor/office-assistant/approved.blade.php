@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>My Approved Applications</h4>
                <span class="badge bg-success">{{ $applications->total() }} Approved by Me</span>
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
                                <th>Submitted By</th>
                                <th>Current Status</th>
                                <th>Verified Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{ $app->old_certificate_number }}</td>
                                    <td>{{ $app->applicant_name_en }}</td>
                                    <td>{{ $app->mobile_no }}</td>
                                    <td>{{ $app->entryBy?->full_name }}</td>
                                    <td>
                                        @if($app->status === 'submitted_to_secretary')
                                            <span class="badge bg-info">With Secretary</span>
                                        @elseif($app->status === 'secretary_approved_final')
                                            <span class="badge bg-success">Final Approved</span>
                                        @elseif($app->status === 'secretary_rejected')
                                            <span class="badge bg-danger">Rejected by Secretary</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $app->status_label }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $app->verified_at_office_assistant?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('ex-supervisor.office-assistant.show', $app) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No approved applications found.</td>
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