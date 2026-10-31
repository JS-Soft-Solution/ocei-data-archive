@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Review Application - {{ $application->old_certificate_number }}</h4>
                        <span
                            class="badge bg-{{ $application->status_badge_color }}">{{ $application->status_label }}</span>
                    </div>

                    <div class="card-body">
                        {{-- Personal Information --}}
                        <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name (English):</strong> {{ $application->applicant_name_en }}
                            </div>
                            <div class="col-md-6">
                                <strong>Name (Bangla):</strong> {{ $application->applicant_name_bn }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Father's Name:</strong> {{ $application->father_name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Mother's Name:</strong> {{ $application->mother_name }}
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
                                <strong>DOB:</strong> {{ $application->date_of_birth?->format('Y-m-d') }}
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Degree:</strong> {{ $application->degree }}
                            </div>
                            <div class="col-md-6">
                                <strong>Subject:</strong> {{ $application->subject }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Board:</strong> {{ $application->board }}
                            </div>
                            <div class="col-md-4">
                                <strong>Result:</strong> {{ $application->academic_result }}
                            </div>
                            <div class="col-md-4">
                                <strong>Year:</strong> {{ $application->passing_year }}
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
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Attachments</h5>
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
                            <a href="{{ route('ex-electrician.office-assistant.pending') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>

                            <div>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                <form method="POST"
                                    action="{{ route('ex-electrician.office-assistant.approve', $application) }}"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Approve this application and forward to Secretary?')">
                                        <i class="fas fa-check"></i> Approve & Forward to Secretary
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar: History --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Audit Trail</h5>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        @foreach($application->histories->sortByDesc('performed_at') as $history)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $history->action_description }}</strong>
                                    <small class="text-muted">{{ $history->performed_at->diffForHumans() }}</small>
                                </div>
                                <small class="text-muted">
                                    By: {{ $history->performedBy?->name ?? 'System' }}<br>
                                    {{ $history->performed_at->format('Y-m-d H:i:s') }}
                                </small>
                                @if($history->notes)
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small>{{ $history->notes }}</small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('ex-electrician.office-assistant.reject', $application) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="reject_reason" class="form-control" rows="5" required
                                placeholder="Provide a clear, specific reason for rejection..."></textarea>
                        </div>
                        <div class="alert alert-info">
                            <strong>Tips for good rejection reasons:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Be specific about what needs fixing</li>
                                <li>Mention exact field names if applicable</li>
                                <li>Explain why it's being rejected</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Reject Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection