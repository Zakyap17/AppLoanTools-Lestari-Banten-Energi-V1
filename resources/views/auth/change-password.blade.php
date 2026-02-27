@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    @include('partials.topbar', ['title' => 'Change Password'])
    <div class="container-fluid py-4">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                                <i class="bi bi-shield-lock text-primary fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Change Password</h5>
                                <p class="text-muted small mb-0">Update your account security</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        {{-- Direct Success Alert --}}
                        @if(session('success'))
                            <div
                                class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-3 p-3">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Update Successful!</h6>
                                    <p class="mb-0 small">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif


                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="current_password"
                                    class="form-label fw-bold small text-muted text-uppercase">Current Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-key text-muted"></i></span>
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" required
                                        placeholder="Enter your current password">
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold small text-muted text-uppercase">New
                                    Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-lock text-muted"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required placeholder="Enter new password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror

                            </div>


                            <div class="mb-4">
                                <label for="password_confirmation"
                                    class="form-label fw-bold small text-muted text-uppercase">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-lock-fill text-muted"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required placeholder="Confirm your new password">
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm rounded-3 fw-bold">
                                    Update Password
                                </button>
                                <a href="{{ route('dashboard') }}"
                                    class="btn btn-light btn-lg border rounded-3 fw-bold text-muted">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            box-shadow: none;
            border-color: #3b82f6;
        }

        .input-group-text {
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
    </style>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'rounded-4'
                        }
                    });
                @endif
                                });
        </script>
    @endpush
@endsection