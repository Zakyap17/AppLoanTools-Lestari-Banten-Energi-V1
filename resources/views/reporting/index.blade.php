@extends('layouts.app')

@section('title', 'Reporting Center')

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
        /* Modern Design System - Dark Navy Theme */
        :root {
            --primary-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Page Header Modern */
        .page-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: var(--card-shadow);
            /* Box Design applied */
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

        /* Modern Filter Panel */
        .reporting-filter {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            position: relative;
        }

        .filter-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .filter-header i {
            font-size: 1.25rem;
            color: #3b82f6;
            background: #eff6ff;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .filter-header h6 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        .filter-group label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-color: #cbd5e1;
            padding: 0.6rem 1rem;
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        .filter-actions .btn {
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            border-radius: 50px;
            /* Pill buttons */
            transition: all 0.2s;
        }

        .filter-actions .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .filter-actions .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
        }

        .filter-actions .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2);
        }

        .filter-actions .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(5, 150, 105, 0.3);
        }

        /* Modern Table Card */
        .report-table-card {
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .report-table-card .card-body {
            padding: 0;
        }

        .modern-table {
            margin-bottom: 0;
        }

        .modern-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .modern-table thead th {
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.25rem 1rem;
            border: none;
        }

        .modern-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
            background: white;
        }

        .modern-table tbody tr:last-child {
            border-bottom: none;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .modern-table tbody td {
            padding: 1rem 1rem;
            vertical-align: middle;
            font-size: 0.875rem;
            color: #334155;
        }

        /* Empty State */
        .empty-state-table {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }

        .empty-state-table i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            display: block;
        }

        .empty-state-table h5 {
            color: #64748b;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        /* Badge Improvements */
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            display: inline-block;
        }
        }
    </style>

    {{-- HEADER --}}
    <div class="page-header-modern d-flex justify-content-between align-items-center position-relative">
        <div class="position-relative z-1">
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Analytics & Overview
            </h6>
            <h3 class="fw-bold mb-0">Reporting Center</h3>
            <p class="text-white-50 mb-0 small mt-1">Generate and export reports for tool activities</p>
        </div>

    </div>

    {{-- FILTER PANEL --}}
    <div class="reporting-filter">
        <div class="filter-header">
            <i class="bi bi-funnel-fill"></i>
            <h6>Filter Reports</h6>
        </div>

        <form action="{{ route('reporting-center') }}" method="GET">
            <div class="row g-3">
                {{-- REPORT TYPE --}}
                <div class="col-md-4 filter-group">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" id="reportTypeSelect" class="form-select">
                        <option value="">-- Select Report --</option>
                        <option value="Stock Opname" {{ request('report_type') == 'Stock Opname' ? 'selected' : '' }}>Stock
                            Opname</option>
                        <option value="Loan" {{ request('report_type') == 'Loan' ? 'selected' : '' }}>Loan</option>
                        <option value="Return / Damage" {{ request('report_type') == 'Return / Damage' ? 'selected' : '' }}>
                            Return / Damage Lost</option>
                        <option value="Tools Transaction" {{ request('report_type') == 'Tools Transaction' ? 'selected' : '' }}>Tools Transaction</option>
                        <option value="Calibration" {{ request('report_type') == 'Calibration' ? 'selected' : '' }}>
                            Calibration</option>
                        <option value="Mutation" {{ request('report_type') == 'Mutation' ? 'selected' : '' }}>Mutation
                        </option>
                    </select>
                </div>

                {{-- DATE FROM --}}
                <div class="col-md-4 filter-group">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                {{-- DATE TO --}}
                <div class="col-md-4 filter-group">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                {{-- STORAGE --}}
                <div class="col-md-4 filter-group">
                    <label class="form-label">Storage Location</label>
                    <select class="form-select" name="location">
                        <option value="">All</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BIN --}}
                <div class="col-md-4 filter-group">
                    <label class="form-label">Bin Location</label>
                    <select class="form-select" name="rack" disabled>
                        <option value="">All</option>
                    </select>
                </div>

                {{-- TOOLS ASSET NO (Select2) --}}
                <div class="col-md-4 filter-group">
                    <label class="form-label">Tools Asset No</label>
                    <select class="form-select" name="tool_id" id="toolSelect" style="width: 100%;">
                        <option value="">All</option>
                        @if(isset($selectedTool))
                            <option value="{{ $selectedTool->id }}" selected>
                                {{ $selectedTool->code }} - {{ $selectedTool->name }}
                            </option>
                        @endif
                    </select>
                </div>

                {{-- EMPLOYEE --}}
                <div class="col-md-6 filter-group">
                    <label class="form-label">Employee</label>
                    <select class="form-select" name="employee">
                        <option value="">All</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->nik }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- STATUS --}}
                <div class="col-md-6 filter-group">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Returned" {{ request('status') == 'Returned' ? 'selected' : '' }}>Returned</option>
                        <option value="Damaged" {{ request('status') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="filter-actions">
                <button type="button" id="resetBtn" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-2"></i>Search
                </button>
                <button type="button" id="exportBtn" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                </button>
            </div>
        </form>
    </div>

    {{-- RESULT TABLES --}}

    {{-- STOCK OPNAME TABLE --}}
    <div id="table-stock-opname" class="card report-table-card"
        style="display: {{ request('report_type') == 'Stock Opname' ? 'block' : 'none' }};">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Storage</th>
                            <th>Bin</th>
                            <th>Tool No.</th>
                            <th>Description</th>
                            <th>Asset No (Serial)</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(request('report_type') == 'Stock Opname')
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->tool->location->name ?? '-' }}</td>
                                    <td>{{ $item->tool->rack->code ?? '-' }}</td>
                                    <td class="fw-bold text-primary">{{ $item->tool->code ?? '-' }}</td>
                                    <td>{{ $item->tool->name ?? '-' }}</td>
                                    <td>{{ $item->serial_number ?? '-' }}</td>
                                    <td class="text-center">{{ $item->tool->uom ?? '-' }}</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">
                                        @php
                                            $statusLabel = ucfirst(str_replace('_', ' ', $item->status));
                                            $badgeClass = $item->status === 'available' ? 'bg-success' : ($item->status === 'in_use' ? 'bg-primary' : ($item->status === 'lost' ? 'bg-secondary' : 'bg-danger'));

                                            // Check for In Service status
                                            if ($item->status === 'damage' && $item->activeDamageReport && $item->activeDamageReport->is_repairable) {
                                                $statusLabel = 'In Service';
                                                $badgeClass = 'bg-warning text-dark';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="empty-state-table">
                                            <i class="bi bi-inbox"></i>
                                            <h5>No Data Found</h5>
                                            <p class="small">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state-table">
                                        <i class="bi bi-filter-circle"></i>
                                        <h5>Select Report Type</h5>
                                        <p class="small">Choose a report type from the filter above</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- LOAN TABLE --}}
    <div id="table-loan" class="card report-table-card"
        style="display: {{ request('report_type') == 'Loan' ? 'block' : 'none' }};">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>No.</th>
                            <th>Document No.</th>
                            <th>Date</th>
                            <th>Borrower Name</th>
                            <th class="text-center">Total Tools</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="accordion" id="loanAccordion">
                        @if(request('report_type') == 'Loan')
                            @forelse($data as $index => $loanCodeRec)
                                @php
                                    $loanGroup = $allLoans[$loanCodeRec->loan_code] ?? collect();
                                    $firstLoan = $loanGroup->first();
                                    
                                    if (!$firstLoan) continue; // Safety check
                                    
                                    $totalQty = $loanGroup->sum('loan_quantity');
                                @endphp
                                
                                <tr data-bs-toggle="collapse" data-bs-target="#collapseLoan{{ $index }}"
                                    aria-expanded="false" aria-controls="collapseLoan{{ $index }}"
                                    style="cursor: pointer;">
                                    <td class="text-center">
                                        <i class="bi bi-chevron-down text-primary" style="transition: transform 0.3s;"></i>
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold text-primary">{{ $firstLoan->loan_code }}</td>
                                    <td>{{ \Carbon\Carbon::parse($firstLoan->loan_date)->format('d M Y') }}</td>
                                    <td>{{ $firstLoan->borrower->name ?? '-' }}</td>
                                    <td class="text-center fw-bold">{{ $totalQty }} Items</td>
                                    <td class="text-center">
                                        <a href="{{ route('loan.print', $firstLoan->loan_code) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary" title="View/Print Document">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- EXPANDED DETAILS -->
                                <tr>
                                    <td colspan="7" class="p-0 border-0">
                                        <div id="collapseLoan{{ $index }}" class="accordion-collapse collapse"
                                            data-bs-parent="#loanAccordion">
                                            <div class="p-4 bg-light">
                                                <h6 class="mb-3 fw-bold text-muted small text-uppercase">Items in this Document:</h6>
                                                <table class="table table-sm table-bordered bg-white mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="small text-muted">Storage</th>
                                                            <th class="small text-muted">Bin</th>
                                                            <th class="small text-muted">Tool No.</th>
                                                            <th class="small text-muted">Description</th>
                                                            <th class="small text-muted">Asset No (Serial)</th>
                                                            <th class="small text-muted text-center">UOM</th>
                                                            <th class="small text-muted text-center">Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($loanGroup as $item)
                                                            <tr>
                                                                <td class="small">{{ $item->tool?->location?->name ?? '-' }}</td>
                                                                <td class="small">{{ $item->tool?->rack?->code ?? '-' }}</td>
                                                                <td class="small text-primary fw-bold">{{ $item->tool?->code ?? '-' }}</td>
                                                                <td class="small">{{ $item->tool?->name ?? '-' }}</td>
                                                                <td class="small">{{ $item->item?->serial_number ?? '-' }}</td>
                                                                <td class="small text-center">{{ $item->tool?->uom ?? '-' }}</td>
                                                                <td class="small text-center fw-bold">{{ $item->loan_quantity }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state-table">
                                            <i class="bi bi-inbox"></i>
                                            <h5>No Loan History Found</h5>
                                            <p class="small">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state-table">
                                        <i class="bi bi-filter-circle"></i>
                                        <h5>Select Report Type</h5>
                                        <p class="small">Choose a report type from the filter above</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RETURN/DAMAGE TABLE --}}
    <div id="table-return-damage" class="card report-table-card"
        style="display: {{ request('report_type') == 'Return / Damage' ? 'block' : 'none' }};">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Document No.</th>
                            <th>Tools Room</th>
                            <th>Bin Location</th>
                            <th>Item Code</th>
                            <th>Asset No.</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Employee Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(request('report_type') == 'Return / Damage')
                            @forelse($data as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $report->report_code }}</td>
                                    <td>{{ $report->tool->location->name ?? '-' }}</td>
                                    <td>{{ $report->tool->rack->code ?? '-' }}</td>
                                    <td>{{ $report->tool->code ?? '-' }}</td>
                                    <td class="fw-bold text-primary">{{ $report->tool->code ?? '-' }}</td>
                                    <td>{{ $report->tool->name ?? '-' }}</td>
                                    <td class="text-center">{{ $report->quantity }}</td>
                                    <td>{{ $report->employee->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @php
                                            $displayStatus = ucfirst($report->status);
                                            $badgeClass = $report->status === 'open' ? 'bg-info' : 'bg-success';

                                            if ($report->status === 'open' && $report->type === 'damage') {
                                                if ($report->is_repairable) {
                                                    $displayStatus = 'In Service';
                                                    $badgeClass = 'bg-warning text-dark';
                                                } else {
                                                    $displayStatus = 'Unrepairable'; // or 'Pending Disposal'
                                                    $badgeClass = 'bg-danger';
                                                }
                                            } elseif ($report->type === 'lost') {
                                                $displayStatus = 'Lost';
                                                $badgeClass = 'bg-secondary';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $badgeClass }}">
                                            {{ $displayStatus }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="empty-state-table">
                                            <i class="bi bi-inbox"></i>
                                            <h5>No Damage/Lost Reports Found</h5>
                                            <p class="small">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state-table">
                                        <i class="bi bi-filter-circle"></i>
                                        <h5>Select Report Type</h5>
                                        <p class="small">Choose a report type from the filter above</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MUTATION TABLE --}}
    <div id="table-mutation" class="card report-table-card"
        style="display: {{ request('report_type') == 'Mutation' ? 'block' : 'none' }};">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Mutation From</th>
                            <th>Mutation To</th>
                            <th>Item Code</th>
                            <th>Asset No.</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Keeper Name</th>
                            <th>Notes</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(request('report_type') == 'Mutation')
                            @forelse($data as $index => $log)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $log->fromLocation->name ?? '-' }}</span>
                                        <div class="small text-muted">{{ $log->fromRack->code ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $log->toLocation->name ?? '-' }}</span>
                                        <div class="small text-muted">{{ $log->toRack->code ?? '-' }}</div>
                                    </td>
                                    <td>{{ $log->tool->code ?? '-' }}</td>
                                    <td class="fw-bold text-primary">{{ $log->tool->code ?? '-' }}</td>
                                    <td>{{ $log->tool->name ?? '-' }}</td>
                                    <td class="text-center">{{ $log->tool->quantity ?? 1 }}</td>
                                    <td>{{ $log->user->name ?? '-' }}</td>
                                    <td>{{ $log->reason }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('mutation.document', $log->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary" title="View Document">
                                            <i class="bi bi-file-text"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="empty-state-table">
                                            <i class="bi bi-inbox"></i>
                                            <h5>No Mutation History Found</h5>
                                            <p class="small">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state-table">
                                        <i class="bi bi-filter-circle"></i>
                                        <h5>Select Report Type</h5>
                                        <p class="small">Choose a report type from the filter above</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- OTHER TABLES (Receive, Calibration) - Placeholder --}}
    {{-- TOOLS TRANSACTION TABLE --}}
    <div id="table-receive" class="card report-table-card"
        style="display: {{ request('report_type') == 'Tools Transaction' ? 'block' : 'none' }};">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>No.</th>
                            <th>Entry Date</th>
                            <th>Storage</th>
                            <th>Bin</th>
                            <th>Tool No.</th>
                            <th>Description</th>
                            <th>Asset No (Serial)</th>
                            <th>Condition</th>
                            <th>Qty</th>
                            <th>Entered By</th>
                            <th>Deleted By</th>
                        </tr>
                    </thead>
                    <tbody class="accordion" id="transactionAccordion">
                        @if(request('report_type') == 'Tools Transaction')
                            @forelse($data as $index => $item)
                                @php
                                    $latestBorrow = $item->loans->whereIn('status', ['returned', 'returned_damage', 'returned_lost'])->sortByDesc('loan_date')->first();
                                    $latestActiveBorrow = $item->loans->where('status', 'ongoing')->first();

                                    // If there is an active borrow, it is the most recent borrow action
                                    $recentBorrowDate = $latestActiveBorrow ? $latestActiveBorrow->loan_date : ($latestBorrow ? $latestBorrow->loan_date : null);

                                    // Latest return is the most recent actual_return_date from history
                                    $recentReturnDate = $latestBorrow ? $latestBorrow->actual_return_date : null;
                                @endphp

                                <!-- Main Row -->
                                <tr data-bs-toggle="collapse" data-bs-target="#collapseTransaction{{ $item->id }}"
                                    aria-expanded="false" aria-controls="collapseTransaction{{ $item->id }}"
                                    style="cursor: pointer;">
                                    <td class="text-center">
                                        <i class="bi bi-chevron-down text-primary"
                                            style="transition: transform 0.3s; display: inline-block;"></i>
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                                    <td>{{ $item->tool?->location?->name ?? '-' }}</td>
                                    <td>{{ $item->tool?->rack?->code ?? '-' }}</td>
                                    <td>
                                        <span class="text-primary fw-bold {{ $item->trashed() ? 'text-decoration-line-through text-danger' : '' }}">
                                            {{ $item->tool?->code ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $item->trashed() ? 'text-decoration-line-through text-danger' : '' }}">
                                            {{ $item->tool?->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $item->trashed() ? 'text-decoration-line-through text-danger' : '' }}">
                                            {{ $item->serial_number ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ strtolower($item->condition) == 'good' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $item->condition ?? 'Good' }}
                                        </span>
                                    </td>
                                    <td class="text-center">1</td>
                                    <td>{{ $item->createdBy?->name ?? '-' }}</td>
                                    <td>
                                        @if($item->trashed())
                                            <span class="text-danger fw-bold"><i class="bi bi-person-x"></i> {{ $item->deletedBy->name ?? 'Unknown' }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Expandable Timeline Row -->
                                <tr>
                                    <td colspan="12" class="p-0 border-0">
                                        <div id="collapseTransaction{{ $item->id }}" class="accordion-collapse collapse"
                                            data-bs-parent="#transactionAccordion">
                                            <div class="px-4 py-3 bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-4 pe-4 border-end border-2 border-secondary">
                                                        <h6 class="text-muted mb-1 fs-xs text-uppercase fw-bold"><i
                                                                class="bi bi-box-arrow-in-right me-1"></i> First Entry into System
                                                        </h6>
                                                        <div class="fs-6 fw-bold text-dark">
                                                            {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : 'Unknown' }}
                                                        </div>
                                                    </div>
                                                    <div class="me-4 pe-4 border-end border-2 border-primary">
                                                        <h6 class="text-primary mb-1 fs-xs text-uppercase fw-bold"><i
                                                                class="bi bi-person-up me-1"></i> Last Borrowed</h6>
                                                        <div class="fs-6 fw-bold text-dark">
                                                            @if($recentBorrowDate)
                                                                {{ \Carbon\Carbon::parse($recentBorrowDate)->format('d M Y, H:i') }}
                                                                <br><small class="text-muted fw-normal">by {{ $latestActiveBorrow ? $latestActiveBorrow->borrower?->name : ($latestBorrow ? $latestBorrow->borrower?->name : '-') }}</small>
                                                            @else
                                                                Never Borrowed
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-success mb-1 fs-xs text-uppercase fw-bold"><i
                                                                class="bi bi-person-down me-1"></i> Last Returned</h6>
                                                        <div class="fs-6 fw-bold text-dark">
                                                            @if($recentReturnDate)
                                                                {{ \Carbon\Carbon::parse($recentReturnDate)->format('d M Y, H:i') }}
                                                                <br><small class="text-muted fw-normal">by {{ $latestBorrow ? $latestBorrow->borrower?->name : '-' }}</small>
                                                            @else
                                                                {{ $latestActiveBorrow ? 'Currently Borrowed' : 'Never Returned' }}
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if($item->trashed())
                                                    <div class="ms-4 ps-4 border-start border-2 border-danger">
                                                        <h6 class="text-danger mb-1 fs-xs text-uppercase fw-bold"><i
                                                                class="bi bi-trash me-1"></i> Removed from System</h6>
                                                        <div class="fs-6 fw-bold text-dark">
                                                            {{ $item->deleted_at ? $item->deleted_at->format('d M Y, H:i') : 'Unknown' }}
                                                            <br><small class="text-muted fw-normal">by {{ $item->deletedBy->name ?? 'Unknown' }}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">
                                        <div class="empty-state-table">
                                            <i class="bi bi-inbox"></i>
                                            <h5>No Transaction History Found</h5>
                                            <p class="small">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state-table">
                                        <i class="bi bi-filter-circle"></i>
                                        <h5>Select Report Type</h5>
                                        <p class="small">Choose a report type from the filter above</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="table-calibration" class="card report-table-card"
        style="display: {{ request('report_type') == 'Calibration' ? 'block' : 'none' }};">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Issue Code</th>
                            <th>Issue Date</th>
                            <th>Tool Code</th>
                            <th>Description</th>
                            <th>Vendor</th>
                            <th>Exp. Completion</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(request('report_type') == 'Calibration')
                            @forelse($data as $index => $cal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $cal->issue_code }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cal->issue_date)->format('d M Y') }}</td>
                                    <td class="text-primary fw-bold">{{ $cal->tool->code ?? '-' }}</td>
                                    <td>{{ $cal->tool->name ?? '-' }}</td>
                                    <td>{{ $cal->vendor->name ?? '-' }}</td>
                                    <td>{{ $cal->expected_completion_date ? \Carbon\Carbon::parse($cal->expected_completion_date)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="status-badge {{ $cal->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}"
                                            style="color: {{ $cal->status === 'completed' ? 'white' : 'black' }} !important;">
                                            {{ ucfirst(str_replace('_', ' ', $cal->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state-table">
                                            <i class="bi bi-inbox"></i>
                                            <h5>No Calibration History Found</h5>
                                            <p class="small">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state-table">
                                        <i class="bi bi-filter-circle"></i>
                                        <h5>Select Report Type</h5>
                                        <p class="small">Choose a report type from the filter above</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        @if($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $data->onEachSide(1)->links('pagination::bootstrap-5') }}
        @endif
    </div>

@endsection

@push('scripts')
    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Function to initialize Select2
            function initToolSelect() {
                $('#toolSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Type at least 3 chars to search...',
                    allowClear: true,
                    minimumInputLength: 3, // Prevent searching for 1-2 chars which is slow
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    language: {
                        inputTooShort: function () {
                            return "Please enter 3 or more characters";
                        },
                        searching: function () {
                            return '<div class="text-center p-2"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Searching...</div>';
                        },
                        noResults: function () {
                            return '<div class="text-center p-2"><i class="bi bi-x-circle text-danger me-2"></i>No assets found</div>';
                        }
                    },
                    ajax: {
                        url: "{{ route('loan.search-asset') }}",
                        dataType: 'json',
                        delay: 250, // Reduced delay for faster response
                        data: function (params) {
                            console.log('Searching for:', params.term);
                            return { q: params.term, include_trashed: 1 };
                        },
                        processResults: function (data) {
                            console.log('Search results received:', data);
                            return {
                                results: $.map(data.results, function (item) {
                                    return {
                                        id: item.id,
                                        text: item.code + ' - ' + item.name + (item.items && item.items.length > 0 ? ' (Serial: ' + item.items[0].serial_number + ')' : '')
                                    }
                                })
                            };
                        },
                        cache: true
                    }

                });
            }

            initToolSelect();
        });

        // Mapping report type to table ID
        const reportTypeTableMap = {
            'Stock Opname': 'table-stock-opname',
            'Loan': 'table-loan',
            'Return / Damage': 'table-return-damage',
            'Tools Transaction': 'table-receive',
            'Calibration': 'table-calibration',
            'Mutation': 'table-mutation'
        };

        const allTableIds = Object.values(reportTypeTableMap);

        function hideAllTables() {
            allTableIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
        }

        // Show/hide table based on report type selection
        document.getElementById('reportTypeSelect').addEventListener('change', function () {
            // Auto submit form to fetch data
            if (this.value) {
                this.form.submit();
            }
        });

        // Reset button
        document.getElementById('resetBtn').addEventListener('click', function () {
            window.location.href = "{{ route('reporting-center') }}";
        });

        // Export Excel button
        document.getElementById('exportBtn').addEventListener('click', function () {
            // Get current URL search params
            const urlParams = new URLSearchParams(window.location.search);

            // Check if report type is selected
            const reportType = urlParams.get('report_type');
            if (!reportType) {
                alert('Please select a report type first');
                return;
            }

            // Build export URL with same parameters
            const exportUrl = "{{ route('reporting-center.export') }}" + '?' + urlParams.toString();

            // Redirect to export
            window.location.href = exportUrl;
        });

        // Dynamic Bin Dropdown
        const locationSelect = document.querySelector('select[name="location"]');
        const rackSelect = document.querySelector('select[name="rack"]');

        if (locationSelect) {
            locationSelect.addEventListener('change', function () {
                const locationId = this.value;
                rackSelect.innerHTML = '<option value="" selected>Loading...</option>';
                rackSelect.disabled = true;

                if (locationId) {
                    fetch(`{{ url('/tools/get-bins') }}/${locationId}`)
                        .then(response => response.json())
                        .then(data => {
                            rackSelect.innerHTML = '<option value="">All</option>';
                            data.forEach(bin => {
                                const option = document.createElement('option');
                                option.value = bin.id;
                                option.text = bin.code + (bin.description ? ` - ${bin.description}` : '');
                                if (String(bin.id) === "{{ request('rack') }}") {
                                    option.selected = true;
                                }
                                rackSelect.appendChild(option);
                            });
                            rackSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching bins:', error);
                            rackSelect.innerHTML = '<option value="">Error loading bins</option>';
                        });
                } else {
                    rackSelect.innerHTML = '<option value="">All</option>';
                    rackSelect.disabled = true;
                }
            });

            // Trigger on load if location is selected
            if (locationSelect.value) {
                locationSelect.dispatchEvent(new Event('change'));
            }
        }
    </script>
@endpush