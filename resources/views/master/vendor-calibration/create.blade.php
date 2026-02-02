@extends('layouts.app')

@section('title', $title)

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="text-muted mb-1">
                Master / Vendor Calibration / <strong class="text-dark">New</strong>
            </h6>
            <h5 class="mb-0 fw-semibold">{{ $title }}</h5>
        </div>
        <a href="{{ route('vendors-calibration') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('vendors-calibration.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            <!-- Code -->
                            <div class="col-md-2 text-end align-self-center">
                                <label class="form-label mb-0">Code</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" name="code"
                                    class="form-control form-control-sm bg-light @error('code') is-invalid @enderror"
                                    value="{{ $nextCode }}" readonly>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="col-md-2 text-end align-self-center">
                                <label class="form-label mb-0">Name</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" name="name"
                                    class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-md-2 text-end pt-1">
                                <label class="form-label mb-0">Address</label>
                            </div>
                            <div class="col-md-10">
                                <textarea name="address"
                                    class="form-control form-control-sm @error('address') is-invalid @enderror"
                                    rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Service -->
                            <div class="col-md-2 text-end align-self-center">
                                <label class="form-label mb-0">Service</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" name="service"
                                    class="form-control form-control-sm @error('service') is-invalid @enderror"
                                    value="{{ old('service') }}">
                                @error('service')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-2 text-end align-self-center">
                                <label class="form-label mb-0">Phone</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" name="phone"
                                    class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Post Code -->
                            <div class="col-md-2 text-end align-self-center">
                                <label class="form-label mb-0">Post Code</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" name="post_code"
                                    class="form-control form-control-sm @error('post_code') is-invalid @enderror"
                                    value="{{ old('post_code') }}">
                                @error('post_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-10 offset-md-2 mt-4">
                                <button type="submit" class="btn btn-sm btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save Vendor
                                </button>
                                <a href="{{ route('vendors-calibration') }}"
                                    class="btn btn-sm btn-light border px-4 ms-2">Cancel</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection