@extends('layouts.app')

@section('title', 'Edit User - ' . $user->full_name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card theme-wizard mb-5">
                <div class="card-header bg-body-tertiary pt-3 pb-2">
                    <ul class="nav justify-content-between nav-wizard">
                        <li class="nav-item"><a class="nav-link active fw-semi-bold" href="#bootstrap-wizard-tab1" data-bs-toggle="tab" data-wizard-step="1"><span class="nav-item-circle-parent"><span class="nav-item-circle"><span class="fas fa-lock"></span></span></span><span class="d-none d-md-block mt-1 fs-10">Account</span></a></li>
                        <li class="nav-item"><a class="nav-link fw-semi-bold" href="#bootstrap-wizard-tab2" data-bs-toggle="tab" data-wizard-step="2"><span class="nav-item-circle-parent"><span class="nav-item-circle"><span class="fas fa-user"></span></span></span><span class="d-none d-md-block mt-1 fs-10">Personal</span></a></li>
                        <li class="nav-item"><a class="nav-link fw-semi-bold" href="#bootstrap-wizard-tab3" data-bs-toggle="tab" data-wizard-step="3"><span class="nav-item-circle-parent"><span class="nav-item-circle"><span class="fas fa-map-marker-alt"></span></span></span><span class="d-none d-md-block mt-1 fs-10">Address</span></a></li>
                        <li class="nav-item"><a class="nav-link fw-semi-bold" href="#bootstrap-wizard-tab4" data-bs-toggle="tab" data-wizard-step="4"><span class="nav-item-circle-parent"><span class="nav-item-circle"><span class="fas fa-thumbs-up"></span></span></span><span class="d-none d-md-block mt-1 fs-10">Done</span></a></li>
                    </ul>
                </div>

                {{-- FORM START --}}
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Vital for Update Requests --}}

                    <div class="card-body py-4">
                        <div class="tab-content">

                            {{-- TAB 1: ACCOUNT --}}
                            <div class="tab-pane active px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab1" id="bootstrap-wizard-tab1">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">User ID (Auto-generated)</label>
                                        <input class="form-control" type="text" value="{{ $user->user_id }}" disabled readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Role / Designation</label>
                                        <select class="form-select @error('admin_type') is-invalid @enderror" name="admin_type" required>
                                            <option value="">Select Role...</option>
                                            @foreach(['super_admin', 'secretary', 'chairman', 'office_assistant', 'data_entry_operator'] as $role)
                                                <option value="{{ $role }}" {{ old('admin_type', $user->admin_type) == $role ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('admin_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input class="form-control @error('full_name') is-invalid @enderror" type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required />
                                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mobile No</label>
                                    <input class="form-control @error('mobile_no') is-invalid @enderror" type="text" name="mobile_no" value="{{ old('mobile_no', $user->mobile_no) }}" required />
                                    @error('mobile_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="alert alert-info fs-10 p-2 mb-3" role="alert">
                                    <span class="fas fa-info-circle me-2"></span>Leave password fields blank if you do not want to change it.
                                </div>

                                <div class="row gx-2">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Min 8 characters" />
                                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm Password" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: PERSONAL --}}
                            <div class="tab-pane px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab2" id="bootstrap-wizard-tab2">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Father's Name</label>
                                        <input class="form-control" type="text" name="father_name" value="{{ old('father_name', $user->father_name) }}" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mother's Name</label>
                                        <input class="form-control" type="text" name="mother_name" value="{{ old('mother_name', $user->mother_name) }}" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">NID Number</label>
                                    <input class="form-control" type="text" name="nid_no" value="{{ old('nid_no', $user->nid_no) }}" />
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        {{-- Format date for input value Y-m-d --}}
                                        <input class="form-control datetimepicker" type="text" name="dob" placeholder="Y-m-d" value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}" data-options='{"dateFormat":"Y-m-d","disableMobile":true}' />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select...</option>
                                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 3: ADDRESS --}}
                            <div class="tab-pane px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab3" id="bootstrap-wizard-tab3">
                                <h5 class="mb-3">Present Address</h5>
                                <div class="row g-2">
                                    <div class="col-md-6 mb-3"><label class="form-label">District</label><input class="form-control" type="text" name="pre_district" value="{{ old('pre_district', $user->pre_district) }}" /></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Upazila</label><input class="form-control" type="text" name="pre_upozila" value="{{ old('pre_upozila', $user->pre_upozila) }}" /></div>
                                    <div class="col-12 mb-3"><label class="form-label">Village/Road</label><input class="form-control" type="text" name="pre_village" value="{{ old('pre_village', $user->pre_village) }}" /></div>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Permanent Address</h5>
                                <div class="row g-2">
                                    <div class="col-md-6 mb-3"><label class="form-label">District</label><input class="form-control" type="text" name="per_district" value="{{ old('per_district', $user->per_district) }}" /></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Upazila</label><input class="form-control" type="text" name="per_upozila" value="{{ old('per_upozila', $user->per_upozila) }}" /></div>
                                    <div class="col-12 mb-3"><label class="form-label">Village/Road</label><input class="form-control" type="text" name="per_village" value="{{ old('per_village', $user->per_village) }}" /></div>
                                </div>
                            </div>

                            {{-- TAB 4: IMAGES & FINISH --}}
                            <div class="tab-pane text-center px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab4" id="bootstrap-wizard-tab4">

                                {{-- Image Preview Logic --}}
                                <div class="mb-4 d-flex justify-content-center flex-column align-items-center">
                                    <label class="form-label">Current Profile Image</label>
                                    <div class="avatar avatar-4xl mb-2">
                                        @if($user->applicant_image)
                                            <img class="rounded-circle img-thumbnail" src="{{ asset('storage/'.$user->applicant_image) }}" alt="Profile" />
                                        @else
                                            <div class="avatar-name rounded-circle"><span>{{ substr($user->full_name, 0, 2) }}</span></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Change Image (Optional)</label>
                                    <input class="form-control" type="file" name="applicant_image" accept="image/*" />
                                    <div class="form-text">Upload to replace the current image. Max 2MB.</div>
                                </div>

                                <hr class="my-4" />

                                <h4 class="mb-1">Ready to Update?</h4>
                                <p>Please review your changes before saving.</p>

                                <button class="btn btn-primary px-5 my-3" type="submit">Update User Information</button>
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER NAV BUTTONS --}}
                    <div class="card-footer bg-body-tertiary">
                        <div class="px-sm-3 px-md-5">
                            <ul class="pager wizard list-inline mb-0">
                                <li class="previous"><button class="btn btn-link ps-0" type="button"><span class="fas fa-chevron-left me-2" data-fa-transform="shrink-3"></span>Prev</button></li>
                                <li class="next"><button class="btn btn-primary px-5 px-sm-6" type="button">Next<span class="fas fa-chevron-right ms-2" data-fa-transform="shrink-3"> </span></button></li>
                            </ul>
                        </div>
                    </div>
                </form>
                {{-- FORM END --}}
            </div>
        </div>
    </div>
@endsection
