@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-user-tie"></i> Chairman - Approved Contractor Permits</h4>
            </div>

            <div class="card-body">
                {{-- Info Alert --}}
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Read-Only Access:</strong>
                    You can view all final approved applications but cannot make any modifications.
                </div>

                {{-- Search and Filter --}}
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by certificate #, name, NID, mobile" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <select name="division" class="form-control">
                                <option value="">All Divisions</option>
                                <option value="Dhaka" {{ request('division') === 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chittagong" {{ request('division') === 'Chittagong' ? 'selected' : '' }}>
                                    Chittagong</option>
                                <option value="Rajshahi" {{ request('division') === 'Rajshahi' ? 'selected' : '' }}>Rajshahi
                                </option>
                                <option value="Khulna" {{ request('division') === 'Khulna' ? 'selected' : '' }}>Khulna
                                </option>
                                <option value="Barisal" {{ request('division') === 'Barisal' ? 'selected' : '' }}>Barisal
                                </option>
                                <option value="Sylhet" {{ request('division') === 'Sylhet' ? 'selected' : '' }}>Sylhet
                                </option>
                                <option value="Rangpur" {{ request('division') === 'Rangpur' ? 'selected' : '' }}>Rangpur
                                </option>
                                <option value="Mymensingh" {{ request('division') === 'Mymensingh' ? 'selected' : '' }}>
                                    Mymensingh</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Total Approved</h6>
                                <h3>{{ $applications->total() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6>This Month</h6>
                                <h3>{{ $applications->where('approved_at_secretary', '>=', now()->startOfMonth())->count() }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <thead class="table-success">
                            <tr>
                                <th>Certificate #</th>
                                <th>Applicant Name</th>
                                <th>NID</th>
                                <th>District</th>
                                <th>Division</th>
                                <th>Approved By</th>
                                <th>Approved Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td><strong>{{ $app->old_certificate_number }}</strong></td>
                                    <td>
                                        {{ $app->applicant_name_en }}<br>
                                        <small class="text-muted">{{ $app->applicant_name_bn }}</small>
                                    </td>
                                    <td>{{ $app->nid_number }}</td>
                                    <td>{{ $app->district }}</td>
                                    <td>{{ $app->division }}</td>
                                    <td>{{ $app->approvedBySecretary?->name }}</td>
                                    <td>{{ $app->approved_at_secretary?->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('ex-contractor.chairman.show', $app) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Details
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