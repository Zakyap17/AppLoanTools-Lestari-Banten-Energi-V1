@extends('layouts.app')

@section('title', 'Create New Tools (Bulk)')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Master / <a href="{{ route('tools.index') }}" class="text-decoration-none text-muted">Tools</a> / <strong
                class="text-dark">New Tools (Bulk)</strong>
        </h6>
    </div>

    {{-- CARD 1: FORM INPUT --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">New Tools :</h5>
            <button type="button" class="btn-close" onclick="window.location.href='{{ route('tools.index') }}'"></button>
        </div>
        <hr class="my-0">
        <div class="card-body px-4 py-4">

            <form id="addToolForm">
                <div class="row g-3">

                    <!-- No. (Auto-incremented in JS) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">No.</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="input_code" class="form-control form-control-sm bg-light"
                            value="{{ $mtCode }}" readonly>
                    </div>

                    <!-- Tools Asset No. (Auto-incremented in JS) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Tools Asset No.</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="input_asset_no" class="form-control form-control-sm bg-light"
                            value="{{ $assetNo }}" readonly>
                    </div>

                    <!-- Description -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Description <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="input_name" class="form-control form-control-sm"
                            placeholder="e.g. Bosch Drill 500W">
                    </div>

                    <!-- PO Number -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">PO Number</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="input_po" class="form-control form-control-sm" placeholder="Optional">
                    </div>

                    <!-- Material Type -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Material Type</label>
                    </div>
                    <div class="col-md-9">
                        <select id="input_type" class="form-select form-select-sm">
                            <option value="">- Pilih Material Type -</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" data-name="{{ $type->code }}">{{ $type->code }} -
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Manufacturer -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Manufacturer</label>
                    </div>
                    <div class="col-md-9">
                        <select id="input_manf" class="form-select form-select-sm">
                            <option value="">- Pilih Manufacturer -</option>
                            @foreach($manufacturers as $man)
                                <option value="{{ $man->id }}" data-name="{{ $man->name }}">{{ $man->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- UOM -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">UOM</label>
                    </div>
                    <div class="col-md-9">
                        <select id="input_uom" class="form-select form-select-sm">
                            <option value="">- Pilih UOM -</option>
                            @foreach($uoms as $uom)
                                <option value="{{ $uom }}">{{ $uom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Price</label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" step="0.01" id="input_price" class="form-control form-control-sm"
                            placeholder="0">
                    </div>

                    <!-- Tools Room (Location) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Tools Room</label>
                    </div>
                    <div class="col-md-9">
                        <select id="input_loc" class="form-select form-select-sm">
                            <option value="">- Pilih Tools Room -</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-name="{{ $loc->name }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bin Location (Rack) -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Bin Location</label>
                    </div>
                    <div class="col-md-9">
                        <select id="input_rack" class="form-select form-select-sm" disabled>
                            <option value="">- Pilih Bin Location -</option>
                        </select>
                    </div>

                    <!-- Initial Quantity -->
                    <div class="col-md-3 text-end align-self-center">
                        <label class="form-label small fw-bold mb-0">Initial Quantity <span
                                class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" id="input_qty" class="form-control form-control-sm" value="0" min="1">
                    </div>

                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="button" id="addToCartBtn" class="btn btn-sm btn-dark px-4 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Add to List
                    </button>
                    <button type="button" id="clearFormBtn"
                        class="btn btn-sm btn-light border px-4 fw-bold ms-2">Clear</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD 2: PREVIEW TABLE --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Preview List</h5>
            <div id="itemCountBadge" class="badge bg-primary rounded-pill">0 Items</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small ps-4">No.</th>
                            <th class="small">Description</th>
                            <th class="small">Type / Manufacturer</th>
                            <th class="small">Location</th>
                            <th class="small text-center">Qty</th>
                            <th class="small text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <tr id="emptyRow">
                            <td colspan="6" class="text-center text-muted py-5 small">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                                List is empty. Add tools using the form above.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3 text-end">
            <button type="button" id="submitAllBtn" class="btn btn-success fw-bold px-4" disabled>
                <i class="bi bi-save me-1"></i> Save All Tools
            </button>
        </div>
    </div>

    {{-- Hidden Form for Submission --}}
    <form id="bulkForm" action="{{ route('tools.store') }}" method="POST" class="d-none">
        @csrf
        <div id="hiddenInputs"></div>
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // -- Elements --
                const addToCartBtn = document.getElementById('addToCartBtn');
                const clearFormBtn = document.getElementById('clearFormBtn');
                const cartTableBody = document.getElementById('cartTableBody');
                const emptyRow = document.getElementById('emptyRow');
                const itemCountBadge = document.getElementById('itemCountBadge');
                const submitAllBtn = document.getElementById('submitAllBtn');
                const bulkForm = document.getElementById('bulkForm');
                const hiddenInputs = document.getElementById('hiddenInputs');

                // Auto IDs
                const inputCode = document.getElementById('input_code');
                const inputAssetNo = document.getElementById('input_asset_no');

                // Rack Loading
                const locSelect = document.getElementById('input_loc');
                const rackSelect = document.getElementById('input_rack');

                // -- Cart Data --
                let cart = [];

                // -- Location Logic --
                locSelect.addEventListener('change', function () {
                    const locId = this.value;
                    rackSelect.innerHTML = '<option value="">Loading...</option>';
                    rackSelect.disabled = true;

                    if (locId) {
                        fetch("{{ route('tools.get-bins', ':id') }}".replace(':id', locId))
                            .then(res => res.json())
                            .then(data => {
                                rackSelect.innerHTML = '<option value="">- Pilih Bin Location -</option>';
                                data.forEach(bin => {
                                    const opt = document.createElement('option');
                                    opt.value = bin.id;
                                    opt.innerText = bin.code + (bin.description ? ` - ${bin.description}` : '');
                                    opt.setAttribute('data-name', bin.code);
                                    rackSelect.appendChild(opt);
                                });
                                rackSelect.disabled = false;
                            });
                    } else {
                        rackSelect.innerHTML = '<option value="">- Pilih Bin Location -</option>';
                    }
                });

                // -- Helper: Increment ID --
                function incrementId(strId) {
                    // Assuming ID format is like "2600001" or "TAS00672"
                    // Extract number part
                    const match = strId.match(/(\d+)$/);
                    if (match) {
                        const numLength = match[1].length;
                        const numVal = parseInt(match[1]);
                        const prefix = strId.substring(0, strId.length - numLength);
                        const newNum = String(numVal + 1).padStart(numLength, '0');
                        return prefix + newNum;
                    }
                    return strId; // Fallback
                }

                // -- Add to Cart --
                addToCartBtn.addEventListener('click', function () {
                    // Validate
                    const name = document.getElementById('input_name').value.trim();
                    const qty = parseInt(document.getElementById('input_qty').value);

                    if (!name || isNaN(qty) || qty < 1) {
                        Swal.fire('Error', 'Please enter a description and valid quantity.', 'error');
                        return;
                    }

                    // Gather Data
                    const typeSelect = document.getElementById('input_type');
                    const manfSelect = document.getElementById('input_manf');
                    const locSelect = document.getElementById('input_loc');
                    const rackSelect = document.getElementById('input_rack');

                    const item = {
                        id: Date.now(),
                        code: inputCode.value,
                        asset_no: inputAssetNo.value,
                        name: name,
                        po_number: document.getElementById('input_po').value,
                        type_id: typeSelect.value,
                        type_name: typeSelect.options[typeSelect.selectedIndex].text,
                        manufacturer_id: manfSelect.value,
                        manf_name: manfSelect.options[manfSelect.selectedIndex].text,
                        uom: document.getElementById('input_uom').value,
                        price: document.getElementById('input_price').value,
                        location_id: locSelect.value,
                        loc_name: locSelect.options[locSelect.selectedIndex].getAttribute('data-name') || '-',
                        rack_id: rackSelect.value,
                        rack_name: rackSelect.options[rackSelect.selectedIndex].getAttribute('data-name') || '-',
                        quantity: qty
                    };

                    cart.push(item);
                    renderCart();

                    // Increment IDs for next input
                    inputCode.value = incrementId(inputCode.value);
                    inputAssetNo.value = incrementId(inputAssetNo.value);

                    resetForm(false);
                });

                clearFormBtn.addEventListener('click', function () {
                    resetForm(true);
                });

                function renderCart() {
                    cartTableBody.innerHTML = '';
                    itemCountBadge.innerText = cart.length + ' Items';

                    if (cart.length === 0) {
                        cartTableBody.appendChild(emptyRow);
                        submitAllBtn.disabled = true;
                        return;
                    }

                    submitAllBtn.disabled = false;

                    cart.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                                                <td class="ps-4 fw-bold text-secondary">${item.code}</td>
                                                <td>
                                                    <div class="fw-bold text-dark">${item.name}</div>
                                                    <div class="small text-muted">${item.asset_no}</div>
                                                </td>
                                                <td class="small">
                                                    <span class="d-block text-secondary">${item.type_name !== '- Pilih Material Type -' ? item.type_name : '-'}</span>
                                                    <span class="d-block text-muted text-secondary">${item.manf_name !== '- Pilih Manufacturer -' ? item.manf_name : '-'}</span>
                                                </td>
                                                <td class="small">
                                                     <span class="badge bg-light text-dark border">${item.loc_name}</span>
                                                     ${item.rack_name !== '-' ? '<i class="bi bi-chevron-right text-muted mx-1"></i> ' + item.rack_name : ''}
                                                </td>
                                                <td class="text-center fw-bold">${item.quantity}</td>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-outline-danger btn-sm border-0" onclick="removeItem(${index})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            `;
                        cartTableBody.appendChild(tr);
                    });
                }

                window.removeItem = function (index) {
                    cart.splice(index, 1);
                    renderCart();
                };

                function resetForm(fullClear = false) {
                    document.getElementById('input_name').value = '';
                    document.getElementById('input_po').value = '';
                    if (fullClear) {
                        document.getElementById('input_type').selectedIndex = 0;
                        document.getElementById('input_manf').selectedIndex = 0;
                        document.getElementById('input_uom').selectedIndex = 0;
                        document.getElementById('input_price').value = '';
                        document.getElementById('input_loc').selectedIndex = 0;
                        rackSelect.innerHTML = '<option value="">- Pilih Bin Location -</option>';
                        rackSelect.disabled = true;
                    }
                    document.getElementById('input_qty').value = 0;
                    document.getElementById('input_name').focus();
                }

                // -- Submit All --
                submitAllBtn.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Save All Tools?',
                        text: `You are about to create ${cart.length} new tool records.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Save All',
                        confirmButtonColor: '#198754'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitData();
                        }
                    });
                });

                function submitData() {
                    hiddenInputs.innerHTML = '';

                    cart.forEach((item, index) => {
                        addHiddenInput(`tools[${index}][name]`, item.name);
                        addHiddenInput(`tools[${index}][po_number]`, item.po_number);
                        addHiddenInput(`tools[${index}][type_id]`, item.type_id);
                        addHiddenInput(`tools[${index}][manufacturer_id]`, item.manufacturer_id);
                        addHiddenInput(`tools[${index}][uom]`, item.uom);
                        addHiddenInput(`tools[${index}][price]`, item.price);
                        addHiddenInput(`tools[${index}][location_id]`, item.location_id);
                        addHiddenInput(`tools[${index}][rack_id]`, item.rack_id);
                        addHiddenInput(`tools[${index}][quantity]`, item.quantity);
                    });

                    const formData = new FormData(bulkForm);

                    fetch(bulkForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Success', data.message, 'success').then(() => {
                                    window.location.href = data.redirect;
                                });
                            } else {
                                let errorMessage = data.message || 'Something went wrong';
                                if (data.errors) {
                                    errorMessage += '<div class="text-start mt-2 small text-danger"><ul>';
                                    for (const [key, value] of Object.entries(data.errors)) {
                                        errorMessage += `<li>${value}</li>`;
                                    }
                                    errorMessage += '</ul></div>';
                                }

                                Swal.fire({
                                    title: 'Error',
                                    html: errorMessage,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Server Error. Check console.', 'error');
                        });
                }

                function addHiddenInput(name, value) {
                    if (value === null || value === undefined || value === '') return;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    hiddenInputs.appendChild(input);
                }
            });
        </script>
    @endpush

@endsection