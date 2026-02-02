@extends('layouts.app')

@section('title', 'Create Location')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('locations.index') }}" class="text-decoration-none text-muted">Locations</a> /
            <strong class="text-dark">Create</strong>
        </h6>
    </div>

    <form action="{{ route('locations.store') }}" method="POST">
        @csrf

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
                                    value="{{ old('code') }}">
                            </div>

                            {{-- Storage Name --}}
                            <div class="col-12">
                                <label class="form-label small">Room Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm" required
                                    value="{{ old('name') }}">
                            </div>

                            {{-- Storage Type --}}
                            <div class="col-12">
                                <label class="form-label small">Storage Type</label>
                                <select id="storage_type_select" class="form-select form-select-sm mb-2">
                                    <option value="Room" {{ old('storage_type', 'Room') == 'Room' ? 'selected' : '' }}>Room</option>
                                    <option value="Department" {{ old('storage_type') == 'Department' ? 'selected' : '' }}>Department</option>
                                    <option value="Other" {{ old('storage_type') && !in_array(old('storage_type'), ['Room', 'Department']) ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <input type="text" id="storage_type_input" name="storage_type" class="form-control form-control-sm"
                                    value="{{ old('storage_type', 'Room') }}" placeholder="Input manual..."
                                    style="{{ old('storage_type') && !in_array(old('storage_type'), ['Room', 'Department']) ? '' : 'display:none;' }}">
                            </div>

                            {{-- Warehouse --}}
                            <div class="col-12">
                                <label class="form-label small">Warehouse Type</label>
                                <select id="warehouse_select" class="form-select form-select-sm mb-2" onchange="toggleManualInput(this, 'warehouse_input')">
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="Indoor" {{ old('warehouse') == 'Indoor' ? 'selected' : '' }}>Indoor</option>
                                    <option value="Outdoor" {{ old('warehouse') == 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
                                    <option value="Other" {{ old('warehouse') && !in_array(old('warehouse'), ['Indoor', 'Outdoor']) ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <input type="text" id="warehouse_input" name="warehouse" class="form-control form-control-sm"
                                    value="{{ old('warehouse') }}" placeholder="Input manual..."
                                    style="{{ old('warehouse') && !in_array(old('warehouse'), ['Indoor', 'Outdoor']) ? '' : 'display:none;' }}">
                            </div>

                            {{-- Total Bin --}}
                            <div class="col-12">
                                <label class="form-label small">Total Bin</label>
                                <input type="number" name="total_bin" id="total_bin" class="form-control form-control-sm bg-light"
                                    value="1" readonly>
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
                                    {{-- Initial Row --}}
                                    <tr>
                                        <td>
                                            <input type="text" name="racks[0][code]" class="form-control form-control-sm" placeholder="e.g. A-01">
                                        </td>
                                        <td>
                                            <input type="text" name="racks[0][description]" class="form-control form-control-sm" placeholder="Optional">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-danger remove-row" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-sm btn-primary">Save Location & Bins</button>
            <a href="{{ route('locations.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    function toggleManualInput(select, inputId) {
        var input = document.getElementById(inputId);
        if (select.value === 'Other') {
            input.style.display = 'block';
            input.value = ''; 
            input.focus();
        } else {
            input.style.display = 'none';
            input.value = select.value; 
        }
    }

    // Add listener to warehouse too for consistency
    document.getElementById('warehouse_select').addEventListener('change', function() {
        toggleManualInput(this, 'warehouse_input');
    });

    document.getElementById('storage_type_select').addEventListener('change', function() {
        toggleManualInput(this, 'storage_type_input');
    });

    // Dynamic Rack Rows
    document.addEventListener('DOMContentLoaded', function() {
        let rackIndex = 1;
        const infoTableBody = document.querySelector('#rack-table tbody');
        const totalBinInput = document.getElementById('total_bin');

        function updateTotalBin() {
            const rowCount = infoTableBody.querySelectorAll('tr').length;
            totalBinInput.value = rowCount;
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
                        <button type="button" class="btn btn-xs btn-danger remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            infoTableBody.insertAdjacentHTML('beforeend', row);
            rackIndex++;
            updateTotalBin();
        });

        infoTableBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
                updateTotalBin();
            }
        });
        
        // Initial setup
        updateTotalBin();
    });
</script>
@endpush