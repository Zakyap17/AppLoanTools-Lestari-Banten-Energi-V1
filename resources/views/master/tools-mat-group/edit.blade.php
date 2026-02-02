@extends('layouts.app')

@section('title', 'Edit MAT Group')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('tools-mat-group.index') }}" class="text-decoration-none text-muted">Tools MAT
                Group</a> / <strong class="text-dark">Edit</strong>
        </h6>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Edit MAT Group : {{ $group->code }}</h5>
            <button type="button" class="btn-close"
                onclick="window.location.href='{{ route('tools-mat-group.index') }}'"></button>
        </div>
        <hr class="my-0">
        <div class="card-body px-4 py-4">
            <form action="{{ route('tools-mat-group.update', $group->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <!-- Code -->
                    <div class="col-md-2 text-end align-self-center">
                        <label class="form-label mb-0">Code</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="code"
                            class="form-control form-control-sm @error('code') is-invalid @enderror"
                            value="{{ old('code', $group->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-2 text-end align-self-center">
                        <label class="form-label mb-0">Description</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="description"
                            class="form-control form-control-sm @error('description') is-invalid @enderror"
                            value="{{ old('description', $group->description) }}" required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Update Group</button>
                    <a href="{{ route('tools-mat-group.index') }}"
                        class="btn btn-sm btn-secondary px-4 fw-bold ms-2">Cancel</a>
                </div>

            </form>
        </div>
    </div>

@endsection