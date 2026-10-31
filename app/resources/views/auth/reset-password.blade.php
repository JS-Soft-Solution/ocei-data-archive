@extends('layouts.partials.guest')


@section('title', 'Reset Password')

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
                                    <p class="opacity-75 text-white">Almost done! Enter your new password.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 d-flex flex-center">
                            <div class="p-4 p-md-5 flex-grow-1">
                                <h4 class="mb-3 text-center">Set New Password</h4>

                                @if (session('success'))
                                    <div class="alert alert-success border-0 fs-10 p-2 mb-3">{{ session('success') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger border-0 fs-10 p-2 mb-3">{{ $errors->first() }}</div>
                                @endif

                                <form class="mt-3" action="{{ route('password.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input class="form-control" type="password" name="password" placeholder="New Password" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm Password" required />
                                    </div>
                                    <button class="btn btn-primary d-block w-100 mt-3" type="submit">Reset Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
