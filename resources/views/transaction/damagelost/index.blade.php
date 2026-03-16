@extends('layouts.app')

@section('title', 'Damage / Lost Reports')



@section('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endsection

@section('full_width_header')
    @include('partials.topbar')
@endsection

@section('content')

    <style>
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
            transition: all 0.2s;
        }

        .search-input-modern:focus {
            outline: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        /* Modern Table */
        .modern-table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .table-custom thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #334155;
            font-size: 0.875rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
    </style>

    {{-- HEADER --}}
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div class="position-relative z-1">
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Transaction</h6>
            <h3 class="fw-bold mb-0">Damage / Lost Reports</h3>
            <p class="text-white-50 mb-0 small mt-1">Record and track damaged or lost assets</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            <form action="{{ route('damage-lost.index') }}" method="GET" class="position-relative mb-0">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" class="search-input-modern ps-5" placeholder="Search report..."
                    value="{{ request('search') }}">
            </form>

            <button class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal"
                data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-1"></i> New Report
            </button>
        </div>
    </div>

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">
        {{-- Card 1 --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 border-start border-4 border-warning">
                <div class="card-body position-relative bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3 text-warning d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-0"
                                style="font-size: 0.7rem; letter-spacing: 0.5px;">Open Damage</h6>
                            <h2 class="fw-bold mb-0 text-dark">
                                {{ $openDamageCount }}
                            </h2>
                        </div>
                    </div>
                    <small class="text-muted ps-1">Unresolved cases</small>
                </div>
            </div>
        </div>
        {{-- Card 2 --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 border-start border-4 border-danger">
                <div class="card-body position-relative bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3 text-danger d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-x-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-0"
                                style="font-size: 0.7rem; letter-spacing: 0.5px;">Lost Items</h6>
                            <h2 class="fw-bold mb-0 text-dark">{{ $lostCount }}</h2>
                        </div>
                    </div>
                    <small class="text-muted ps-1">Total lost assets</small>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table table-custom align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center ps-4">#</th>
                        <th>Document No.</th>
                        <th>Document Date</th>
                        <th>Employee Name</th>
                        <th>Item Details</th>
                        <th style="width:100px">Status</th>
                        <th>Reason</th>
                        <th style="width:120px" class="text-center pe-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td class="text-center ps-4">
                                <button class="btn btn-sm btn-light border rounded-circle toggle-detail"
                                    data-target="damage-detail-{{ $report->id }}" title="Show Items"
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ $report->report_code }}</span>
                            </td>
                            <td>{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : '-' }}
                            </td>
                            <td>{{ $report->employee->name ?? '-' }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $report->tool?->name ?? 'Deleted Asset' }}</div>
                                <div class="small text-muted">
                                    SN: {{ $report->item?->serial_number ?? '-' }}
                                    @php
                                        $siblings = \App\Models\DamageLost::with(['tool.location', 'tool.rack', 'item'])
                                            ->where('report_code', $report->report_code)
                                            ->get();
                                        $count = $siblings->count();
                                    @endphp
                                    @if($count > 1)
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill ms-1">+{{ $count - 1 }}
                                            More</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusLabel = '';
                                    $badgeClass = '';

                                    if ($report->type === 'lost') {
                                        $statusLabel = 'Lost';
                                        $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                    } elseif ($report->type === 'damage') {
                                        if (!$report->is_repairable) {
                                            $statusLabel = 'Unrepairable';
                                            $badgeClass = 'bg-dark text-white border border-dark';
                                        } elseif ($report->status === 'open') {
                                            $statusLabel = 'Damage';
                                            $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                        } else {
                                            $statusLabel = 'Resolved';
                                            $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                        }
                                    } else {
                                        $statusLabel = ucfirst($report->status);
                                        $badgeClass = $report->status === 'open' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-success-subtle text-success border border-success-subtle';
                                    }
                                @endphp
                                <span class="badge rounded-pill px-3 {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ Str::limit($report->description, 40) }}</td>
                            <td class="text-center pe-4">
                                @if($report->type === 'lost')
                                    <span class="text-muted small">-</span>
                                @else
                                    <a href="{{ route('damage-lost.print', ['id' => $report->id, 'mode' => 'view']) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-light border rounded-circle shadow-sm p-2 text-primary"
                                        title="View Document"
                                        style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>

                        {{-- EXPAND DETAIL ROW --}}
                        <tr id="damage-detail-{{ $report->id }}" class="d-none bg-light">
                            <td colspan="8" class="p-4 bg-light border-bottom">
                                <div class="bg-white rounded-3 shadow-sm border overflow-hidden p-3">
                                    <h6 class="fw-bold small text-uppercase text-muted mb-3"><i
                                            class="bi bi-list-check me-2"></i> Report Items</h6>
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="text-secondary" style="font-size: 0.8rem;">
                                            <tr>
                                                <th style="width:40px">No.</th>
                                                <th>Storage</th>
                                                <th>Serial No.</th>
                                                <th>Item Name</th>
                                                <th style="width:60px" class="text-center">QTY</th>
                                                <th style="width:80px">Severity</th>
                                                <th>Status</th>
                                                <th style="width:100px" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 0.85rem;">
                                            @foreach($siblings as $index => $sib)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-muted">{{ $sib->tool?->location?->name ?? '-' }}
                                                        ({{ $sib->tool?->rack?->code ?? '-' }})</td>
                                                    <td class="text-muted">{{ $sib->item?->serial_number ?? '-' }}</td>
                                                    <td class="fw-bold text-dark">{{ $sib->tool?->name ?? 'Deleted Asset' }}</td>
                                                    <td class="text-center">{{ $sib->quantity }}</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge rounded-pill {{ $sib->severity === 'high' ? 'bg-danger-subtle text-danger' : ($sib->severity === 'medium' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info') }} small px-2">
                                                            {{ ucfirst($sib->severity) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge rounded-pill {{ $sib->type === 'damage' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger' }} small">
                                                            {{ ucfirst($sib->type) }}
                                                        </span>
                                                        @if(!$sib->is_repairable && $sib->type === 'damage')
                                                            <span class="badge bg-dark text-white micro-badge ms-1">Unrepairable</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($sib->type === 'damage' && $sib->status === 'open' && $sib->is_repairable)
                                                            <button
                                                                class="btn btn-sm btn-success text-white py-1 px-3 rounded-pill small shadow-sm"
                                                                onclick="confirmResolve({{ $sib->id }}, '{{ $sib->item->serial_number ?? $sib->tool->name }}')"
                                                                title="Mark as Resolved">
                                                                <i class="bi bi-check-lg me-1"></i> Resolve
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox display-4 opacity-25 mb-3"></i>
                                    <h6 class="fw-normal">No damage or lost reports found</h6>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-top bg-light bg-opacity-25 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }}
                entries
            </div>
            <div>
                {{ $reports->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    {{-- VIEW MODAL --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Report Code</label>
                            <div class="fw-bold" id="viewCode">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Report Date</label>
                            <div id="viewDate">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Employee</label>
                            <div id="viewEmployee">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Tool / Asset</label>
                            <div id="viewTool">-</div>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Status</label>
                            <div><span class="badge bg-secondary" id="viewStatus">-</span></div>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Description</label>
                            <div class="p-2 bg-light rounded border small" id="viewDescription">-</div>
                        </div>

                        {{-- EVIDENCE SECTION --}}
                        <div class="col-12 d-none" id="viewEvidenceSection">
                            <hr>
                            <label class="small fw-bold text-muted mb-2">Resolution Evidence (Bukti Perbaikan)</label>
                            <div class="text-center bg-dark rounded p-3">
                                <img src="" id="viewEvidenceImg" class="img-fluid rounded shadow-sm"
                                    style="max-height: 400px; object-fit: contain;">
                                <a href="" id="viewEvidenceLink" target="_blank" class="btn btn-sm btn-light mt-2">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Open Original File
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL RESOLVE CONFIRMATION --}}
    <div class="modal fade" id="resolveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formResolve" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Resolve Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-tools text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="fw-semibold mb-2">Mark as Resolved?</h6>
                        <p class="text-muted small mb-0" id="resolveMessageBody">
                            <!-- Message will be set by JS -->
                        </p>

                        {{-- EVIDENCE UPLOAD (MODERN) --}}
                        <div class="mt-4 text-start">
                            <label class="form-label small fw-bold mb-2">Evidence (Bukti Perbaikan)</label>

                            <div class="upload-zone p-4 text-center border border-2 border-dashed rounded-4 position-relative"
                                style="border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.2s;"
                                ondragover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='#eff6ff'; event.preventDefault();"
                                ondragleave="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';"
                                ondrop="handleEvidenceDrop(event)">

                                <input type="file" name="evidence" id="evidenceInput"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    accept="image/*,application/pdf" style="cursor: pointer;"
                                    onchange="handleEvidenceSelect(this)">

                                <div style="pointer-events: none;">
                                    <div class="mb-2">
                                        <div class="bg-primary bg-opacity-10 d-inline-flex p-2 rounded-circle text-primary">
                                            <i class="bi bi-cloud-arrow-up fs-4"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 small">Click or drag proof here</h6>
                                    <p class="text-muted extra-small mb-0" id="evidenceNameDisplay"
                                        style="font-size: 0.7rem;">
                                        JPG, PNG, PDF (Max 10MB)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <script>
                            function handleEvidenceSelect(input) {
                                if (input.files.length > 0) {
                                    validateEvidence(input.files[0], input);
                                }
                            }

                            function handleEvidenceDrop(event) {
                                event.preventDefault();
                                const zone = event.currentTarget;
                                zone.style.borderColor = '#cbd5e1';
                                zone.style.backgroundColor = '#f8fafc';

                                const input = document.getElementById('evidenceInput');
                                if (event.dataTransfer.files.length > 0) {
                                    input.files = event.dataTransfer.files;
                                    validateEvidence(event.dataTransfer.files[0], input);
                                }
                            }

                            function validateEvidence(file, input) {
                                const display = document.getElementById('evidenceNameDisplay');
                                const maxSize = 10 * 1024 * 1024; // 10MB

                                if (file.size > maxSize) {
                                    // Error
                                    display.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-circle-fill me-1"></i> File too large (> 10MB)!</span>';
                                    input.value = ''; // Clear input
                                } else {
                                    // Success
                                    display.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> ' + file.name + '</span>';
                                }
                            }
                        </script>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            Yes, Resolve
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CREATE MODAL (Redesigned) --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="formCreateReport" action="{{ route('damage-lost.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Report Damage / Lost</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <!-- HEADER SECTION -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3">Report
                                    Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Document No.</label>
                                        <input type="text" class="form-control form-control-sm bg-light"
                                            value="(Auto Generated)" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Document Date</label>
                                        <input type="date" name="report_date" class="form-control form-control-sm"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Keeper Name</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ auth()->user()->name ?? 'Admin' }}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Type</label>
                                        <select name="type" id="newInputType" class="form-select form-select-sm" required>
                                            <option value="damage">Damage (Rusak)</option>
                                            <option value="lost">Lost (Hilang)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Employee Name</label>
                                        <select name="employee_id" class="form-select form-select-sm" required>
                                            <option value="">-- Select Employee --</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4" id="severityContainer">
                                        <label class="form-label small fw-bold text-muted">Severity</label>
                                        <select name="severity" id="inputSeverity" class="form-select form-select-sm"
                                            required>
                                            <option value="low">Low (Ringan)</option>
                                            <option value="medium">Medium (Sedang)</option>
                                            <option value="high">High (Berat)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Reason</label>
                                        <textarea name="description" class="form-control form-control-sm" rows="2"
                                            placeholder="Explain the reason..."></textarea>
                                    </div>
                                    {{-- Global Repairable Toggle (Applies to all damage items by default, or just hidden
                                    global setting) --}}
                                    <div class="col-12" id="globalRepairSection">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="isRepairableGlobal"
                                                name="is_repairable" value="1" checked>
                                            <label class="form-check-label small" for="isRepairableGlobal">Is Repairable?
                                                (Bisa Diperbaiki)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ITEM ENTRY SECTION -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3">Add Items</h6>
                                <div class="row g-2 align-items-end">
                                    {{-- Storage & Bin are Filters for the Tool Dropdown --}}
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted">Storage Location</label>
                                        <input type="text" id="entryStorage" class="form-control form-control-sm bg-light"
                                            placeholder="Auto-fill" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted">Bin Location</label>
                                        <input type="text" id="entryBin" class="form-control form-control-sm bg-light"
                                            placeholder="Auto-fill" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Tools Asset No.</label>
                                        <select id="entryToolSelect" class="form-select form-select-sm"
                                            style="width: 100%;">
                                            <option value="">-- Select Tool --</option>
                                            @foreach($tools as $item)
                                                @php
                                                    $isService = in_array($item->id, $openDamageItemIds ?? []);
                                                @endphp
                                                <option value="{{ $item->id }}" data-code="{{ $item->tool?->code ?? '' }}"
                                                    data-name="{{ $item->tool?->name ?? '' }}" data-serial="{{ $item->serial_number }}"
                                                    data-location="{{ $item->tool?->location?->name ?? '-' }}"
                                                    data-bin="{{ $item->tool?->rack?->code ?? '-' }}"
                                                    data-type="{{ $item->tool?->type?->name ?? '-' }}"
                                                    data-price="{{ $item->tool?->price ?? 0 }}" {{ $isService ? 'disabled' : '' }}>
                                                    {{ $item->serial_number }} - {{ $item->tool?->name ?? 'Deleted Asset' }}
                                                    {{ $isService ? ' - (SERVICE)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label small fw-bold text-muted">Qty</label>
                                        {{-- Serialized items usually Qty 1 --}}
                                        <input type="number" id="entryQty" class="form-control form-control-sm" value="1"
                                            min="1" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted">Notes</label>
                                        <input type="text" id="entryNotes" class="form-control form-control-sm"
                                            placeholder="Details...">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" id="btnAddItem" class="btn btn-primary btn-sm w-100 fw-bold">
                                            <i class="bi bi-plus-lg me-1"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PREVIEW TABLE -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-striped table-hover mb-0 small align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">No.</th>
                                            <th>Storage</th>
                                            <th>Bin</th>
                                            <th>Type</th>
                                            <th>Tools Asset No.</th>
                                            <th>Description</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-center">QTY</th>
                                            <th>Notes</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewTableBody">
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                Empty Table Data !!!
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- HIDDEN DATA INPUT -->
                        <input type="hidden" name="items_json" id="itemsJsonInput">
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary shadow-sm">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Keep internal legacy scripts minimal
    </script>
@endsection

@push('scripts')
    <!-- jQuery (Required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle Detail Buttons
            document.querySelectorAll('.toggle-detail').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.toggle-detail').forEach(b => {
                        if (b !== this) {
                            const t = document.getElementById(b.dataset.target);
                            t.classList.add('d-none');
                            b.querySelector('i').classList.remove('bi-chevron-up');
                            b.querySelector('i').classList.add('bi-chevron-down');
                        }
                    });
                    const target = document.getElementById(this.dataset.target);
                    target.classList.toggle('d-none');
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-chevron-down');
                    icon.classList.toggle('bi-chevron-up');
                });
            });

            // --- MASTER-DETAIL LOGIC ---
            let cartItems = [];

            // Init Select2 for Tool Entry
            if ($('#entryToolSelect').length) {
                const $toolSelect = $('#entryToolSelect');
                $toolSelect.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#createModal'),
                    width: '100%',
                    placeholder: '-- Select Tool --',
                    allowClear: true
                });

                // Auto-fill Logic
                $toolSelect.on('change', function () {
                    const $opt = $(this).find(':selected');
                    const loc = $opt.data('location') || '';
                    const bin = $opt.data('bin') || '';

                    $('#entryStorage').val(loc);
                    $('#entryBin').val(bin);
                });
            }

            // Function to Render Table
            function renderCartTable() {
                const tbody = document.getElementById('previewTableBody');
                tbody.innerHTML = '';

                if (cartItems.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-4">Empty Table Data !!!</td></tr>`;
                    return;
                }

                cartItems.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                                                                        <td class="ps-3 text-center">${index + 1}</td>
                                                                        <td class="small">${item.location}</td>
                                                                        <td class="small">${item.bin}</td>
                                                                        <td class="small">${item.type}</td>
                                                                        <td class="fw-bold small text-primary">${item.serial_number}</td>
                                                                        <td class="small">${item.name}</td>
                                                                        <td class="text-end small">${item.priceFormatted}</td>
                                                                        <td class="text-center">${item.quantity}</td>
                                                                        <td class="small fst-italic text-muted">${item.notes || '-'}</td>
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-index="${index}">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    `;
                    tbody.appendChild(tr);
                });

                // Attach Remove Events
                document.querySelectorAll('.btn-remove-item').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.dataset.index);
                        cartItems.splice(idx, 1);
                        renderCartTable();
                    });
                });
            }

            // Add Item Logic
            document.getElementById('btnAddItem').addEventListener('click', function (e) {
                e.preventDefault();

                const toolSelect = $('#entryToolSelect');
                const toolId = toolSelect.val();

                if (!toolId) {
                    alert('Please select a tool / asset first!');
                    return;
                }

                // Check dupe
                const isExist = cartItems.some(i => i.tool_item_id == toolId);
                if (isExist) {
                    alert('This item is already in the list!');
                    return;
                }

                // Get Data
                let opt = toolSelect.find(':selected');
                if (opt.length === 0) opt = toolSelect.find(`option[value="${toolId}"]`);

                const qty = parseInt(document.getElementById('entryQty').value) || 1;
                const notes = document.getElementById('entryNotes').value;

                // Format Price
                const rawPrice = opt.data('price') || 0;
                const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(rawPrice);

                const itemData = {
                    tool_item_id: toolId,
                    serial_number: opt.data('serial') || '-',
                    code: opt.data('code') || '-',
                    name: opt.data('name') || '-',
                    location: opt.data('location') || '-',
                    bin: opt.data('bin') || '-',
                    type: opt.data('type') || '-',
                    priceFormatted: priceFormatted,
                    quantity: qty,
                    notes: notes
                };

                cartItems.push(itemData);
                renderCartTable();

                // Reset Inputs
                toolSelect.val(null).trigger('change');
                document.getElementById('entryNotes').value = '';
            });

            // Submit Handler
            document.getElementById('formCreateReport').addEventListener('submit', function (e) {
                if (cartItems.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the table data!');
                    return;
                }
                document.getElementById('itemsJsonInput').value = JSON.stringify(cartItems);
            });

            // Toggle Repairable for Global
            const inputType = document.getElementById('newInputType');
            const repairSection = document.getElementById('globalRepairSection');
            const severityContainer = document.getElementById('severityContainer');
            const inputSeverity = document.getElementById('inputSeverity');

            function toggleFields() {
                const isDamage = inputType.value === 'damage';

                if (repairSection) repairSection.style.display = isDamage ? 'block' : 'none';

                if (severityContainer) {
                    severityContainer.style.display = isDamage ? 'block' : 'none';
                    if (inputSeverity) {
                        if (isDamage) {
                            inputSeverity.setAttribute('required', 'required');
                        } else {
                            inputSeverity.removeAttribute('required');
                            inputSeverity.value = 'high'; // Default high for lost (backend handles it too)
                        }
                    }
                }
            }

            if (inputType) {
                inputType.addEventListener('change', toggleFields);
                toggleFields(); // Initial run
            }

            // --- VIEW / RESOLVE LEGACY LOGIC ---
            window.showViewModal = function (code, date, emp, tool, desc, status, evidence) {
                document.getElementById('viewCode').innerText = code;
                document.getElementById('viewDate').innerText = date;
                document.getElementById('viewEmployee').innerText = emp;
                document.getElementById('viewTool').innerText = tool;
                document.getElementById('viewDescription').innerText = desc || '-';
                document.getElementById('viewStatus').innerText = status;

                const evSection = document.getElementById('viewEvidenceSection');
                const evImg = document.getElementById('viewEvidenceImg');
                const evLink = document.getElementById('viewEvidenceLink');

                if (evidence) {
                    evSection.classList.remove('d-none');
                    if (evidence.toLowerCase().endsWith('.pdf')) {
                        evImg.src = 'https://via.placeholder.com/300x150?text=PDF+Document';
                    } else {
                        evImg.src = evidence;
                    }
                    evLink.href = evidence;
                } else {
                    evSection.classList.add('d-none');
                }
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            };

            window.confirmResolve = function (id, code) {
                const form = document.getElementById('formResolve');
                if (form) {
                    form.action = `/damage-lost/${id}/resolve`;
                    const msgEl = document.getElementById('resolveMessageBody');
                    if (msgEl) msgEl.innerHTML = `Apakah barang pada laporan <strong>${code}</strong> sudah selesai diperbaiki dan siap masuk stock kembali?`;
                    new bootstrap.Modal(document.getElementById('resolveModal')).show();
                }
            };
        });
    </script>
@endpush