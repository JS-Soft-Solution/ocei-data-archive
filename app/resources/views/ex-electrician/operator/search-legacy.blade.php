{{-- resources/views/ex-electrician/operator/search-legacy.blade.php --}}
@extends('layouts.app')

@section('title', 'Search Legacy Electrician Permits')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-search"></i>
                            Search Unclaimed Legacy Permits (Ex-Electrician)
                        </h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted">
                            Search for historical permit records that have not yet been claimed by any data entry operator.
                            Once claimed, you can digitize and verify the record.
                        </p>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('ex-electrician.operator.search') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="old_certificate_number" class="form-label">Old Certificate Number</label>
                                    <input type="text" name="old_certificate_number" id="old_certificate_number"
                                           class="form-control" value="{{ request('old_certificate_number') }}"
                                           placeholder="e.g. EL-2020-12345" autofocus>
                                </div>
                                <div class="col-md-5">
                                    <label for="applicant_name_en" class="form-label">Applicant Name (English)</label>
                                    <input type="text" name="applicant_name_en" id="applicant_name_en"
                                           class="form-control" value="{{ request('applicant_name_en') }}"
                                           placeholder="e.g. Md. Rahman">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Results Table -->
                        @if(isset($results) && $results->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Old Certificate #</th>
                                        <th>Applicant Name</th>
                                        <th>Mobile</th>
                                        <th>District</th>
                                        <th>Issue Date</th>
                                        <th>Attachments</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($results as $application)
                                        <tr>
                                            <td>
                                                <strong>{{ $application->old_certificate_number }}</strong>
                                            </td>
                                            <td>
                                                {{ $application->applicant_name_en }}
                                                <br>
                                                <small class="text-muted">{{ $application->applicant_name_bn }}</small>
                                            </td>
                                            <td>{{ $application->mobile_no }}</td>
                                            <td>{{ $application->district }}</td>
                                            <td>{{ $application->issue_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td>
                                                @if($application->attachments->count() > 0)
                                                    <span class="badge bg-success">
                                                        {{ $application->attachments->count() }} file(s)
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">No files</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('ex-electrician.operator.claim', $application) }}"
                                                      method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Claim this record? You will be responsible for digitizing it.')">
                                                        <i class="fas fa-hand-paper"></i> Claim & Edit
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $results->appends(request()->query())->links() }}
                            </div>
                        @elseif(request()->hasAny(['old_certificate_number', 'applicant_name_en']))
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i>
                                No unclaimed legacy records found matching your search.
                            </div>
                        @else
                            <div class="alert alert-light text-center border">
                                <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                <p>Start typing to search for unclaimed legacy permits.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th { font-weight: 600; }
        .badge { font-size: 0.8em; }
    </style>
@endpush
