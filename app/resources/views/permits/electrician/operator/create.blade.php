@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Ex-Electrician Application</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('ex-electrician.operator.store') }}">
                            @csrf

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Please enter the old certificate number to create a new application. You'll be redirected to
                                the full form to complete all details.
                            </div>

                            <div class="mb-3">
                                <label for="old_certificate_number" class="form-label">Old Certificate Number <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('old_certificate_number') is-invalid @enderror"
                                    id="old_certificate_number" name="old_certificate_number"
                                    value="{{ old('old_certificate_number') }}" placeholder="Enter old certificate number"
                                    required autofocus>
                                @error('old_certificate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    This will be used to identify the application. Make sure it's unique.
                                </small>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="class" class="form-label">License Class</label>
                                    <select class="form-select @error('class') is-invalid @enderror" id="class" name="class">
                                        <option value="">Select Class</option>
                                        <option value="C" {{ old('class') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="BC" {{ old('class') == 'BC' ? 'selected' : '' }}>BC</option>
                                        <option value="ABC" {{ old('class') == 'ABC' ? 'selected' : '' }}>ABC</option>
                                    </select>
                                    @error('class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="book_number" class="form-label">Book/Registrar Number</label>
                                    <input type="text" class="form-control @error('book_number') is-invalid @enderror"
                                        id="book_number" name="book_number" value="{{ old('book_number') }}"
                                        placeholder="Enter book or registrar number">
                                    @error('book_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="applicant_name_en" class="form-label">Applicant Name (English)</label>
                                <input type="text" class="form-control @error('applicant_name_en') is-invalid @enderror"
                                    id="applicant_name_en" name="applicant_name_en" value="{{ old('applicant_name_en') }}"
                                    placeholder="Optional: Enter applicant name">
                                @error('applicant_name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="mobile_no" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control @error('mobile_no') is-invalid @enderror"
                                    id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}"
                                    placeholder="Optional: Enter mobile number">
                                @error('mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('ex-electrician.operator.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Draft & Continue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection