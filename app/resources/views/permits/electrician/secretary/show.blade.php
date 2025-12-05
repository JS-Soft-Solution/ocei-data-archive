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

                        {{-- Personal Information --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Personal Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name (English):</strong> {{ $application->applicant_name_en }}
                            </div>
                            <div class="col-md-6">
                                <strong>Name (Bangla):</strong> {{ $application->applicant_name_bn }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Father's Name:</strong> {{ $application->father_name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Mother's Name:</strong> {{ $application->mother_name }}
                            </div>
                            <div class="col-md-4">
                                <strong>DOB:</strong> {{ $application->date_of_birth?->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>NID:</strong> {{ $application->nid_number }}
                            </div>
                            <div class="col-md-4">
                                <strong>Mobile:</strong> {{ $application->mobile_no }}
                            </div>
                            <div class="col-md-4">
                                <strong>Email:</strong> {{ $application->email }}
                            </div>
                        </div>

                        {{-- Address --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Address</h5>
                        <p>
                            {{ $application->village }}, {{ $application->post_office }}, {{ $application->postcode }}<br>
                            {{ $application->upazilla }}, {{ $application->district }}, {{ $application->division }}
                        </p>

                        {{-- Education --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Education</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Degree:</strong> {{ $application->degree }}
                            </div>
                            <div class="col-md-3">
                                <strong>Subject:</strong> {{ $application->subject }}
                            </div>
                            <div class="col-md-3">
                                <strong>Board:</strong> {{ $application->board }}
                            </div>
                            <div class="col-md-3">
                                <strong>Result:</strong> {{ $application->academic_result }}
                            </div>
                        </div>

                        {{-- Work Experience --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Work Experience</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Company:</strong> {{ $application->company }}
                            </div>
                            <div class="col-md-4">
                                <strong>Designation:</strong> {{ $application->designation }}
                            </div>
                            <div class="col-md-4">
                                <strong>Duration:</strong> {{ $application->total_job_duration }}
                            </div>
                        </div>

                        {{-- Certificate Details --}}
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Certificate Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Certificate Number:</strong> {{ $application->certificate_number }}
                            </div>
                            <div class="col-md-6">
                                <strong>Issue Date:</strong> {{ $application->issue_date?->format('Y-m-d') }}
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
                                                @if(in_array($attachment->file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'webp']))
                                                    <a href="{{ route('attachments.preview', $attachment) }}" 
                                                       class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </a>
                                                @endif
                                                <a href="{{ route('attachments.download', $attachment) }}"
                                                    class="btn btn-sm btn-primary" target="_blank">
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
                            <a href="{{ route('ex-electrician.secretary.pending') }}" class="btn btn-secondary">
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
                <form method="POST" action="{{ route('ex-electrician.secretary.approve', $application) }}">
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
                <form method="POST" action="{{ route('ex-electrician.secretary.reject', $application) }}">
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