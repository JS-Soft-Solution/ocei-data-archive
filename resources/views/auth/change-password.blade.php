@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card border-top border-0 border-4 border-primary mb-3">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <div class="avatar avatar-4xl mb-2">
                            <div class="avatar-name rounded-circle bg-primary-subtle text-primary">
                                <span class="fas fa-lock fs-2"></span>
                            </div>
                        </div>
                        <h4 class="mb-1">Change Password</h4>
                        <p class="text-500 fs-10">Ensure your account is using a long, random password to stay secure.</p>
                    </div>

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success border-0 d-flex align-items-center" role="alert">
                            <span class="fas fa-check-circle text-success fs-6 me-2"></span>
                            <div class="flex-1">{{ session('success') }}</div>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Current Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="current_password">Current Password</label>
                            <div class="input-group">
                                <input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" placeholder="Enter current password" required />
                                <span class="input-group-text bg-white cursor-pointer" onclick="togglePassword('current_password')">
                                <span class="fas fa-eye text-600"></span>
                            </span>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="password">New Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="Enter new password" required />
                                <span class="input-group-text bg-white cursor-pointer" onclick="togglePassword('password')">
                                <span class="fas fa-eye text-600"></span>
                            </span>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text fs-11 text-warning">
                                Password must be at least 8 characters long.
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <div class="input-group">
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm new password" required />
                                <span class="input-group-text bg-white cursor-pointer" onclick="togglePassword('password_confirmation')">
                                <span class="fas fa-eye text-600"></span>
                            </span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-primary" type="submit">Update Password</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-link btn-sm text-600">Cancel and return to dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Simple vanilla JS to toggle password visibility
            function togglePassword(fieldId) {
                const passwordField = document.getElementById(fieldId);
                const icon = passwordField.nextElementSibling.querySelector('span');

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordField.type = "password";
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        </script>
    @endpush
@endsection
