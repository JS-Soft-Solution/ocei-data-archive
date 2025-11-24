@extends('layouts.app')

@section('title', $title ?? 'Electrician Permits')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-center">
                <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                    <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">{{ $title }}</h5>
                </div>
                <div class="col-8 col-sm-auto ms-auto text-end ps-0">
                    <div id="orders-actions">
                        @if(request()->routeIs('permits.electrician.drafts'))
                            <a href="{{ route('permits.electrician.search') }}" class="btn btn-falcon-default btn-sm">
                                <span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span>
                                <span class="d-none d-sm-inline-block ms-1">New Entry</span>
                            </a>
                        @endif
                        <button class="btn btn-falcon-default btn-sm mx-2" type="button">
                            <span class="fas fa-filter" data-fa-transform="shrink-3 down-2"></span>
                            <span class="d-none d-sm-inline-block ms-1">Filter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive scrollbar">
                <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
                    <thead class="bg-200">
                    <tr>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="cert_no">Cert No</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="name">Applicant</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="mobile">Mobile</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="operator">Entry By</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap text-center" data-sort="status">Status</th>
                        <th class="no-sort text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($applications as $app)
                        <tr class="btn-reveal-trigger">
                            <td class="cert_no align-middle white-space-nowrap py-2">
                                <strong>{{ $app->old_certificate_number }}</strong>
                            </td>
                            <td class="name align-middle py-2">{{ $app->applicant_name_en }}</td>
                            <td class="mobile align-middle py-2">{{ $app->mobile_no }}</td>
                            <td class="operator align-middle py-2">{{ $app->entryUser->full_name ?? '-' }}</td>
                            <td class="status align-middle text-center fs-9 white-space-nowrap">
                                @php
                                    $badgeClass = match($app->application_status) {
                                        'approved' => 'success',
                                        'rejected' => 'secondary',
                                        'submitted' => 'warning',
                                        'verified' => 'info',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge badge rounded-pill d-block badge-subtle-{{ $badgeClass }}">
                                {{ ucfirst($app->application_status) }}
                            </span>
                            </td>
                            <td class="py-2 align-middle white-space-nowrap text-end">
                                <div class="dropdown font-sans-serif position-static">
                                    <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end border py-0">
                                        <div class="py-2">
                                            {{-- ACTIONS BASED ON ROLE AND STATUS --}}
                                            @if(auth()->user()->hasRole('data_entry_operator') && in_array($app->application_status, ['draft', 'rejected']))
                                                <a class="dropdown-item" href="{{ route('permits.electrician.edit', $app->id) }}">Edit</a>
                                                <form action="{{ route('permits.electrician.submit', $app->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">Submit for Approval</button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->hasRole('office_assistant') && $app->application_status == 'submitted')
                                                <a class="dropdown-item" href="{{ route('permits.electrician.edit', $app->id) }}">View/Verify</a> {{-- View calls Edit mostly in read-only mode --}}
                                                <form action="{{ route('permits.electrician.verify', $app->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">Approve (Verify)</button>
                                                </form>
                                                <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">Reject</button>
                                            @endif

                                            @if(auth()->user()->hasRole('secretary') && $app->application_status == 'verified')
                                                <form action="{{ route('permits.electrician.approve', $app->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">Final Approve</button>
                                                </form>
                                                <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">Reject</button>
                                            @endif

                                            @if(auth()->user()->hasRole(['super_admin', 'secretary', 'chairman']))
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('permits.electrician.audit', $app->id) }}">Audit History</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- REJECT MODAL --}}
                                <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ auth()->user()->hasRole('secretary') ? route('permits.electrician.secretary.reject', $app->id) : route('permits.electrician.reject', $app->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header"><h5 class="modal-title">Reject Application</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Rejection Reason</label>
                                                        <textarea class="form-control" name="reason" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Confirm Reject</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">No applications found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-center">
            {{ $applications->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
