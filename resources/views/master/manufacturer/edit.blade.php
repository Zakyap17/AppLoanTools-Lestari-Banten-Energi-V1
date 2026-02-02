@extends('layouts.app')

@section('title', 'Edit Manufacturer')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('manufacture.index') }}" class="text-decoration-none text-muted">Manufacturer
                Tools</a> / <strong class="text-dark">Edit</strong>
        </h6>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('manufacture.update', $manufacturer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Code --}}
                    <div class="col-md-6">
                        <label class="form-label small">Code *</label>
                        <input type="text" name="code" class="form-control form-control-sm" required
                            value="{{ old('code', $manufacturer->code) }}">
                    </div>

                    {{-- Name --}}
                    <div class="col-md-6">
                        <label class="form-label small">Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" required
                            value="{{ old('name', $manufacturer->name) }}">
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label class="form-label small">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm"
                            value="{{ old('phone', $manufacturer->phone) }}">
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label small">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                            value="{{ old('email', $manufacturer->email) }}">
                    </div>

                    {{-- Address --}}
                    <div class="col-12">
                        <label class="form-label small">Address</label>
                        <textarea name="address" class="form-control form-control-sm"
                            rows="3">{{ old('address', $manufacturer->address) }}</textarea>
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">Update Manufacturer</button>
                    <a href="{{ route('manufacture.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>

@endsection