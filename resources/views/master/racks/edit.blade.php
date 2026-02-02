@extends('layouts.app')

@section('title', 'Edit Rack')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('locations.index') }}" class="text-decoration-none text-muted">Locations (Racks)</a>
            / <strong class="text-dark">Edit Rack</strong>
        </h6>
    </div>

    <form action="{{ route('racks.update', $rack->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Form Rack : {{ $rack->location->name ?? 'Unknown Storage' }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Rack Code --}}
                    <div class="col-12">
                        <label class="form-label small">Rack Code</label>
                        <input type="text" name="code" class="form-control form-control-sm"
                            value="{{ old('code', $rack->code) }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label small">Description</label>
                        <textarea name="description" class="form-control form-control-sm"
                            rows="3">{{ old('description', $rack->description) }}</textarea>
                    </div>

                    {{-- Room --}}
                    <div class="col-12">
                        <label class="form-label small">Room</label>
                        <input type="text" name="room" class="form-control form-control-sm"
                            value="{{ old('room', $rack->room) }}">
                    </div>

                    {{-- Building --}}
                    <div class="col-12">
                        <label class="form-label small">Building</label>
                        <input type="text" name="building" class="form-control form-control-sm"
                            value="{{ old('building', $rack->building) }}">
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-sm btn-primary">Update Rack</button>
            <a href="{{ route('locations.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
        </div>

    </form>

@endsection