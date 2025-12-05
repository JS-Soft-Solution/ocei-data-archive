@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4>View Electrician Application - {{ $application->old_certificate_number }}</h4>
                            <span class="badge bg-{{ $application->status_badge_color }}" style="font-size: 1rem;">
                                {{ $application->status_label }}
                            </span>
                        </div>
                        <div>
                            @if($application->canBeEdited())
                                <a href="{{ route('ex-electrician.operator.edit', $application) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Application
                                </a>
                            @endif
                            <a href="{{ route('ex-electrician.operator.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Personal Information --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-user"></i> Personal Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Old Certificate Number:</strong> {{ $application->old_certificate_number }}
                                    </p>
                                    <p><strong>Class:</strong> {{ $application->class ?? 'N/A' }}</p>
                                    <p><strong>Book Number:</strong> {{ $application->book_number ?? 'N/A' }}</p>
                                    <p><strong>Applicant Name (English):</strong>
                                        {{ $application->applicant_name_en ?? 'N/A' }}</p>
                                    <p><strong>Applicant Name (Bangla):</strong>
                                        {{ $application->applicant_name_bn ?? 'N/A' }}</p>
                                    <p><strong>Father's Name:</strong> {{ $application->father_name ?? 'N/A' }}</p>
                                    <p><strong>Mother's Name:</strong> {{ $application->mother_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Mobile Number:</strong> {{ $application->mobile_no ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $application->email ?? 'N/A' }}</p>
                                    <p><strong>Date of Birth:</strong> {{ $application->date_of_birth ?? 'N/A' }}</p>
                                    <p><strong>NID Number:</strong> {{ $application->nid_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Address Information --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-map-marker-alt"></i> Address Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Village:</strong> {{ $application->village ?? 'N/A' }}</p>
                                    <p><strong>Post Office:</strong> {{ $application->post_office ?? 'N/A' }}</p>
                                    <p><strong>Postcode:</strong> {{ $application->postcode ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Upazilla:</strong> {{ $application->upazilla ?? 'N/A' }}</p>
                                    <p><strong>District:</strong> {{ $application->district ?? 'N/A' }}</p>
                                    <p><strong>Division:</strong> {{ $application->division ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Education Information --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-graduation-cap"></i> Education Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Degree:</strong> {{ $application->degree ?? 'N/A' }}</p>
                                    <p><strong>Subject:</strong> {{ $application->subject ?? 'N/A' }}</p>
                                    <p><strong>Board:</strong> {{ $application->board ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Result:</strong> {{ $application->academic_result ?? 'N/A' }}</p>
                                    <p><strong>Passing Year:</strong> {{ $application->passing_year ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Work Experience --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-briefcase"></i> Work Experience</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Company:</strong> {{ $application->company ?? 'N/A' }}</p>
                                    <p><strong>Designation:</strong> {{ $application->designation ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Job Duration:</strong> {{ $application->total_job_duration ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Certificate Information --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-certificate"></i> Certificate Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Certificate Number:</strong> {{ $application->certificate_number ?? 'N/A' }}
                                    </p>
                                    <p><strong>Issue Date:</strong> {{ $application->issue_date ?? 'N/A' }}</p>
                                    <p><strong>Expiry Date:</strong> {{ $application->expiry_date ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Renewal Period:</strong> {{ $application->renewal_period ?? 'N/A' }}</p>
                                    <p><strong>Last Renewal Date:</strong> {{ $application->last_renewal_date ?? 'N/A' }}
                                    </p>
                                    <p><strong>Result:</strong> {{ $application->result ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Attachments --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-paperclip"></i> Attachments</h5>
                            @if($application->attachments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>File Name</th>
                                                <th>Type</th>
                                                <th>Size</th>
                                                <th>Uploaded</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($application->attachments as $attachment)
                                                <tr>
                                                    <td>{{ $attachment->original_name }}</td>
                                                    <td>{{ $attachment->attachment_type ?? 'General' }}</td>
                                                    <td>{{ $attachment->human_readable_size }}</td>
                                                    <td>{{ $attachment->uploaded_at->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        @if(in_array($attachment->file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'webp']))
                                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                                data-bs-target="#previewModal{{ $attachment->id }}">
                                                                <i class="fas fa-eye"></i> Preview
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('attachments.download', $attachment) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </td>
                                                </tr>

                                                {{-- Preview Modal --}}
                                                @if(in_array($attachment->file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'webp']))
                                                    <div class="modal fade" id="previewModal{{ $attachment->id }}" tabindex="-1">
                                                        <div class="modal-dialog modal-xl">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ $attachment->original_name }}</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    @if($attachment->file_extension === 'pdf')
                                                                        <iframe src="{{ route('attachments.preview', $attachment) }}"
                                                                            style="width: 100%; height: 600px;" frameborder="0"></iframe>
                                                                    @else
                                                                        <img src="{{ route('attachments.preview', $attachment) }}"
                                                                            class="img-fluid" alt="{{ $attachment->original_name }}"
                                                                            style="max-height: 600px;">
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <a href="{{ route('attachments.download', $attachment) }}"
                                                                        class="btn btn-primary">
                                                                        <i class="fas fa-download"></i> Download
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No attachments uploaded yet.</p>
                            @endif
                        </div>

                        {{-- Workflow Information --}}
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-history"></i> Workflow Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Entry By:</strong> {{ $application->entryBy?->name ?? 'N/A' }}</p>
                                    <p><strong>Entry Date:</strong>
                                        {{ $application->entry_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                                    <p><strong>Last Updated:</strong>
                                        {{ $application->last_updated_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    @if($application->reject_reason)
                                        <div class="alert alert-danger">
                                            <strong>Rejection Reason:</strong><br>
                                            {{ $application->reject_reason }}<br>
                                            <small class="text-muted">Rejected by:
                                                {{ $application->rejectedBy?->name ?? 'System' }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Audit History --}}
                        @if($application->histories->count() > 0)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2"><i class="fas fa-clipboard-list"></i> Audit History</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date/Time</th>
                                                <th>Action</th>
                                                <th>Performed By</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($application->histories->sortByDesc('performed_at') as $history)
                                                <tr>
                                                    <td>{{ $history->performed_at->format('Y-m-d H:i:s') }}</td>
                                                    <td>{{ $history->action_description }}</td>
                                                    <td>{{ $history->performedBy?->name ?? 'System' }}</td>
                                                    <td>
                                                        @if($history->notes)
                                                            {{ $history->notes }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection