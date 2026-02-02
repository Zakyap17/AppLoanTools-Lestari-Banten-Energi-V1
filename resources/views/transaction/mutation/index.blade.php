@extends('layouts.app')

@section('title', 'Tools Mutation')

@section('full_width_header')
    @include('partials.topbar')
@endsection

@section('content')

    <style>
        /* Modern Card List Styling */
        .mutation-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .mutation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .mutation-card.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .mutation-card .card-body {
            padding: 1.25rem;
        }

        /* Custom Checkbox */
        .custom-check {
            width: 24px;
            height: 24px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .mutation-card.selected .custom-check {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        .mutation-card.selected .custom-check::after {
            content: "\f26b";
            /* Bootstrap icon check-lg */
            font-family: bootstrap-icons;
            font-size: 16px;
        }

        /* Location Badge */
        .loc-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .loc-badge i {
            color: #64748b;
        }

        /* Floating Action Bar */
        .floating-action-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            /* Hidden by default */
            background: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            min-width: 400px;
            justify-content: space-between;
        }

        .floating-action-bar.visible {
            transform: translateX(-50%) translateY(0);
        }

        .fab-count {
            background: #eff6ff;
            color: #3b82f6;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Page Header */
        /* Page Header */
        .page-header-modern {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 1.5rem;
            /* Reduced from 3rem */
            border-radius: 20px;
            /* Rounded corners */
            margin-bottom: 2rem;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            /* Removed negative margins */
        }

        .page-header-modern::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('data:image/svg+xml,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><g fill="%23ffffff" fill-opacity="0.05"><path d="M0 0h20L0 20z"/></g></svg>');
            opacity: 0.3;
        }

        .search-input-modern {
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            width: 300px;
        }

        .search-input-modern:focus {
            outline: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
    </style>

    {{-- HEADER --}}
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div class="position-relative z-1">
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Tools Transaction
            </h6>
            <h3 class="fw-bold mb-0">Tools Mutation</h3>
            <p class="text-white-50 mb-0 small mt-1">Move tools between storage locations and racks</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            <form action="{{ route('mutation') }}" method="GET" class="position-relative">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                <input type="text" name="search" class="search-input-modern ps-5" placeholder="Search asset, name..."
                    value="{{ request('search') }}" id="searchInput">
                @if(request('search'))
                    <button type="button" class="btn btn-link position-absolute text-muted"
                        style="right: 10px; top: 50%; transform: translateY(-50%); padding: 0;"
                        onclick="window.location.href='{{ route('mutation') }}'">
                        <i class="bi bi-x-lg"></i>
                    </button>
                @endif
            </form>
            <button class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal"
                data-bs-target="#createMutationModal" title="Start a new mutation">
                <i class="bi bi-plus-lg me-1"></i> New Mutation
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    {{-- LIST CONTENT --}}
    <div class="row g-3">
        @forelse ($tools as $tool)
            <div class="col-12">
                <div class="mutation-card" id="card-{{ $tool->id }}" onclick="toggleSelection({{ $tool->id }})">
                    <input type="checkbox" class="d-none row-check" id="check-{{ $tool->id }}" value="{{ $tool->id }}"
                        data-location="{{ $tool->location->name ?? 'Unknown' }}" data-rack="{{ $tool->rack->code ?? '-' }}">

                    <div class="card-body d-flex align-items-center justify-content-between">
                        {{-- LEFT: CHECKBOX & INFO --}}
                        <div class="d-flex align-items-center gap-4">
                            <div class="custom-check"></div>

                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                        {{ $tool->code }}
                                    </span>
                                    @if($tool->quantity < 5)
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                            Low Stock: {{ $tool->quantity }}
                                        </span>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">{{ $tool->name }}</h6>
                            </div>
                        </div>

                        {{-- MIDDLE: LOCATION --}}
                        <div class="d-flex align-items-center gap-4">
                            <div class="loc-badge">
                                <i class="bi bi-shop-window"></i>
                                <span>{{ $tool->location->name ?? '-' }}</span>
                            </div>
                            <i class="bi bi-arrow-right text-muted opacity-25"></i>
                            <div class="loc-badge">
                                <i class="bi bi-grid-3x3"></i>
                                <span>{{ $tool->rack->code ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- RIGHT: QTY & ACTION --}}
                        <div class="d-flex align-items-center gap-4">
                            <div class="text-end">
                                <small class="text-uppercase text-muted"
                                    style="font-size: 0.7rem; font-weight: 600;">Quantity</small>
                                <div class="fs-5 fw-bold text-dark">{{ $tool->quantity }} <span
                                        class="fs-6 text-muted fw-normal">{{ $tool->uom ?? 'Unit' }}</span></div>
                            </div>

                            {{-- Dummy Delete Action (Optional) --}}
                            <button class="btn btn-light text-danger rounded-circle p-2 border-0"
                                onclick="event.stopPropagation(); confirm('Delete item?')" title="Delete Item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">No items available for mutation</h5>
                    <p class="text-muted small">All items are either in use or not found.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4 d-flex justify-content-end">
        {{ $tools->links() }}
    </div>

    {{-- FLOATING ACTION BAR --}}
    <div class="floating-action-bar" id="floatingBar">
        <div class="d-flex align-items-center gap-3">
            <div class="fab-count">
                <span id="selectedCount">0</span> Selected
            </div>
            <span class="text-muted small border-start ps-3" id="currentStorageInfo">
                Select items to move
            </span>
        </div>

        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
            data-bs-target="#mutationModal">
            Mutate Selected <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>


    {{-- ================= EXISTING CONFIRMATION MODAL ================= --}}
    <form action="{{ route('mutation.store') }}" method="POST" id="formMutation">
        @csrf
        <div id="toolIdsContainer"></div>

        <div class="modal fade" id="mutationModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-gradient-primary text-white p-4"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">Confirm Mutation</h5>
                            <p class="mb-0 text-white-50 small">Move selected items to new location</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- INFO BOX --}}
                        <div class="alert alert-primary bg-primary-subtle border-0 d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle-fill text-primary me-3 fs-4"></i>
                            <div>
                                <div class="small text-muted text-uppercase fw-bold">Items to Move</div>
                                <strong class="text-primary fs-5" id="modalSelectedCount">0</strong> <span
                                    class="text-primary">Tools Selected</span>
                            </div>
                        </div>

                        {{-- FORM --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Target
                                Storage</label>
                            <select name="to_location_id" id="destStorage"
                                class="form-select form-select-lg bg-light border-0" required>
                                <option value="" selected disabled>Select Storage...</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->code }} - {{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Target
                                Bin</label>
                            <select name="to_rack_id" id="destBin" class="form-select form-select-lg bg-light border-0"
                                required disabled>
                                <option value="" selected disabled>Select Bin...</option>
                                @foreach($racks as $rack)
                                    <option value="{{ $rack->id }}" data-location="{{ $rack->location_id }}"
                                        class="rack-opt d-none">
                                        {{ $rack->code }} {{ $rack->description ? '(' . $rack->description . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="binHint" class="form-text small text-muted mt-2"><i class="bi bi-arrow-up-short"></i>
                                Select storage first</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Reason /
                                Notes</label>
                            <textarea name="reason" id="mutationReason" class="form-control bg-light border-0" rows="3"
                                placeholder="Why are these items being moved?"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer p-3 bg-light border-top-0 justify-content-between">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnConfirmMutation"
                            class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm" disabled>
                            Confirm Move
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    {{-- ================= NEW CREATE MUTATION SEARCH MODAL ================= --}}
    <form action="{{ route('mutation.store') }}" method="POST" id="formCreateMutation">
        @csrf
        <div class="modal fade" id="createMutationModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-white border-bottom p-4">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">New Mutation Transaction</h5>
                            <p class="mb-0 text-muted small">Search and add items to move list</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">
                        <!-- SEARCH BAR -->
                        <div class="mb-4 position-relative">
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0 ps-3"><i
                                        class="bi bi-search text-muted"></i></span>
                                <input type="text" class="form-control border-0" id="createMutationSearch"
                                    placeholder="Type items name or code..." autocomplete="off">
                            </div>
                            <!-- SEARCH RESULTS DROPDOWN -->
                            <div id="mutationSearchResults" class="list-group position-absolute w-100 shadow-lg mt-2 d-none"
                                style="z-index: 1050; max-height: 250px; overflow-y: auto;">
                            </div>
                        </div>

                        <!-- SELECTED ITEMS LIST -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between">
                                <h6 class="fw-bold mb-0">Selected Items</h6>
                                <button type="button" class="btn btn-link py-0 text-decoration-none text-danger small"
                                    onclick="clearAllItems()">Clear All</button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 200px;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="ps-4">Item Name</th>
                                                <th>Current Location</th>
                                                <th class="text-end pe-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedItemsTableBody">
                                            <tr id="emptyRow">
                                                <td colspan="3" class="text-center py-4 text-muted small">
                                                    No items selected. Search above to add.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- DESTINATION -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">To Location</label>
                                <select name="to_location_id" id="createDestStorage" class="form-select border-0 shadow-sm"
                                    required>
                                    <option value="" selected disabled>Select Storage...</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->code }} - {{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">To Bin/Rack</label>
                                <select name="to_rack_id" id="createDestBin" class="form-select border-0 shadow-sm" required
                                    disabled>
                                    <option value="" selected disabled>Select Bin...</option>
                                    @foreach($racks as $rack)
                                        <option value="{{ $rack->id }}" data-location="{{ $rack->location_id }}"
                                            class="create-rack-opt d-none">
                                            {{ $rack->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Notes</label>
                                <textarea name="reason" class="form-control border-0 shadow-sm" rows="2"
                                    placeholder="Reason for movement..."></textarea>
                            </div>
                        </div>

                        <!-- HIDDEN INPUTS CONTAINER for SELECTED ITEMS API -->
                        <div id="createHiddenInputs"></div>

                    </div>
                    <div class="modal-footer p-3 bg-white">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnCreateSubmit"
                            class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" disabled>
                            Process Mutation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('=== MUTATION PAGE SCRIPT LOADED ===');

            // Logic for Card Selection and Floating Bar
            const floatingBar = document.getElementById('floatingBar');
            const selectedCountSpan = document.getElementById('selectedCount');
            const modalCountSpan = document.getElementById('modalSelectedCount');
            const currentStorageInfo = document.getElementById('currentStorageInfo');
            const container = document.getElementById('toolIdsContainer');

            console.log('Main elements:', {
                floatingBar: !!floatingBar,
                selectedCountSpan: !!selectedCountSpan,
                modalCountSpan: !!modalCountSpan,
                currentStorageInfo: !!currentStorageInfo,
                container: !!container
            });

            window.toggleSelection = function (id) {
                const checkbox = document.getElementById('check-' + id);
                const card = document.getElementById('card-' + id);

                // Toggle state
                checkbox.checked = !checkbox.checked;

                if (checkbox.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }

                updateUI();
            }

            window.updateUI = function () {
                const floatingBar = document.getElementById('floatingBar');
                const selectedCountSpan = document.getElementById('selectedCount');
                const modalCountSpan = document.getElementById('modalSelectedCount');
                const currentStorageInfo = document.getElementById('currentStorageInfo');
                const container = document.getElementById('toolIdsContainer');

                const checkedBoxes = document.querySelectorAll('.row-check:checked');
                const count = checkedBoxes.length;

                // Update counts
                selectedCountSpan.innerText = count;
                modalCountSpan.innerText = count;

                // Toggle Floating Bar
                if (count > 0) {
                    floatingBar.classList.add('visible');
                } else {
                    floatingBar.classList.remove('visible');
                }

                // Location Validation Logic
                if (count > 0) {
                    const firstLoc = checkedBoxes[0].getAttribute('data-location');
                    const firstRack = checkedBoxes[0].getAttribute('data-rack');
                    let sameLocation = true;

                    for (let i = 1; i < checkedBoxes.length; i++) {
                        if (checkedBoxes[i].getAttribute('data-location') !== firstLoc ||
                            checkedBoxes[i].getAttribute('data-rack') !== firstRack) {
                            sameLocation = false;
                            break;
                        }
                    }

                    if (sameLocation) {
                        currentStorageInfo.innerHTML = `Moving from: <strong class="text-dark">${firstLoc} - ${firstRack}</strong>`;
                        currentStorageInfo.classList.remove('text-danger');
                    } else {
                        currentStorageInfo.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle me-1"></i> Mixed Locations Selected</span>`;
                    }
                }

                // Update Hidden Inputs
                container.innerHTML = "";
                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = "hidden";
                    input.name = "tool_ids[]";
                    input.value = cb.value;
                    container.appendChild(input);
                });
            }

            // Modal Dropdown Logic
            const destStorage = document.getElementById('destStorage');
            const destBin = document.getElementById('destBin');
            const rackOpts = document.querySelectorAll('.rack-opt');
            const binHint = document.getElementById('binHint');
            const btnConfirmMutation = document.getElementById('btnConfirmMutation');

            destStorage.addEventListener('change', function () {
                const storageId = this.value;
                destBin.value = "";

                if (storageId) {
                    destBin.removeAttribute('disabled');
                    binHint.classList.add('d-none');
                    rackOpts.forEach(opt => {
                        if (opt.getAttribute('data-location') === storageId) {
                            opt.classList.remove('d-none');
                        } else {
                            opt.classList.add('d-none');
                        }
                    });
                } else {
                    destBin.setAttribute('disabled', 'true');
                    binHint.classList.remove('d-none');
                    rackOpts.forEach(opt => opt.classList.add('d-none'));
                }
                validateModal();
            });

            destBin.addEventListener('change', validateModal);

            function validateModal() {
                if (destStorage.value !== "" && destBin.value !== "") {
                    btnConfirmMutation.removeAttribute('disabled');
                } else {
                    btnConfirmMutation.setAttribute('disabled', 'true');
                }
            }

            // --- NEW CREATE MUTATION MODAL LOGIC ---
            const createSearch = document.getElementById('createMutationSearch');
            const searchResults = document.getElementById('mutationSearchResults');
            const selectedItemsBody = document.getElementById('selectedItemsTableBody');
            const emptyRow = document.getElementById('emptyRow');
            const createHiddenContainer = document.getElementById('createHiddenInputs');
            const createDestStorage = document.getElementById('createDestStorage');
            const createDestBin = document.getElementById('createDestBin');
            const createRackOpts = document.querySelectorAll('.create-rack-opt');
            const btnCreateSubmit = document.getElementById('btnCreateSubmit');

            // Debug: Check if all elements are found
            console.log('Create Mutation Form Elements:', {
                createSearch: !!createSearch,
                searchResults: !!searchResults,
                selectedItemsBody: !!selectedItemsBody,
                emptyRow: !!emptyRow,
                createHiddenContainer: !!createHiddenContainer,
                createDestStorage: !!createDestStorage,
                createDestBin: !!createDestBin,
                createRackOptsCount: createRackOpts.length,
                btnCreateSubmit: !!btnCreateSubmit
            });

            let addedItems = new Set();
            let searchTimeout;

            createSearch.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                if (query.length < 2) {
                    searchResults.classList.add('d-none');
                    return;
                }

                // Show loading
                searchResults.innerHTML = '<div class="list-group-item p-3 text-center"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Searching...</div>';
                searchResults.classList.remove('d-none');

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('mutation.search') }}?q=${encodeURIComponent(query)}`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return res.json();
                        })
                        .then(data => {
                            searchResults.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(item => {
                                    const isAdded = addedItems.has(String(item.id));
                                    const btnHtml = isAdded
                                        ? `<span class="badge bg-success"><i class="bi bi-check"></i> Added</span>`
                                        : `<button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick='addItem(${JSON.stringify(item)})'>Add</button>`;

                                    const div = document.createElement('div');
                                    div.className = "list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-bottom";
                                    div.innerHTML = `
                                                                <div>
                                                                    <div class="fw-bold text-dark">${item.name} <small class="text-muted ms-2">${item.code}</small></div>
                                                                    <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> ${item.location?.name || '-'} / ${item.rack?.code || '-'}</div>
                                                                </div>
                                                                ${btnHtml}
                                                            `;
                                    searchResults.appendChild(div);
                                });
                                searchResults.classList.remove('d-none');
                            } else {
                                searchResults.innerHTML = `<div class="list-group-item p-3 text-muted text-center">No items found</div>`;
                                searchResults.classList.remove('d-none');
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            searchResults.innerHTML = `<div class="list-group-item p-3 text-danger text-center"><i class="bi bi-exclamation-triangle me-2"></i>Error loading results. Please try again.</div>`;
                            searchResults.classList.remove('d-none');
                        });
                }, 300);
            });

            // Hide search when clicking outside
            document.addEventListener('click', function (e) {
                if (!createSearch.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('d-none');
                }
            });

            window.addItem = function (item) {
                if (addedItems.has(String(item.id))) return;

                addedItems.add(String(item.id));
                emptyRow.style.display = 'none';

                const tr = document.createElement('tr');
                tr.id = `row-${item.id}`;
                tr.innerHTML = `
                                                <td class="ps-4">
                                                    <div class="fw-bold">${item.name}</div>
                                                    <small class="text-muted">${item.code}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">${item.location?.name || '-'}</span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle" onclick="removeItem('${item.id}')">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </td>
                                            `;
                selectedItemsBody.appendChild(tr);

                // Add Hidden Input
                const input = document.createElement('input');
                input.type = "hidden";
                input.name = "tool_ids[]";
                input.value = item.id;
                input.id = `input-${item.id}`;
                createHiddenContainer.appendChild(input);

                searchResults.classList.add('d-none');
                createSearch.value = "";
                createSearch.focus();
                validateCreateForm();
            };

            window.removeItem = function (id) {
                addedItems.delete(String(id));
                document.getElementById(`row-${id}`).remove();
                document.getElementById(`input-${id}`).remove();

                if (addedItems.size === 0) {
                    emptyRow.style.display = 'table-row';
                }
                validateCreateForm();
            };

            window.clearAllItems = function () {
                addedItems.clear();
                selectedItemsBody.innerHTML = '';
                selectedItemsBody.appendChild(emptyRow);
                emptyRow.style.display = 'table-row';
                createHiddenContainer.innerHTML = '';
                validateCreateForm();
            }

            createDestStorage.addEventListener('change', function () {
                const storageId = this.value;
                createDestBin.value = "";
                if (storageId) {
                    createDestBin.removeAttribute('disabled');
                    createRackOpts.forEach(opt => {
                        opt.classList.toggle('d-none', opt.getAttribute('data-location') !== storageId);
                    });
                } else {
                    createDestBin.setAttribute('disabled', 'true');
                }
                validateCreateForm();
            });

            createDestBin.addEventListener('change', validateCreateForm);

            function validateCreateForm() {
                const hasItems = addedItems.size > 0;
                const hasLocation = createDestStorage.value !== "";
                const hasRack = createDestBin.value !== "";

                if (hasItems && hasLocation && hasRack) {
                    btnCreateSubmit.removeAttribute('disabled');
                } else {
                    btnCreateSubmit.setAttribute('disabled', 'true');
                }
            }

            // --- HEADER SEARCH FUNCTIONALITY (Server-Side) ---
            const headerSearchInput = document.getElementById('searchInput');
            let headerSearchTimeout;

            headerSearchInput.addEventListener('input', function () {
                clearTimeout(headerSearchTimeout);
                const form = this.closest('form');

                // Auto-submit after 300ms of no typing (faster response)
                headerSearchTimeout = setTimeout(() => {
                    form.submit();
                }, 300);
            });

            // Submit on Enter key
            headerSearchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(headerSearchTimeout);
                    this.closest('form').submit();
                }
            });

        }); // End DOMContentLoaded
    </script>

@endsection