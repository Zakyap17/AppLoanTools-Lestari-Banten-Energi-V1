@extends('layouts.app')

@section('title', 'Edit Tool')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('tools.index') }}" class="text-decoration-none text-muted">Tools</a> / <strong
                class="text-dark">Edit Tools</strong>
        </h6>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Edit Tool : {{ $tool->code }}</h5>
            <button type="button" class="btn-close" onclick="window.location.href='{{ route('tools.index') }}'"></button>
        </div>
        <hr class="my-0">
        <div class="card-body px-4 py-4">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <h6 class="mb-0 fw-bold">Validation Error!</h6>
                    </div>
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('tools.update', $tool->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page') }}">

                <div class="row g-3">

                    <!-- No. (Readonly prefix MT) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">No.</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="code" class="form-control form-control-sm bg-light"
                            value="{{ old('code', $tool->code) }}" readonly>
                    </div>

                    <!-- Tools Asset No. -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Tools Asset No.</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="serial_number" class="form-control form-control-sm"
                            value="{{ old('serial_number', $tool->serial_number) }}">
                    </div>

                    <!-- Description -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Description</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control form-control-sm"
                            value="{{ old('name', $tool->name) }}" required>
                    </div>



                    <!-- Material Type (Connected to Tools Type Master) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Material Type</label>
                    </div>
                    <div class="col-md-9">
                        <select name="type_id" class="form-select form-select-sm">
                            <option value="" disabled>- Pilih Material Type -</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id', $tool->type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->code }} - {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>








                    <!-- Manufacturer -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Manufacturer</label>
                    </div>
                    <div class="col-md-9">
                        <select name="manufacturer_id" class="form-select form-select-sm">
                            <option value="" disabled>- Pilih Manufacturer -</option>
                            @foreach($manufacturers as $man)
                                <option value="{{ $man->id }}" {{ old('manufacturer_id', $tool->manufacturer_id) == $man->id ? 'selected' : '' }}>{{ $man->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- UOM -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">UOM</label>
                    </div>
                    <div class="col-md-9">
                        <select name="uom" class="form-select form-select-sm">
                             <option value="" disabled>- Pilih UOM -</option>
                             @foreach($uoms as $uom)
                                <option value="{{ $uom }}" {{ old('uom', $tool->uom) == $uom ? 'selected' : '' }}>{{ $uom }}</option>
                             @endforeach
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Price</label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" step="0.01" name="price" class="form-control form-control-sm"
                            value="{{ old('price', $tool->price) }}">
                    </div>

                    <!-- Tools Room (Location) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Tools Room</label>
                    </div>
                    <div class="col-md-9">
                        <select name="location_id" id="location_select" class="form-select form-select-sm">
                            <option value="" disabled>- Pilih Tools Room -</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id', $tool->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bin Location (Rack) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Bin Location</label>
                    </div>
                    <div class="col-md-9">
                        <select name="rack_id" id="rack_select" class="form-select form-select-sm" disabled>
                            <option value="" selected disabled>- Pilih Bin Location -</option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Quantity</label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" name="quantity" class="form-control form-control-sm"
                            value="{{ old('quantity', $tool->quantity) }}" min="0">
                    </div>

                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Update Tools</button>
                    <a href="{{ route('tools.index') }}" class="btn btn-sm btn-secondary px-4 fw-bold ms-2">Cancel</a>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Room & Bin Logic ---
                const locationSelect = document.getElementById('location_select');
                const rackSelect = document.getElementById('rack_select');
                const selectedRackId = "{{ old('rack_id', $tool->rack_id) }}";

                function fetchBins(locationId, preselectId = null) {
                    rackSelect.innerHTML = '<option value="" selected disabled>Loading...</option>';
                    rackSelect.disabled = true;

                    if(locationId) {
                        fetch(`{{ url('/tools/get-bins') }}/${locationId}`)
                            .then(response => response.json())
                            .then(data => {
                                rackSelect.innerHTML = '<option value="" selected disabled>- Pilih Bin Location -</option>';
                                data.forEach(bin => {
                                    const option = document.createElement('option');
                                    option.value = bin.id;
                                    option.text = bin.code + (bin.description ? ` - ${bin.description}` : '');
                                    if(String(bin.id) === String(preselectId)) {
                                        option.selected = true;
                                    }
                                    rackSelect.appendChild(option);
                                });
                                rackSelect.disabled = false;
                            })
                            .catch(error => {
                                console.error('Error fetching bins:', error);
                                rackSelect.innerHTML = '<option value="" selected disabled>Error loading bins</option>';
                            });
                    } else {
                        rackSelect.innerHTML = '<option value="" selected disabled>- Pilih Bin Location -</option>';
                        rackSelect.disabled = true;
                    }
                }

                locationSelect.addEventListener('change', function() {
                    fetchBins(this.value);
                });

                // Initial Load
                if(locationSelect.value) {
                    fetchBins(locationSelect.value, selectedRackId);
                }
            });
        </script>
    @endpush

@endsection