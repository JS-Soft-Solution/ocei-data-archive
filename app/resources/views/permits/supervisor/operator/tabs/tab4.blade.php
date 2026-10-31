{{-- Tab 4: Work Experience --}}
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="company" class="form-label">Company/Organization Name</label>
        <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company"
            value="{{ old('company', $application->company) }}" placeholder="Current or most recent employer">
        @error('company')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="designation" class="form-label">Designation/Position</label>
        <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation"
            name="designation" value="{{ old('designation', $application->designation) }}"
            placeholder="e.g., Junior Supervisor, Technician">
        @error('designation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="total_job_duration" class="form-label">Total Job Duration</label>
        <input type="text" class="form-control @error('total_job_duration') is-invalid @enderror"
            id="total_job_duration" name="total_job_duration"
            value="{{ old('total_job_duration', $application->total_job_duration) }}"
            placeholder="e.g., 5 years, 18 months">
        @error('total_job_duration')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="alert alert-warning mt-3">
    <i class="fas fa-briefcase"></i> <strong>Work Experience:</strong> Provide details of your electrical work
    experience. This helps verify your practical knowledge in the field.
</div>