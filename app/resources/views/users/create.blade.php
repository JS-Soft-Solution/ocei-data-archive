@extends('layouts.app')

@section('title', 'Create New User')

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

                {{-- ONE SINGLE FORM FOR ALL STEPS --}}
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body py-4">
                        <div class="tab-content">

                            {{-- TAB 1: ACCOUNT --}}
                            <div class="tab-pane active px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab1" id="bootstrap-wizard-tab1">
                                <div class="mb-3">
                                    <label class="form-label">Role / Designation</label>
                                    <select class="form-select @error('admin_type') is-invalid @enderror" name="admin_type" required>
                                        <option value="">Select Role...</option>
                                        <option value="super_admin">Super Admin</option>
                                        <option value="secretary">Secretary</option>
                                        <option value="chairman">Chairman</option>
                                        <option value="office_assistant">Office Assistant</option>
                                        <option value="data_entry_operator">Data Entry Operator</option>
                                    </select>
                                    @error('admin_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input class="form-control" type="text" name="full_name" placeholder="John Smith" value="{{ old('full_name') }}" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" placeholder="Email address" value="{{ old('email') }}" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile_no" placeholder="017XXXXXXXX" value="{{ old('mobile_no') }}" required />
                                </div>

                                <div class="row gx-2">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input class="form-control" type="password" name="password" placeholder="Password" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm Password" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: PERSONAL --}}
                            <div class="tab-pane px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab2" id="bootstrap-wizard-tab2">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Father's Name</label>
                                        <input class="form-control" type="text" name="father_name" value="{{ old('father_name') }}" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mother's Name</label>
                                        <input class="form-control" type="text" name="mother_name" value="{{ old('mother_name') }}" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">NID Number</label>
                                    <input class="form-control" type="text" name="nid_no" value="{{ old('nid_no') }}" />
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input class="form-control datetimepicker" type="text" name="dob" placeholder="Y-m-d" data-options='{"dateFormat":"Y-m-d","disableMobile":true}' />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 3: ADDRESS --}}
                            <div class="tab-pane px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab3" id="bootstrap-wizard-tab3">
                                <h5 class="mb-3">Present Address</h5>
                                <div class="row g-2">
                                    <div class="col-md-6 mb-3"><label class="form-label">District</label><input class="form-control" type="text" name="pre_district" /></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Upazila</label><input class="form-control" type="text" name="pre_upozila" /></div>
                                    <div class="col-12 mb-3"><label class="form-label">Village/Road</label><input class="form-control" type="text" name="pre_village" /></div>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Permanent Address</h5>
                                <div class="row g-2">
                                    <div class="col-md-6 mb-3"><label class="form-label">District</label><input class="form-control" type="text" name="per_district" /></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Upazila</label><input class="form-control" type="text" name="per_upozila" /></div>
                                    <div class="col-12 mb-3"><label class="form-label">Village/Road</label><input class="form-control" type="text" name="per_village" /></div>
                                </div>
                            </div>

                            {{-- TAB 4: IMAGES & FINISH --}}
                            <div class="tab-pane text-center px-sm-3 px-md-5" role="tabpanel" aria-labelledby="bootstrap-wizard-tab4" id="bootstrap-wizard-tab4">
                                <div class="mb-3">
                                    <label class="form-label">Upload Applicant Image</label>
                                    <input class="form-control" type="file" name="applicant_image" accept="image/*" />
                                </div>

                                <div class="wizard-lottie-wrapper">
                                    <div class="lottie wizard-lottie mx-auto my-3" data-options='{"path":"{{ asset('assets/img/animated-icons/celebration.json') }}"}'></div>
                                </div>
                                <h4 class="mb-1">You are all set!</h4>
                                <p>Please review the data and click Submit.</p>

                                <button class="btn btn-success px-5 my-3" type="submit">Create User</button>
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
            </div>
        </div>
    </div>
@endsection
