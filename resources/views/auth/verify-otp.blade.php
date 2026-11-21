@extends('layouts.partials.guest')


@section('title', 'Verify OTP')

@section('content')
    <div class="row min-vh-100 flex-center g-0">
        <div class="col-lg-8 col-xxl-5 py-3 position-relative">
            <div class="card overflow-hidden z-1">
                <div class="card-body p-0">
                    <div class="row g-0 h-100">
                        <div class="col-md-5 text-center bg-card-gradient">
                            <div class="position-relative p-4 pt-md-5 pb-md-7" data-bs-theme="light">
                                <div class="bg-holder bg-auth-card-shape" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/half-circle.png') }});"></div>
                                <div class="z-1 position-relative">
                                    <a class="link-light mb-4 font-sans-serif fs-4 d-inline-block fw-bolder" href="{{ url('/') }}">falcon</a>
                                    <p class="opacity-75 text-white">An OTP has been sent to your mobile number ending in **{{ substr($user->mobile_no, -2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 d-flex flex-center">
                            <div class="p-4 p-md-5 flex-grow-1">
                                <h4 class="mb-3 text-center">Enter OTP</h4>

                                @if (session('success'))
                                    <div class="alert alert-success border-0 fs-10 p-2 mb-3">{{ session('success') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger border-0 fs-10 p-2 mb-3">{{ $errors->first() }}</div>
                                @endif

                                <form class="mt-3" action="{{ route('password.otp.submit', $user->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">4-Digit Code</label>
                                        <input class="form-control text-center text-primary fs-2" type="text" name="otp" maxlength="4" placeholder="----" required />
                                    </div>
                                    <button class="btn btn-primary d-block w-100 mt-3" type="submit">Verify</button>
                                </form>

                                <form action="{{ route('password.email') }}" method="POST" class="mt-3 text-center">
                                    @csrf
                                    <input type="hidden" name="login_id" value="{{ $user->user_id }}">
                                    <button type="submit" class="btn btn-link fs-10">Resend OTP</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
