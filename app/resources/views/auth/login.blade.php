@extends('layouts.partials.guest')

@section('title', 'Account Login')

@section('content')
    <div class="row min-vh-100 flex-center g-0">
        <div class="col-lg-8 col-xxl-5 py-3 position-relative">
            <img class="bg-auth-circle-shape" src="{{ asset('assets/img/icons/spot-illustrations/bg-shape.png') }}" alt="" width="250">
            <img class="bg-auth-circle-shape-2" src="{{ asset('assets/img/icons/spot-illustrations/shape-1.png') }}" alt="" width="150">

            <div class="card overflow-hidden z-1">
                <div class="card-body p-0">
                    <div class="row g-0 h-100">
                        {{-- Left Side Info Panel --}}
                        <div class="col-md-5 text-center bg-card-gradient">
                            <div class="position-relative p-4 pt-md-5 pb-md-7" data-bs-theme="light">
                                <div class="bg-holder bg-auth-card-shape" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/half-circle.png') }});"></div>
                                <div class="z-1 position-relative">
                                    <a class="link-light mb-4 font-sans-serif fs-5 d-inline-block fw-bolder bg-white rounded p-2" href="{{ url('/') }}">
                                        <img src="{{ asset('assets/img/ocei-logo.png') }}" alt="OCEI Logo" class="img-fluid" style="max-width: 200px;">
                                    </a>
                                    <p class="opacity-75 text-white">OCEI Data Archive Solution. <br>Restricted Access Only.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Right Side Login Form --}}
                        <div class="col-md-7 d-flex flex-center">
                            <div class="p-4 p-md-5 flex-grow-1">
                                <div class="row flex-between-center">
                                    <div class="col-auto">
                                        <h3>Account Login</h3>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('login.perform') }}">
                                    @csrf

                                    @if($errors->any())
                                        <div class="alert alert-danger border-0 fs-10 p-2 mb-3">
                                            <ul class="mb-0 ps-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(session('success'))
                                        <div class="alert alert-success border-0 fs-10 p-2 mb-3">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label" for="login_id">Email or User ID</label>
                                        <input class="form-control" id="login_id" name="login_id" type="text" value="{{ old('login_id') }}" required autofocus />
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="password">Password</label>
                                        </div>
                                        <input class="form-control" id="password" name="password" type="password" required />
                                    </div>

                                    <div class="row flex-between-center">
                                        <div class="col-auto">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" id="card-checkbox" name="remember" />
                                                <label class="form-check-label mb-0" for="card-checkbox">Remember me</label>
                                            </div>
                                        </div>
                                        {{-- FORGOT PASSWORD LINK ADDED HERE --}}
                                        <div class="col-auto">
                                            <a class="fs-10" href="{{ route('password.request') }}">Forgot Password?</a>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <button class="btn btn-primary d-block w-100 mt-3" type="submit">Log in</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
