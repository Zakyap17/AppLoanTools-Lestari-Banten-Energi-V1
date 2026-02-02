@extends('layouts.app')

@section('title', 'Edit Tool Type')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('tools-type.index') }}" class="text-decoration-none text-muted">Tools Type</a> /
            <strong class="text-dark">Edit</strong>
        </h6>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tools-type.update', $toolType->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Code --}}
                    <div class="col-md-6">
                        <label class="form-label small">Type Code *</label>
                        <input type="text" name="code" class="form-control form-control-sm" required
                            value="{{ old('code', $toolType->code) }}">
                    </div>

                    {{-- Name as Description --}}
                    <div class="col-md-6">
                        <label class="form-label small">Description *</label>
                        <input type="text" name="name" class="form-control form-control-sm" required
                            value="{{ old('name', $toolType->name) }}">
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">Update Type</button>
                    <a href="{{ route('tools-type.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>

@endsection