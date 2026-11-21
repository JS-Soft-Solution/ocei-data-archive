{{-- Tab 1: Personal Information --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="old_certificate_number" class="form-label">Old Certificate Number <span
                class="text-danger">*</span></label>
        <input type="text" class="form-control @error('old_certificate_number') is-invalid @enderror"
            id="old_certificate_number" name="old_certificate_number"
            value="{{ old('old_certificate_number', $application->old_certificate_number) }}" required>
        @error('old_certificate_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="nid_number" class="form-label">NID Number</label>
        <input type="text" class="form-control @error('nid_number') is-invalid @enderror" id="nid_number"
            name="nid_number" value="{{ old('nid_number', $application->nid_number) }}">
        @error('nid_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="applicant_name_bn" class="form-label">Applicant Name (Bangla)</label>
        <input type="text" class="form-control @error('applicant_name_bn') is-invalid @enderror" id="applicant_name_bn"
            name="applicant_name_bn" value="{{ old('applicant_name_bn', $application->applicant_name_bn) }}">
        @error('applicant_name_bn')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="applicant_name_en" class="form-label">Applicant Name (English)</label>
        <input type="text" class="form-control @error('applicant_name_en') is-invalid @enderror" id="applicant_name_en"
            name="applicant_name_en" value="{{ old('applicant_name_en', $application->applicant_name_en) }}">
        @error('applicant_name_en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="father_name" class="form-label">Father's Name</label>
        <input type="text" class="form-control @error('father_name') is-invalid @enderror" id="father_name"
            name="father_name" value="{{ old('father_name', $application->father_name) }}">
        @error('father_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="mother_name" class="form-label">Mother's Name</label>
        <input type="text" class="form-control @error('mother_name') is-invalid @enderror" id="mother_name"
            name="mother_name" value="{{ old('mother_name', $application->mother_name) }}">
        @error('mother_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth"
            name="date_of_birth" value="{{ old('date_of_birth', $application->date_of_birth?->format('Y-m-d')) }}">
        @error('date_of_birth')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="mobile_no" class="form-label">Mobile Number</label>
        <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" id="mobile_no" name="mobile_no"
            value="{{ old('mobile_no', $application->mobile_no) }}">
        @error('mobile_no')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email', $application->email) }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>