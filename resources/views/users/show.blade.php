@extends('layouts.app')

@section('title', 'User Details - ' . $user->full_name)

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- HEADER CARD --}}
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="mb-2">{{ $user->full_name ?? 'Unknown Name' }}</h5>
                            <a class="text-800 fs-10" href="mailto:{{ $user->email }}">
                                <span class="fas fa-envelope me-1"></span>{{ $user->email }}
                            </a>
                            <span class="mx-2 text-300">|</span>
                            <a class="text-800 fs-10" href="tel:{{ $user->mobile_no }}">
                                <span class="fas fa-phone me-1"></span>{{ $user->mobile_no }}
                            </a>

                            <div class="mt-2">
                            <span class="badge badge-subtle-{{ $user->otp_status === 'verified' ? 'success' : 'warning' }}">
                                OTP: {{ ucfirst($user->otp_status) }}
                            </span>
                                <span class="badge badge-subtle-primary ms-2">{{ ucwords(str_replace('_', ' ', $user->admin_type)) }}</span>
                            </div>
                        </div>
                        <div class="col-auto d-none d-sm-block">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-3xl me-2">
                                    @if($user->applicant_image)
                                        <img class="rounded-circle border border-2 border-white shadow-sm" src="{{ asset('storage/'.$user->applicant_image) }}" alt="" />
                                    @else
                                        <div class="avatar-name rounded-circle border border-2 border-white shadow-sm"><span>{{ substr($user->full_name ?? 'U', 0, 2) }}</span></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top text-end">
                    <a class="btn btn-falcon-default btn-sm" href="{{ route('users.edit', $user->id) }}">
                        <span class="fas fa-pencil-alt fs-11 me-1"></span> Edit User
                    </a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline-block ms-2" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-falcon-default btn-sm text-danger" type="submit">
                            <span class="fas fa-trash fs-11 me-1"></span> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                {{-- LEFT COLUMN: PERSONAL INFO --}}
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body bg-body-tertiary border-top">
                            <div class="row">
                                {{-- Identity Section --}}
                                <div class="col-lg-6">
                                    <h6 class="fw-bold ls mb-3 text-uppercase text-600 fs-11">Identity</h6>

                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">User ID</p></div>
                                        <div class="col">{{ $user->user_id ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Full Name (En)</p></div>
                                        <div class="col">{{ $user->full_name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Full Name (Bn)</p></div>
                                        <div class="col font-sans-serif">{{ $user->full_name_bn ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">NID No</p></div>
                                        <div class="col">{{ $user->nid_no ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Date of Birth</p></div>
                                        <div class="col">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d M, Y') : '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Gender</p></div>
                                        <div class="col">{{ $user->gender ?? '-' }}</div>
                                    </div>
                                </div>

                                {{-- Family Info Section --}}
                                <div class="col-lg-6 mt-4 mt-lg-0">
                                    <h6 class="fw-bold ls mb-3 text-uppercase text-600 fs-11">Family Info</h6>

                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Father's Name</p></div>
                                        <div class="col">{{ $user->father_name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Father (Bn)</p></div>
                                        <div class="col font-sans-serif">{{ $user->father_name_bn ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Mother's Name</p></div>
                                        <div class="col">{{ $user->mother_name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Mother (Bn)</p></div>
                                        <div class="col font-sans-serif">{{ $user->mother_name_bn ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Spouse Name</p></div>
                                        <div class="col">{{ $user->spouse_name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 col-sm-4"><p class="fw-semi-bold mb-1">Spouse (Bn)</p></div>
                                        <div class="col font-sans-serif">{{ $user->spouse_name_bn ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: ADDRESSES --}}
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Address Details</h5>
                        </div>
                        <div class="card-body bg-body-tertiary border-top">

                            <div class="mb-4">
                                <h6 class="fw-bold text-uppercase text-600 mb-2 fs-11">Present Address</h6>
                                <p class="mb-1 fs-10">{{ $user->pre_village ?? 'Village/Road: -' }}</p>
                                <p class="mb-1 fs-10">
                                    {{ $user->pre_post_office ? 'PO: '.$user->pre_post_office : '' }}
                                    {{ $user->pre_post_code ? '- '.$user->pre_post_code : '' }}
                                </p>
                                <p class="mb-0 fw-semi-bold">
                                    {{ $user->pre_upozila ?? '' }} {{ $user->pre_district ? ', '.$user->pre_district : '' }}
                                    {{ $user->pre_division ? ', '.$user->pre_division : '' }}
                                </p>
                            </div>

                            <hr class="my-3 text-200">

                            <div class="mb-4">
                                <h6 class="fw-bold text-uppercase text-600 mb-2 fs-11">Permanent Address</h6>
                                <p class="mb-1 fs-10">{{ $user->per_village ?? 'Village/Road: -' }}</p>
                                <p class="mb-1 fs-10">
                                    {{ $user->per_post_office ? 'PO: '.$user->per_post_office : '' }}
                                    {{ $user->per_post_code ? '- '.$user->per_post_code : '' }}
                                </p>
                                <p class="mb-0 fw-semi-bold">
                                    {{ $user->per_upozila ?? '' }} {{ $user->per_district ? ', '.$user->per_district : '' }}
                                    {{ $user->per_division ? ', '.$user->per_division : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTTOM CARD: DOCUMENTS & JOB INFO --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Official Information & Documents</h5>
                </div>
                <div class="card-body bg-body-tertiary border-top">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <p class="fw-semi-bold mb-1 fs-10 text-600">Organization</p>
                            <p class="mb-0 fw-bold">{{ $user->business_organization_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <p class="fw-semi-bold mb-1 fs-10 text-600">Designation (EN)</p>
                            <p class="mb-0 fw-bold">{{ $user->admin_designation ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="fw-semi-bold mb-1 fs-10 text-600">Designation (BN)</p>
                            <p class="mb-0 fw-bold font-sans-serif">{{ $user->admin_designation_bn ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold ls mb-3 text-uppercase text-600 fs-11">Uploaded Documents</h6>
                            <div class="d-flex flex-wrap gap-4">
                                {{-- NID IMAGE --}}
                                <div class="d-flex flex-column">
                                    <span class="fs-10 fw-semi-bold mb-2">NID Copy</span>
                                    @if($user->nid_image)
                                        <a href="{{ asset('storage/'.$user->nid_image) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$user->nid_image) }}" alt="NID" class="img-thumbnail" style="height: 120px; width: auto; object-fit: contain;">
                                        </a>
                                    @else
                                        <div class="bg-200 rounded d-flex align-items-center justify-content-center text-500" style="height: 120px; width: 180px;">
                                            <small>No Image</small>
                                        </div>
                                    @endif
                                </div>

                                {{-- APPLICANT IMAGE (Large View) --}}
                                <div class="d-flex flex-column">
                                    <span class="fs-10 fw-semi-bold mb-2">Applicant Photo</span>
                                    @if($user->applicant_image)
                                        <a href="{{ asset('storage/'.$user->applicant_image) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$user->applicant_image) }}" alt="Applicant" class="img-thumbnail" style="height: 120px; width: auto; object-fit: contain;">
                                        </a>
                                    @else
                                        <div class="bg-200 rounded d-flex align-items-center justify-content-center text-500" style="height: 120px; width: 120px;">
                                            <small>No Image</small>
                                        </div>
                                    @endif
                                </div>

                                {{-- SIGNATURE --}}
                                <div class="d-flex flex-column">
                                    <span class="fs-10 fw-semi-bold mb-2">Signature</span>
                                    @if($user->applicant_sign)
                                        <a href="{{ asset('storage/'.$user->applicant_sign) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$user->applicant_sign) }}" alt="Signature" class="img-thumbnail" style="height: 80px; width: auto; object-fit: contain; margin-top: 20px;">
                                        </a>
                                    @else
                                        <div class="bg-200 rounded d-flex align-items-center justify-content-center text-500" style="height: 120px; width: 180px;">
                                            <small>No Signature</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
