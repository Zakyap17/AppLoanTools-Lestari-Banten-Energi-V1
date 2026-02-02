@extends('layouts.app')

@section('title', 'New MAT Sub Group')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('tools-mat-group.index') }}" class="text-decoration-none text-muted">Material
                Groups</a> / <strong class="text-dark">New Sub Group</strong>
        </h6>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">New MAT Sub Group :</h5>
            <button type="button" class="btn-close"
                onclick="window.location.href='{{ route('tools-mat-group.index') }}'"></button>
        </div>
        <hr class="my-0">
        <div class="card-body px-4 py-4">
            <form action="{{ route('tools-mat-sub-group.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <!-- MAT Group -->
                    <div class="col-md-2 text-end align-self-center">
                        <label class="form-label mb-0">MAT Group</label>
                    </div>
                    <div class="col-md-10">
                        <select name="mat_group_code"
                            class="form-select form-select-sm @error('mat_group_code') is-invalid @enderror" required>
                            <option value="" selected disabled>- Pilih MAT Group -</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->code }}" {{ old('mat_group_code', request('group_id') ? \App\Models\ToolsMatGroup::find(request('group_id'))->code : '') == $group->code ? 'selected' : '' }}>
                                    {{ $group->code }} - {{ $group->description }}
                                </option>
                            @endforeach
                        </select>
                        @error('mat_group_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sub Code -->
                    <div class="col-md-2 text-end align-self-center">
                        <label class="form-label mb-0">Sub Code</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="sub_code"
                            class="form-control form-control-sm @error('sub_code') is-invalid @enderror"
                            value="{{ old('sub_code') }}" required>
                        @error('sub_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sub Description -->
                    <div class="col-md-2 text-end align-self-center">
                        <label class="form-label mb-0">Sub Description</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="sub_description"
                            class="form-control form-control-sm @error('sub_description') is-invalid @enderror"
                            value="{{ old('sub_description') }}" required>
                        @error('sub_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Save Sub Group</button>
                    <a href="{{ route('tools-mat-group.index') }}"
                        class="btn btn-sm btn-secondary px-4 fw-bold ms-2">Cancel</a>
                </div>

            </form>
        </div>
    </div>

@endsection