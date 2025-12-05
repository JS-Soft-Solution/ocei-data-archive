@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4><i class="fas fa-user-shield"></i> Admin Override Edit - {{ $application->old_certificate_number }}</h4>
            </div>

            <div class="card-body">
                {{-- Warning --}}
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle"></i> ADMIN OVERRIDE MODE</strong><br>
                    You are editing a {{ $application->isLocked() ? 'LOCKED' : '' }} record. All changes will be logged in
                    audit trail with your admin override reason.
                </div>

                @if($application->isLocked())
                    <div class="alert alert-warning">
                        <i class="fas fa-lock"></i> <strong>This record is LOCKED</strong> (Secretary Approved Final). Only you
                        can edit it.
                    </div>
                @endif

                <form method="POST" action="{{ route('ex-contractor.admin.update', $application) }}">
                    @csrf
                    @method('PUT')

                    {{-- Override Reason (Required) --}}
                    <div class="alert alert-info mb-4">
                        <div class="mb-3">
                            <label class="form-label"><strong>Override Reason * (Required)</strong></label>
                            <textarea name="override_reason" class="form-control" rows="2" required
                                placeholder="Explain why you're making this override edit..."></textarea>
                            <small class="text-muted">This will be logged in the audit trail.</small>
                        </div>
                    </div>

                    {{-- Personal Information --}}
                    <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Old Certificate Number</label>
                            <input type="text" class="form-control" value="{{ $application->old_certificate_number }}"
                                disabled>
                            <small class="text-muted">Cannot be changed</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NID Number</label>
                            <input type="text" name="nid_number" class="form-control"
                                value="{{ old('nid_number', $application->nid_number) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applicant Name (English)</label>
                            <input type="text" name="applicant_name_en" class="form-control"
                                value="{{ old('applicant_name_en', $application->applicant_name_en) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applicant Name (Bangla)</label>
                            <input type="text" name="applicant_name_bn" class="form-control"
                                value="{{ old('applicant_name_bn', $application->applicant_name_bn) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Name</label>
                            <input type="text" name="father_name" class="form-control"
                                value="{{ old('father_name', $application->father_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control"
                                value="{{ old('mother_name', $application->mother_name) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                value="{{ old('date_of_birth', $application->date_of_birth?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_no" class="form-control"
                                value="{{ old('mobile_no', $application->mobile_no) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $application->email) }}">
                        </div>
                    </div>

                    {{-- Address --}}
                    <h5 class="border-bottom pb-2 mb-3 mt-4">Address</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Village/Area</label>
                            <textarea name="village" class="form-control"
                                rows="2">{{ old('village', $application->village) }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Post Office</label>
                            <input type="text" name="post_office" class="form-control"
                                value="{{ old('post_office', $application->post_office) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Post Code</label>
                            <input type="number" name="postcode" class="form-control"
                                value="{{ old('postcode', $application->postcode) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Upazilla</label>
                            <input type="text" name="upazilla" class="form-control"
                                value="{{ old('upazilla', $application->upazilla) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">District</label>
                            <input type="text" name="district" class="form-control"
                                value="{{ old('district', $application->district) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Division</label>
                            <input type="text" name="division" class="form-control"
                                value="{{ old('division', $application->division) }}">
                        </div>
                    </div>

                    {{-- Certificate Details --}}
                    <h5 class="border-bottom pb-2 mb-3 mt-4">Certificate Details</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">License Class</label>
                            <select name="class" class="form-select">
                                <option value="">Select Class</option>
                                <option value="C" {{ old('class', $application->class) == 'C' ? 'selected' : '' }}>C</option>
                                <option value="BC" {{ old('class', $application->class) == 'BC' ? 'selected' : '' }}>BC</option>
                                <option value="ABC" {{ old('class', $application->class) == 'ABC' ? 'selected' : '' }}>ABC</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Book/Registrar Number</label>
                            <input type="text" name="book_number" class="form-control"
                                value="{{ old('book_number', $application->book_number) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Certificate Number</label>
                            <input type="text" name="certificate_number" class="form-control"
                                value="{{ old('certificate_number', $application->certificate_number) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Issue Date</label>
                            <input type="date" name="issue_date" class="form-control"
                                value="{{ old('issue_date', $application->issue_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control"
                                value="{{ old('expiry_date', $application->expiry_date?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    {{-- Education --}}
                    <h5 class="border-bottom pb-2 mb-3 mt-4">Education</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Degree</label>
                            <input type="text" name="degree" class="form-control"
                                value="{{ old('degree', $application->degree) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control"
                                value="{{ old('subject', $application->subject) }}">
                        </div>
                    </div>

                    {{-- Work Experience --}}
                    <h5 class="border-bottom pb-2 mb-3 mt-4">Work Experience</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control"
                                value="{{ old('company', $application->company) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control"
                                value="{{ old('designation', $application->designation) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Job Duration</label>
                            <input type="text" name="total_job_duration" class="form-control"
                                value="{{ old('total_job_duration', $application->total_job_duration) }}">
                        </div>
                    </div>

                    {{-- Current Status Info --}}
                    <h5 class="border-bottom pb-2 mb-3 mt-4">Current Status</h5>
                    <div class="alert alert-secondary">
                        <strong>Status:</strong> <span
                            class="badge bg-{{ $application->status_badge_color }}">{{ $application->status_label }}</span><br>
                        <strong>Entry By:</strong> {{ $application->entryBy?->name }}<br>
                        <strong>Created:</strong> {{ $application->created_at->format('Y-m-d H:i:s') }}<br>
                        @if($application->isLocked())
                            <strong>Approved By:</strong> {{ $application->approvedBySecretary?->name }}<br>
                            <strong>Approved At:</strong> {{ $application->approved_at_secretary?->format('Y-m-d H:i:s') }}
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('ex-contractor.admin.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save"></i> Save Override Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection