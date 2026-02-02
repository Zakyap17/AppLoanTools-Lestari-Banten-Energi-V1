@extends('layouts.app')

@section('title', 'Edit Location')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('locations.index') }}" class="text-decoration-none text-muted">Locations</a> /
            <strong class="text-dark">Edit</strong>
        </h6>
    </div>

    <form action="{{ route('locations.update', $location->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- FORM STORAGE --}}
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Tools Room Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Storage Code --}}
                            <div class="col-12">
                                <label class="form-label small">Room Code *</label>
                                <input type="text" name="code" class="form-control form-control-sm" required
                                    value="{{ old('code', $location->code) }}">
                            </div>

                            {{-- Storage Name --}}
                            <div class="col-12">
                                <label class="form-label small">Room Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm" required
                                    value="{{ old('name', $location->name) }}">
                            </div>

                            {{-- Storage Type --}}
                            <div class="col-12">
                                <label class="form-label small">Storage Type</label>
                                @php
                                    $currentType = old('storage_type', $location->storage_type);
                                    $isOther = $currentType && !in_array($currentType, ['Room', 'Department']);
                                @endphp
                                <select id="storage_type_select" class="form-select form-select-sm mb-2" onchange="toggleManualInput(this, 'storage_type_input')">
                                    <option value="" disabled {{ !$currentType ? 'selected' : '' }}>Select Type</option>
                                    <option value="Room" {{ $currentType == 'Room' ? 'selected' : '' }}>Room</option>
                                    <option value="Department" {{ $currentType == 'Department' ? 'selected' : '' }}>Department</option>
                                    <option value="Other" {{ $isOther ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <input type="text" id="storage_type_input" name="storage_type" class="form-control form-control-sm"
                                    value="{{ $currentType }}" placeholder="Input manual..."
                                    style="{{ $isOther ? '' : 'display:none;' }}">
                            </div>

                            {{-- Warehouse --}}
                            <div class="col-12">
                                <label class="form-label small">Warehouse Type</label>
                                @php
                                    $currentWarehouse = old('warehouse', $location->warehouse);
                                    $isOtherWarehouse = $currentWarehouse && !in_array($currentWarehouse, ['Indoor', 'Outdoor']);
                                @endphp
                                <select id="warehouse_select" class="form-select form-select-sm mb-2" onchange="toggleManualInput(this, 'warehouse_input')">
                                    <option value="" disabled {{ !$currentWarehouse ? 'selected' : '' }}>Select Type</option>
                                    <option value="Indoor" {{ $currentWarehouse == 'Indoor' ? 'selected' : '' }}>Indoor</option>
                                    <option value="Outdoor" {{ $currentWarehouse == 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
                                    <option value="Other" {{ $isOtherWarehouse ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <input type="text" id="warehouse_input" name="warehouse" class="form-control form-control-sm"
                                    value="{{ $currentWarehouse }}" placeholder="Input manual..."
                                    style="{{ $isOtherWarehouse ? '' : 'display:none;' }}">
                            </div>

                            {{-- Total Bin --}}
                            <div class="col-12">
                                <label class="form-label small">Total Bin</label>
                                <input type="number" name="total_bin" id="total_bin" class="form-control form-control-sm bg-light"
                                    value="{{ old('total_bin', $location->total_bin) }}" readonly>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM RACK --}}
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Bin Details</h6>
                        <button type="button" class="btn btn-xs btn-success" id="add-rack-row">
                            <i class="bi bi-plus-lg"></i> Add Bin
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="rack-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bin Code</th>
                                        <th>Description</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($location->racks as $index => $rack)
                                        <tr>
                                            <input type="hidden" name="racks[{{ $index }}][id]" value="{{ $rack->id }}">
                                            <td>
                                                <input type="text" name="racks[{{ $index }}][code]" class="form-control form-control-sm" value="{{ $rack->code }}">
                                            </td>
                                            <td>
                                                <input type="text" name="racks[{{ $index }}][description]" class="form-control form-control-sm" value="{{ $rack->description }}">
                                            </td>
                                            <td class="text-center">
                                                {{-- Optional: Allow deleting existing racks --}}
                                                {{-- For now, maybe just disabled or handled via hidden delete flag if requested --}}
                                                <button type="button" class="btn btn-xs btn-danger remove-existing-row" data-id="{{ $rack->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                {{-- Hidden input to mark for deletion if we implement that --}}
                                                <input type="hidden" name="racks[{{ $index }}][delete]" value="0" class="delete-flag">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-sm btn-primary">Update Location</button>
            <a href="{{ route('locations.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    function toggleManualInput(select, inputId) {
        var input = document.getElementById(inputId);
        var predefined = [];

        // Determine predefined values based on input ID (simplified check)
        if (inputId === 'storage_type_input') {
            predefined = ['Room', 'Department'];
        } else if (inputId === 'warehouse_input') {
            predefined = ['Indoor', 'Outdoor'];
        }

        if (select.value === 'Other') {
            input.style.display = 'block';
            // Only clear if the current value is one of the predefined ones
            if (predefined.includes(input.value)) {
                 input.value = '';
            }
            input.focus();
        } else {
            input.style.display = 'none';
            input.value = select.value;
        }
    }
    document.getElementById('storage_type_select').addEventListener('change', function() {
        toggleManualInput(this, 'storage_type_input');
    });

    // Dynamic Rack Rows
    document.addEventListener('DOMContentLoaded', function() {
        let rackIndex = {{ $location->racks->count() }};
        const infoTableBody = document.querySelector('#rack-table tbody');
        const totalBinInput = document.getElementById('total_bin');

        function updateTotalBin() {
            // Count rows that are NOT explicitly hidden
            let count = 0;
            const rows = infoTableBody.querySelectorAll('tr');
            rows.forEach(row => {
               if (row.style.display !== 'none') {
                   count++;
               }
            });
            totalBinInput.value = count;
        }

        document.getElementById('add-rack-row').addEventListener('click', function() {
            const row = `
                <tr>
                    <td>
                        <input type="text" name="racks[${rackIndex}][code]" class="form-control form-control-sm" placeholder="e.g. A-${rackIndex + 1}">
                    </td>
                    <td>
                        <input type="text" name="racks[${rackIndex}][description]" class="form-control form-control-sm" placeholder="Optional">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-danger remove-new-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            infoTableBody.insertAdjacentHTML('beforeend', row);
            rackIndex++;
            updateTotalBin();
        });

        // Handle clicking Remove button
        infoTableBody.addEventListener('click', function(e) {
            // New Rows (inserted via JS)
            if (e.target.closest('.remove-new-row')) {
                e.target.closest('tr').remove();
                updateTotalBin();
            }
            
            // Existing Rows
            if (e.target.closest('.remove-existing-row')) {
                const tr = e.target.closest('tr');
                tr.style.display = 'none';
                tr.querySelector('.delete-flag').value = 1;
                updateTotalBin();
            }
        });
        
        // Initial setup
        updateTotalBin();
    });
</script>
@endpush