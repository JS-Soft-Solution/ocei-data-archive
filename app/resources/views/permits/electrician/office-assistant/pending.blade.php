@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Pending Applications - Office Assistant Review</h4>
                        <span class="badge bg-info">{{ $applications->total() }} Pending</span>
                    </div>

                    <div class="card-body">
                        {{-- Search and Filter Form --}}
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search"
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info w-100">Search</button>
                                </div>
                            </div>
                        </form>

                        {{-- Per-Page Selector --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            @include('components.per-page-selector')
                            <div>
                                <span class="text-muted">Total: <strong>{{ $applications->total() }}</strong> pending</span>
                            </div>
                        </div>

                        {{-- Bulk Actions Form --}}
                        <form id="bulkActionForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <button type="button" class="btn btn-success" onclick="bulkApprove()">
                                    <i class="fas fa-check"></i> Bulk Approve
                                </button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#bulkRejectModal">
                                    <i class="fas fa-times"></i> Bulk Reject
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40"><input type="checkbox" id="selectAll"></th>
                                            <th>Certificate #</th>
                                            <th>Applicant</th>
                                            <th>Mobile</th>
                                            <th>NID</th>
                                            <th>Attachments</th>
                                            <th>Submitted By</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($applications as $app)
                                            <tr>
                                                <td><input type="checkbox" name="application_ids[]" value="{{ $app->id }}"
                                                        class="bulk-checkbox"></td>
                                                <td>{{ $app->old_certificate_number }}</td>
                                                <td>
                                                    {{ $app->applicant_name_en }}<br>
                                                    <small class="text-muted">{{ $app->applicant_name_bn }}</small>
                                                </td>
                                                <td>{{ $app->mobile_no }}</td>
                                                <td>{{ $app->nid_number }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $app->attachments->count() }} files</span>
                                                    @if($app->attachments->count() > 0)
                                                        <a href="{{ route('attachments.preview', $app->attachments->first()) }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-info ms-1"
                                                           title="Preview First Attachment">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $app->entryBy?->full_name }}</td>
                                                <td>{{ $app->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <a href="{{ route('ex-electrician.office-assistant.show', $app) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Review
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        onclick="singleApprove({{ $app->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="singleReject({{ $app->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No pending applications.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Reject Modal --}}
    <div class="modal fade" id="bulkRejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('ex-electrician.office-assistant.bulk-reject') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Reject Applications</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Common Rejection Reason *</label>
                            <textarea name="reject_reason" class="form-control" rows="4" required
                                placeholder="Provide a clear reason that applies to all selected applications..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <strong>Note:</strong> All selected applications will receive the same rejection reason.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Reject Selected</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('selectAll').addEventListener('change', function () {
                document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = this.checked);
            });

            function bulkApprove() {
                const selected = Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
                if (selected.length === 0) {
                    alert('Please select at least one application');
                    return;
                }
                if (!confirm(`Approve ${selected.length} application(s)?`)) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("ex-electrician.office-assistant.bulk-approve") }}';
                form.innerHTML = `
                                                        @csrf
                                                        ${selected.map(id => `<input type="hidden" name="application_ids[]" value="${id}">`).join('')}
                                                    `;
                document.body.appendChild(form);
                form.submit();
            }

            document.querySelector('#bulkRejectModal form').addEventListener('submit', function (e) {
                const selected = Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
                if (selected.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one application');
                    return false;
                }

                // Remove old hidden inputs
                const oldInputs = this.querySelectorAll('input[name="application_ids[]"]');
                oldInputs.forEach(input => input.remove());

                // Add new hidden inputs for each selected application
                selected.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'application_ids[]';
                    input.value = id;
                    this.appendChild(input);
                });
            });

            // Single approve function
            function singleApprove(appId) {
                Swal.fire({
                    title: 'Approve Application?',
                    text: 'This will forward the application to Secretary for final approval.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/ex-electrician/office-assistant/${appId}/approve`;
                        form.innerHTML = '@csrf';
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            // Single reject function
            function singleReject(appId) {
                Swal.fire({
                    title: 'Reject Application?',
                    html: '<textarea id="rejectReason" class="swal2-textarea" placeholder="Enter rejection reason..." required></textarea>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const reason = document.getElementById('rejectReason').value;
                        if (!reason) {
                            Swal.showValidationMessage('Please enter a rejection reason');
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/ex-electrician/office-assistant/${appId}/reject`;
                        form.innerHTML = `
                                    @csrf
                                    <input type="hidden" name="reject_reason" value="${result.value}">
                                `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection