@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4><i class="fas fa-user-shield"></i> Super Admin - All Applications</h4>
            </div>

            <div class="card-body">
                {{-- Warning Alert --}}
                <div class="alert alert-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Admin Override Access:</strong>
                    You can view, edit, and manage ALL applications including locked and deleted records. All actions are
                    logged.
                </div>

                {{-- Search and Filters --}}
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <input type="text" name="search" class="form-control" placeholder="Search"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted_to_office_assistant" {{ request('status') === 'submitted_to_office_assistant' ? 'selected' : '' }}>Submitted to OA
                                </option>
                                <option value="office_assistant_rejected" {{ request('status') === 'office_assistant_rejected' ? 'selected' : '' }}>OA Rejected</option>
                                <option value="submitted_to_secretary" {{ request('status') === 'submitted_to_secretary' ? 'selected' : '' }}>Submitted to Secretary</option>
                                <option value="secretary_rejected" {{ request('status') === 'secretary_rejected' ? 'selected' : '' }}>Secretary Rejected</option>
                                <option value="secretary_approved_final" {{ request('status') === 'secretary_approved_final' ? 'selected' : '' }}>Final Approved</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <select name="show_deleted" class="form-control">
                                <option value="0" {{ request('show_deleted') == '0' ? 'selected' : '' }}>Active Only</option>
                                <option value="1" {{ request('show_deleted') == '1' ? 'selected' : '' }}>Deleted Only</option>
                                <option value="all" {{ request('show_deleted') == 'all' ? 'selected' : '' }}>All (Active +
                                    Deleted)</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1 mb-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>

                {{-- Statistics --}}
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6>Total</h6>
                                <h3>{{ $statistics['total'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6>Draft</h6>
                                <h3>{{ $statistics['draft'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6>Pending</h6>
                                <h3>{{ $statistics['pending'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6>Approved</h6>
                                <h3>{{ $statistics['approved'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h6>Rejected</h6>
                                <h3>{{ $statistics['rejected'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h6>Deleted</h6>
                                <h3>{{ $statistics['deleted'] }}</h3>
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
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Certificate #</th>
                                <th>Applicant</th>
                                <th>Entry By</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr class="{{ $app->trashed() ? 'table-secondary' : '' }}">
                                    <td>{{ $app->id }}</td>
                                    <td>
                                        {{ $app->old_certificate_number }}
                                        @if($app->trashed())
                                            <br><span class="badge bg-secondary"><i class="fas fa-trash"></i> Deleted</span>
                                        @endif
                                    </td>
                                    <td>{{ $app->applicant_name_en }}</td>
                                    <td>{{ $app->entryBy?->name ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $app->status_badge_color }}">
                                            {{ $app->status_label }}
                                        </span>
                                        @if($app->isLocked())
                                            <br><i class="fas fa-lock text-warning" title="Locked"></i>
                                        @endif
                                    </td>
                                    <td>{{ $app->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($app->trashed())
                                            <form method="POST" action="{{ route('ex-electrician.admin.restore', $app->id) }}"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Restore this application?')">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('ex-electrician.admin.edit', $app) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('ex-electrician.admin.destroy', $app) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Soft delete this application?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $app->id }}">
                                            <i class="fas fa-exchange-alt"></i> Change Status
                                        </button>
                                    </td>
                                </tr>

                                {{-- Change Status Modal --}}
                                <div class="modal fade" id="statusModal{{ $app->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('ex-electrician.admin.change-status', $app) }}">
                                                @csrf
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Change Status - {{ $app->old_certificate_number }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-danger">
                                                        <strong>⚠️ Admin Override:</strong> This bypasses normal workflow!
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Current Status</label>
                                                        <input type="text" class="form-control" value="{{ $app->status_label }}"
                                                            disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">New Status *</label>
                                                        <select name="new_status" class="form-control" required>
                                                            <option value="draft">Draft</option>
                                                            <option value="submitted_to_office_assistant">Submitted to OA
                                                            </option>
                                                            <option value="office_assistant_rejected">OA Rejected</option>
                                                            <option value="submitted_to_secretary">Submitted to Secretary
                                                            </option>
                                                            <option value="secretary_rejected">Secretary Rejected</option>
                                                            <option value="secretary_approved_final">Final Approved (Locked)
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Reason for Override *</label>
                                                        <textarea name="reason" class="form-control" rows="3" required
                                                            placeholder="Explain why you're manually changing the status..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning">Change Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No applications found.</td>
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