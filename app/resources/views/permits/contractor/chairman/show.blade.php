@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-user-tie"></i> Chairman - View Application</h4>
                <span class="badge bg-success"><i class="fas fa-lock"></i> LOCKED - Final Approved</span>
            </div>

            <div class="card-body">
                {{-- Approval Info --}}
                <div class="alert alert-success">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-check-circle"></i> Approved by Secretary:</strong>
                            {{ $application->approvedBySecretary?->name }}<br>
                            <small>Date: {{ $application->approved_at_secretary?->format('d M Y, h:i A') }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Verified by Office Assistant:</strong>
                            {{ $application->verifiedByOfficeAssistant?->name }}<br>
                            <small>Date: {{ $application->verified_at_office_assistant?->format('d M Y, h:i A') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Certificate Info Card --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Certificate Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Old Certificate Number:</strong> {{ $application->old_certificate_number }}</p>
                                <p><strong>Certificate Number:</strong> {{ $application->certificate_number }}</p>
                                <p><strong>Issue Date:</strong> {{ $application->issue_date?->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Expiry Date:</strong> {{ $application->expiry_date?->format('Y-m-d') }}</p>
                                <p><strong>Renewal Period:</strong> {{ $application->renewal_period }} years</p>
                                <p><strong>Result:</strong> {{ $application->result }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Information --}}
                <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Name (English):</strong> {{ $application->applicant_name_en }}</p>
                        <p><strong>Name (Bangla):</strong> {{ $application->applicant_name_bn }}</p>
                        <p><strong>Father's Name:</strong> {{ $application->father_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Mother's Name:</strong> {{ $application->mother_name }}</p>
                        <p><strong>Date of Birth:</strong> {{ $application->date_of_birth?->format('Y-m-d') }}</p>
                        <p><strong>NID:</strong> {{ $application->nid_number }}</p>
                    </div>
                </div>

                {{-- Contact Information --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4">Contact Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Mobile:</strong> {{ $application->mobile_no }}</p>
                        <p><strong>Email:</strong> {{ $application->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>District:</strong> {{ $application->district }}</p>
                        <p><strong>Division:</strong> {{ $application->division }}</p>
                    </div>
                </div>

                {{-- Address --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4">Full Address</h5>
                <p>
                    {{ $application->village }}<br>
                    {{ $application->post_office }}, {{ $application->postcode }}<br>
                    {{ $application->upazilla }}, {{ $application->district }}, {{ $application->division }}
                </p>

                {{-- Education --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4">Education</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Degree</th>
                                <th>Subject</th>
                                <th>Board</th>
                                <th>Result</th>
                                <th>Passing Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $application->degree }}</td>
                                <td>{{ $application->subject }}</td>
                                <td>{{ $application->board }}</td>
                                <td>{{ $application->academic_result }}</td>
                                <td>{{ $application->passing_year }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Work Experience --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4">Work Experience</h5>
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Company:</strong> {{ $application->company }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Designation:</strong> {{ $application->designation }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Total Duration:</strong> {{ $application->total_job_duration }}</p>
                    </div>
                </div>

                {{-- Attachments --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4">Attachments ({{ $application->attachments->count() }})</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>File Name</th>
                                <th>Size</th>
                                <th>Uploaded By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($application->attachments as $attachment)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $attachment->attachment_type ?? 'General' }}</span>
                                    </td>
                                    <td>{{ $attachment->original_name }}</td>
                                    <td>{{ $attachment->file_size_human }}</td>
                                    <td>{{ $attachment->uploadedBy?->name }}</td>
                                    <td>
                                        <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-sm btn-info"
                                            target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Audit Trail Summary --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4">Workflow History Summary</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Stage</th>
                                <th>Performed By</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-plus-circle text-success"></i> Created</td>
                                <td>{{ $application->entryBy?->name }}</td>
                                <td>{{ $application->entry_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-check text-info"></i> OA Verification</td>
                                <td>{{ $application->verifiedByOfficeAssistant?->name }}</td>
                                <td>{{ $application->verified_at_office_assistant?->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-check-circle text-success"></i> Secretary Approval</td>
                                <td>{{ $application->approvedBySecretary?->name }}</td>
                                <td>{{ $application->approved_at_secretary?->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-4">
                    <a href="{{ route('ex-contractor.chairman.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print Application
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection