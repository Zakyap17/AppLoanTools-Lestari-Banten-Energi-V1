@extends('layouts.app')

@section('title', 'New Loan')

@section('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-bold">New Loan Transaction</h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('loan.index') }}" class="text-decoration-none">Loan</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <form action="{{ route('loan.store') }}" method="POST" id="loanForm">
                @csrf

                {{-- SECTION 1 – Loan Information --}}
                <div class="card mb-3 border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">Loan Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Document No</label>
                                <input type="text" name="loan_code" class="form-control bg-light"
                                    value="{{ $docNo }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Loan Date <span class="text-danger">*</span></label>
                                <input type="date" name="loan_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Return Date <span class="text-danger">*</span></label>
                                <input type="date" name="expected_return_date" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Borrower <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="" selected disabled>Select Borrower</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->nik }} - {{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Keeper <span class="text-danger">*</span></label>
                                <select name="keeper_id" class="form-select" required>
                                    <option value="" {{ !$detectedKeeperId ? 'selected' : '' }} disabled>Select Keeper</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ $detectedKeeperId == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->nik }} - {{ $emp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Reason</label>
                                <textarea name="notes" class="form-control" rows="2"
                                    placeholder="Purpose of loan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2 – Select Asset --}}
                <div class="card mb-3 border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">Select Asset</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Search Asset / Tool</label>
                                <select id="toolSelector" class="form-select" style="width: 100%;">
                                    <option value="">-- Search by Name / Code --</option>
                                    <!-- Options populated via AJAX -->
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 bg-light p-3 rounded align-items-end">
                            <input type="hidden" id="tool_id">
                            <div class="col-md-3">
                                <label class="form-label small text-secondary">Asset Number</label>
                                <input type="text" id="asset_code" class="form-control bg-white" readonly>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small text-secondary">Asset Name</label>
                                <input type="text" id="asset_name" class="form-control bg-white" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-secondary">Stock</label>
                                <input type="text" id="max_qty" class="form-control bg-white" readonly>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label small fw-semibold">Select Specific Items (Serials)</label>
                                <div id="serialSelectionContainer" class="border p-2 bg-white rounded"
                                    style="max-height: 150px; overflow-y: auto; display: none;">
                                    <div class="text-muted small">Loading items...</div>
                                </div>
                                <div class="mt-1 small fw-semibold text-primary" id="selectedCountDisplay">Selected: 0</div>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label small text-secondary">Notes (Optional)</label>
                                <input type="text" id="input_notes" class="form-control"
                                    placeholder="Notes per item group...">
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="button" id="addItemBtn" class="btn btn-dark btn-sm px-4" disabled>
                                    <i class="bi bi-plus-lg me-1"></i> Add to List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3 – Items List --}}
                <div class="card mb-4 border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                    <div class="card-body p-0">
                        <div class="p-4 pb-3 border-bottom bg-light">
                            <h6 class="fw-bold mb-0">Loan Items</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small ps-4 fw-semibold">Asset No</th>
                                        <th class="small fw-semibold">Asset Name</th>
                                        <th class="small text-center fw-semibold">Qty</th>
                                        <th class="small fw-semibold">Serials</th>
                                        <th class="small fw-semibold">Notes</th>
                                        <th class="small text-center fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted py-5 small">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                            No items added yet. Search and add assets above.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Hidden Container for Form Submission --}}
                <div id="hiddenInputsContainer"></div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('loan.index') }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-dark px-5" id="submitBtn" disabled>
                        <i class="bi bi-check-lg me-1"></i> Save Loan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <!-- jQuery (Required for Select2) -->
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const submitBtn = document.getElementById('submitBtn');
                const itemsTableBody = document.querySelector('#itemsTable tbody');
                const emptyRow = document.getElementById('emptyRow');
                const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');

                // Inputs
                const toolIdInput = document.getElementById('tool_id');
                const assetCodeInput = document.getElementById('asset_code');
                const assetNameInput = document.getElementById('asset_name');
                const maxQtyInput = document.getElementById('max_qty');
                const inputNotes = document.getElementById('input_notes');
                const serialSelectionContainer = document.getElementById('serialSelectionContainer');
                const selectedCountDisplay = document.getElementById('selectedCountDisplay');

                let addedItems = [];

                // --- 1 Month Auto-Fill Logic ---
                const loanDateInput = document.querySelector('input[name="loan_date"]');
                const returnDateInput = document.querySelector('input[name="expected_return_date"]');

                function updateReturnDate() {
                    const loanDateVal = loanDateInput.value;
                    if (loanDateVal) {
                        const date = new Date(loanDateVal);
                        
                        // Add 1 month logically
                        date.setMonth(date.getMonth() + 1);

                        // Format to YYYY-MM-DD
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');

                        returnDateInput.value = `${year}-${month}-${day}`;
                    }
                }

                if (loanDateInput && returnDateInput) {
                    if (loanDateInput.value && !returnDateInput.value) {
                        updateReturnDate();
                    }
                    loanDateInput.addEventListener('change', updateReturnDate);
                }
                // ---------------------------

                // --- SELECT2 LOGIC ---
                // Employee & Keeper Dropdowns
                $('select[name="employee_id"]').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });

                $('select[name="keeper_id"]').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });

                $('#toolSelector').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Type at least 3 characters to search...',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 3,
                    templateResult: function(data) {
                        if (data.loading) {
                            // Create loading element with spinner
                            var $container = $('<div class="text-center p-2"></div>');
                            var $spinner = $('<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>');
                            var $text = $('<span>Searching...</span>');
                            $container.append($spinner).append($text);
                            return $container;
                        }
                        return data.text;
                    },
                    language: {
                        inputTooShort: function () {
                            return 'Please enter at least 3 characters';
                        },
                        noResults: function () {
                            return 'No assets found';
                        },
                        searching: function() {
                            return 'Searching...';
                        }
                    },
                    ajax: {
                        url: "{{ route('loan.search-asset') }}",
                        dataType: 'json',
                        delay: 500,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    }
                });

                $('#toolSelector').on('select2:select', function (e) {
                    const data = e.params.data;
                    // data contains: id, text, code, name, qty (from API)
                    
                    const toolData = {
                        id: data.id,
                        code: data.code,
                        name: data.name,
                        quantity: data.qty
                    };
                    selectAsset(toolData);
                });

                $('#toolSelector').on('select2:clear', function (e) {
                    resetInput();
                });

                function selectAsset(tool) {
                    toolIdInput.value = tool.id;
                    assetCodeInput.value = tool.code;
                    assetNameInput.value = tool.name;
                    maxQtyInput.value = tool.quantity;

                    // Fetch Available Serials
                    fetchSerials(tool.id);
                    addItemBtn.disabled = true; // Wait for selection
                }

                function fetchSerials(toolId) {
                    serialSelectionContainer.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center py-4">
                            <div class="spinner-border text-dark" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="ms-3 text-muted small">Loading items history...</div>
                        </div>
                    `;
                    serialSelectionContainer.style.display = 'block';
                    selectedCountDisplay.innerText = 'Selected: 0';
                    
                    fetch(`/loan/items/${toolId}`)
                        .then(res => res.json())
                        .then(items => {
                            serialSelectionContainer.innerHTML = '';
                            if (items.length === 0) {
                                serialSelectionContainer.innerHTML = '<div class="text-danger small">No serialize items found. Migration needed?</div>';
                                return;
                            }

                            items.forEach(item => {
                                const isDamaged = item.condition && item.condition.toLowerCase() === 'damage';
                                const div = document.createElement('div');
                                div.className = 'form-check form-check-sm mb-1';
                                div.innerHTML = `
                                    <input class="form-check-input serial-checkbox" type="checkbox" 
                                        value="${item.id}" 
                                        data-serial="${item.serial_number}" 
                                        id="serial_${item.id}"
                                        ${isDamaged ? 'disabled' : ''}>
                                    <label class="form-check-label small ${isDamaged ? 'text-danger' : ''}" for="serial_${item.id}">
                                        ${item.serial_number} 
                                        <span class="${isDamaged ? 'fw-bold' : 'text-muted'}">
                                            (${item.condition || 'Good'})
                                        </span>
                                    </label>
                                `;
                                serialSelectionContainer.appendChild(div);
                            });

                            // Attach listeners
                            const checkboxes = serialSelectionContainer.querySelectorAll('.serial-checkbox');
                            checkboxes.forEach(cb => {
                                cb.addEventListener('change', function () {
                                    updateSelectedCount();
                                });
                            });
                        });
                }

                function updateSelectedCount() {
                    const checkboxes = serialSelectionContainer.querySelectorAll('.serial-checkbox:checked');
                    selectedCountDisplay.innerText = `Selected: ${checkboxes.length}`;
                    addItemBtn.disabled = checkboxes.length === 0;
                }

                // Add Item Logic
                addItemBtn.addEventListener('click', function () {
                    const id = toolIdInput.value;
                    const code = assetCodeInput.value;
                    const name = assetNameInput.value;
                    const notes = inputNotes.value;

                    // Get selected serials
                    const selectedCheckboxes = serialSelectionContainer.querySelectorAll('.serial-checkbox:checked');
                    let selectedItems = [];
                    selectedCheckboxes.forEach(cb => {
                        selectedItems.push({
                            id: cb.value,
                            serial: cb.getAttribute('data-serial')
                        });
                    });

                    if (!id) return;
                    if (selectedItems.length === 0) {
                        alert('Please select at least one item.');
                        return;
                    }

                    if (addedItems.find(i => i.id === id)) {
                        alert('Item Group already in list! Remove it first to re-add.');
                        return;
                    }

                    addedItems.push({ id, code, name, qty: selectedItems.length, serials: selectedItems, notes });
                    renderTable();
                    
                    // Reset Input
                    $('#toolSelector').val(null).trigger('change');
                });

                function resetInput() {
                    toolIdInput.value = '';
                    assetCodeInput.value = '';
                    assetNameInput.value = '';
                    maxQtyInput.value = '';

                    serialSelectionContainer.innerHTML = '';
                    serialSelectionContainer.style.display = 'none';
                    selectedCountDisplay.innerText = 'Selected: 0';

                    inputNotes.value = '';
                    addItemBtn.disabled = true;
                    // Note: clearing val(null) triggers clear event which calls resetInput again if we are not careful.
                    // But here it's fine as we just clear inputs.
                }

                function renderTable() {
                    itemsTableBody.innerHTML = '';
                    hiddenInputsContainer.innerHTML = '';

                    if (addedItems.length === 0) {
                        itemsTableBody.appendChild(emptyRow);
                        submitBtn.disabled = true;
                        return;
                    }

                    submitBtn.disabled = false;

                    addedItems.forEach((item, index) => {
                        const serialsList = item.serials.map(s => `<span class="badge bg-light text-dark border me-1 mb-1">${s.serial}</span>`).join('');

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="small ps-3 fw-bold align-middle">${item.code}</td>
                            <td class="small align-middle">${item.name}</td>
                            <td class="small text-center align-middle">${item.qty}</td>
                            <td class="small align-middle">${serialsList}</td>
                            <td class="small align-middle text-muted">${item.notes || '-'}</td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-danger btn-sm p-0 px-2 remove-btn" data-index="${index}">
                                    &times;
                                </button>
                            </td>
                        `;
                        itemsTableBody.appendChild(row);

                        createHiddenInput(`items[${index}][tool_id]`, item.id);
                        createHiddenInput(`items[${index}][notes]`, item.notes);

                        item.serials.forEach(s => {
                            createHiddenInput(`items[${index}][tool_item_ids][]`, s.id);
                        });
                    });

                    document.querySelectorAll('.remove-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const idx = this.getAttribute('data-index');
                            addedItems.splice(idx, 1);
                            renderTable();
                        });
                    });
                }

                function createHiddenInput(name, value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    hiddenInputsContainer.appendChild(input);
                }
            });
        </script>
    @endpush
@endsection