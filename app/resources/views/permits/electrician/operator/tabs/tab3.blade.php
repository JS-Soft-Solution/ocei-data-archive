{{-- Tab 3: Education Information --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="degree" class="form-label">Degree/Qualification</label>
        <input type="text" class="form-control @error('degree') is-invalid @enderror" id="degree" name="degree"
            value="{{ old('degree', $application->degree) }}"
            placeholder="e.g., SSC, Diploma in Electrical Engineering">
        @error('degree')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="subject" class="form-label">Subject/Trade</label>
        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject"
            value="{{ old('subject', $application->subject) }}" placeholder="e.g., Electrical Technology">
        @error('subject')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="board" class="form-label">Board/Institution</label>
        <input type="text" class="form-control @error('board') is-invalid @enderror" id="board" name="board"
            value="{{ old('board', $application->board) }}" placeholder="e.g., Dhaka Board, BTEB">
        @error('board')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="academic_result" class="form-label">Result/Grade</label>
        <input type="text" class="form-control @error('academic_result') is-invalid @enderror" id="academic_result"
            name="academic_result" value="{{ old('academic_result', $application->academic_result) }}"
            placeholder="e.g., GPA 3.5, First Division">
        @error('academic_result')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="passing_year" class="form-label">Passing Year</label>
        <input type="text" class="form-control @error('passing_year') is-invalid @enderror" id="passing_year"
            name="passing_year" value="{{ old('passing_year', $application->passing_year) }}" placeholder="e.g., 2015">
        @error('passing_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="fas fa-info-circle"></i> <strong>Note:</strong> Please provide educational qualifications relevant to
    electrical work.
</div>