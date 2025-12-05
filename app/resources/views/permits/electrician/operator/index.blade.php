@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>My Electrician Applications</h4>
                        <div>
                            <a href="{{ route('ex-electrician.operator.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> New Application
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Flash Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-check-circle"></i> Success!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle"></i> Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-info-circle"></i> Info:</strong> {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle"></i> Warning:</strong> {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Search and Filter Form --}}
                        <form method="GET" action="{{ route('ex-electrician.operator.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search (Certificate #, NID, Name)" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="submitted_to_office_assistant" {{ request('status') === 'submitted_to_office_assistant' ? 'selected' : '' }}>
                                            Submitted to OA</option>
                                        <option value="office_assistant_rejected" {{ request('status') === 'office_assistant_rejected' ? 'selected' : '' }}>Rejected by
                                            OA</option>
                                        <option value="submitted_to_secretary" {{ request('status') === 'submitted_to_secretary' ? 'selected' : '' }}>Submitted to
                                            Secretary</option>
                                        <option value="secretary_rejected" {{ request('status') === 'secretary_rejected' ? 'selected' : '' }}>Rejected by Secretary</option>
                                        <option value="secretary_approved_final" {{ request('status') === 'secretary_approved_final' ? 'selected' : '' }}>Final
                                            Approved</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                                        placeholder="To Date">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Search</button>
                                    <a href="{{ route('ex-electrician.operator.index') }}"
                                        class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        {{-- Per-Page Selector --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            @include('components.per-page-selector')
                            <div>
                                <span class="text-muted">Total: <strong>{{ $applications->total() }}</strong>
                                    applications</span>
                            </div>
                        </div>

                        {{-- Bulk Actions --}}
                        <form id="bulkSubmitForm" method="POST" action="{{ route('ex-electrician.operator.bulk-submit') }}">
                            @csrf
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Submit selected applications?')">
                                    <i class="fas fa-paper-plane"></i> Bulk Submit
                                </button>
                            </div>

                            {{-- Applications Table --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40"><input type="checkbox" id="selectAll"></th>
                                            <th>Old Certificate #</th>
                                            <th>Applicant Name</th>
                                            <th>Mobile</th>
                                            <th>NID</th>
                                            <th>Status</th>
                                            <th>Attachments</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($applications as $app)
                                            <tr>
                                                <td>
                                                    @if(in_array($app->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']) && $app->attachments()->count() > 0)
                                                        <input type="checkbox" name="application_ids[]" value="{{ $app->id }}"
                                                            class="bulk-checkbox">
                                                    @endif
                                                </td>
                                                <td>{{ $app->old_certificate_number }}</td>
                                                <td>
                                                    {{ $app->applicant_name_en }}<br>
                                                    <small class="text-muted">{{ $app->applicant_name_bn }}</small>
                                                </td>
                                                <td>{{ $app->mobile_no }}</td>
                                                <td>{{ $app->nid_number }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $app->status_badge_color }}">
                                                        {{ $app->status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $app->attachments()->count() }} files</span>
                                                    @if($app->attachments()->count() > 0)
                                                        <a href="{{ route('attachments.preview', $app->attachments->first()) }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-info ms-1"
                                                           title="Preview First Attachment">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $app->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($app->canBeEdited())
                                                        <a href="{{ route('ex-electrician.operator.edit', $app) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    @endif

                                                    @if($app->canBeSubmitted())
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            onclick="submitApplication({{ $app->id }})">
                                                            <i class="fas fa-paper-plane"></i> Submit
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('ex-electrician.operator.show', $app) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>

                                                    @if($app->reject_reason)
                                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $app->id }}">
                                                            <i class="fas fa-exclamation-triangle"></i> Reason
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>

                                            @if($app->reject_reason)
                                                <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Rejection Reason</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Rejected by:</strong>
                                                                    {{ $app->rejectedBy?->name ?? 'System' }}</p>
                                                                <p><strong>Reason:</strong></p>
                                                                <div class="alert alert-danger">
                                                                    {{ $app->reject_reason }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No applications found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        {{ $applications->links() }}
                    </div>
                </div>

                {{-- Claim Record Card --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Claim Existing Record</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('ex-electrician.operator.claim') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="old_certificate_number" class="form-control"
                                        placeholder="Enter Old Certificate Number" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-hand-paper"></i> Claim Record
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('selectAll').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.bulk-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });

            function submitApplication(appId) {
                if (!confirm('Submit this application for review?')) {
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/ex-electrician/operator/' + appId + '/submit';

                // Get CSRF token from the bulk form
                const bulkForm = document.getElementById('bulkSubmitForm');
                const csrfToken = bulkForm.querySelector('input[name="_token"]').value;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        </script>
    @endpush
@endsection