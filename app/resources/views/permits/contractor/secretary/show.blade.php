@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Secretary Review - {{ $application->old_certificate_number }}</h4>
                        <span class="badge bg-light text-dark">{{ $application->status_label }}</span>
                    </div>

                    <div class="card-body">
                        {{-- Alert: Office Assistant Verification --}}
                        <div class="alert alert-info">
                            <strong><i class="fas fa-check-circle"></i> Verified by Office Assistant:</strong>
                            {{ $application->verifiedByOfficeAssistant?->name }}<br>
                            <small>Verified on:
                                {{ $application->verified_at_office_assistant?->format('d M Y, h:i A') }}</small>
                        </div>

                        {{-- Organization Information --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-building me-1"></i> Organization Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Organization Name (English):</strong> <strong>{{ $application->company_name_en ?? $application->applicant_name_en ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <strong>Organization Name (Bangla):</strong> {{ $application->company_name_bn ?? $application->applicant_name_bn ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Owner / Shareholder:</strong> {{ $application->owner_shareholder_name ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Approved Class:</strong> <span class="badge bg-secondary">{{ $application->class ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Book Number:</strong> {{ $application->book_number ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Trade License:</strong> {{ $application->trade_license_number ?? 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Mobile:</strong> {{ $application->mobile_no ?? 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Email:</strong> {{ $application->email ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Assigned Supervisor --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-tie me-1"></i> Assigned Supervisor</h5>
                        <p class="mb-0"><strong>Supervisor Name / Certificate #:</strong> {{ $application->elb_certified_supervisor_no ?? 'N/A' }}</p>

                        {{-- Testing Equipment --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-tools me-1"></i> Testing Instruments</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Earth Tester (ET) Serial:</strong> {{ $application->et_serial_no ?? 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Megger (MG) Serial:</strong> {{ $application->mg_serial_no ?? 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Clamp Meter (CM) Serial:</strong> {{ $application->cm_serial_no ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Business Address --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-map-marker-alt me-1"></i> Business Address</h5>
                        <p>
                            {{ $application->village ?? $application->boa_village ?? 'N/A' }}<br>
                            @if($application->upazilla || $application->district || $application->division)
                                {{ $application->upazilla ?? $application->boa_upozila ?? '' }}, {{ $application->district ?? $application->boa_district ?? '' }}, {{ $application->division ?? $application->boa_division ?? '' }}
                            @endif
                        </p>

                        {{-- Certificate Details --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-certificate me-1"></i> Certificate Details</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Old Certificate Number:</strong> {{ $application->old_certificate_number }}
                            </div>
                            <div class="col-md-4">
                                <strong>Issue Date:</strong> {{ $application->issue_date?->format('Y-m-d') ?? ($application->issue_date ?? 'N/A') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Expiry Date:</strong> {{ $application->expiry_date?->format('Y-m-d') ?? ($application->expiry_date ?? 'N/A') }}
                            </div>
                        </div>

                        {{-- Attachments --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Attachments ({{ $application->attachments->count() }})</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($application->attachments as $attachment)
                                        <tr>
                                            <td><span
                                                    class="badge bg-secondary">{{ $attachment->attachment_type ?? 'General' }}</span>
                                            </td>
                                            <td>{{ $attachment->original_name }}</td>
                                            <td>{{ $attachment->file_size_human }}</td>
                                            <td>
                                                <a href="{{ route('attachments.download', $attachment) }}"
                                                    class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('ex-contractor.secretary.pending') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Queue
                            </a>

                            <div>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-ban"></i> Reject to Operator
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="fas fa-lock"></i> Final Approve & LOCK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar: History --}}
            <div class="col-md-4">
                @include('permits.components._history_timeline', ['histories' => $application->histories])
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('ex-contractor.secretary.approve', $application) }}">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">🔒 Final Approval Confirmation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>⚠️ Important:</strong> This action will:
                            <ul class="mb-0 mt-2">
                                <li><strong>Lock this record permanently</strong></li>
                                <li>Only Super Admin can edit after this</li>
                                <li>Status becomes "Secretary Approved Final"</li>
                            </ul>
                        </div>
                        <p class="mb-0">Are you certain this application is complete and accurate?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-lock"></i> Yes, Approve & Lock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('ex-contractor.secretary.reject', $application) }}">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Reject Application</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Note:</strong> Application will return directly to operator (skips Office Assistant on
                            resubmit).
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="reject_reason" class="form-control" rows="5" required
                                placeholder="Provide a detailed reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection