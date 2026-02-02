@extends('layouts.app')

@section('title', 'Create New Employee')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('employee.index') }}" class="text-decoration-none text-muted">Employee</a> / <strong
                class="text-dark">Create</strong>
        </h6>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('employee.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- NIK --}}
                    <div class="col-md-6">
                        <label class="form-label small">Employee ID (NIK) *</label>
                        <input type="text" name="nik" class="form-control form-control-sm" required
                            value="{{ old('nik') }}">
                    </div>

                    {{-- Name --}}
                    <div class="col-md-6">
                        <label class="form-label small">Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" required
                            value="{{ old('name') }}">
                    </div>

                    {{-- Department --}}
                    <div class="col-md-6">
                        <label class="form-label small">Department</label>
                        <input type="text" name="department" class="form-control form-control-sm"
                            value="{{ old('department') }}">
                    </div>

                    {{-- Position --}}
                    <div class="col-md-6">
                        <label class="form-label small">Position</label>
                        <input type="text" name="position" class="form-control form-control-sm"
                            value="{{ old('position') }}">
                    </div>



                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label small">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}">
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">Save Employee</button>
                    <a href="{{ route('employee.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>

@endsection