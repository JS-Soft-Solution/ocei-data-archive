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

                {{-- Organization Information --}}
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-building me-1"></i> Organization Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Organization Name (English):</strong> <strong>{{ $application->company_name_en ?? $application->applicant_name_en ?? 'N/A' }}</strong></p>
                        <p><strong>Organization Name (Bangla):</strong> {{ $application->company_name_bn ?? $application->applicant_name_bn ?? 'N/A' }}</p>
                        <p><strong>Owner / Shareholder:</strong> {{ $application->owner_shareholder_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Approved Class:</strong> <span class="badge bg-secondary">{{ $application->class ?? 'N/A' }}</span></p>
                        <p><strong>Book Number:</strong> {{ $application->book_number ?? 'N/A' }}</p>
                        <p><strong>Trade License:</strong> {{ $application->trade_license_number ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Contact Information --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-phone me-1"></i> Contact Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Mobile:</strong> {{ $application->mobile_no ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $application->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>District:</strong> {{ $application->district ?? $application->boa_district ?? 'N/A' }}</p>
                        <p><strong>Division:</strong> {{ $application->division ?? $application->boa_division ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Assigned Supervisor --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-tie me-1"></i> Assigned Supervisor</h5>
                <p><strong>Supervisor Name / Certificate #:</strong> {{ $application->elb_certified_supervisor_no ?? 'N/A' }}</p>

                {{-- Testing Equipment --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-tools me-1"></i> Testing Instruments</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Earth Tester (ET) Serial:</strong> {{ $application->et_serial_no ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Megger (MG) Serial:</strong> {{ $application->mg_serial_no ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Clamp Meter (CM) Serial:</strong> {{ $application->cm_serial_no ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Address --}}
                <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-map-marker-alt me-1"></i> Business Address</h5>
                <p>
                    {{ $application->village ?? $application->boa_village ?? 'N/A' }}<br>
                    @if($application->upazilla || $application->district || $application->division)
                        {{ $application->upazilla ?? $application->boa_upozila ?? '' }}, {{ $application->district ?? $application->boa_district ?? '' }}, {{ $application->division ?? $application->boa_division ?? '' }}
                    @endif
                </p>
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