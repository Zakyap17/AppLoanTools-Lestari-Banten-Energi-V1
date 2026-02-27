@extends('layouts.app')

@section('title', 'Loan')

@section('full_width_header')
    @include('partials.topbar')
@endsection

@section('content')

    <style>
        .page-header-modern {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 1.5rem; /* Reduced from 3rem */
            border-radius: 20px; /* Rounded corners */
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
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Tools Transaction
            </h6>
            <h3 class="fw-bold mb-0">Loan</h3>
            <p class="text-white-50 mb-0 small mt-1">Manage tool borrowing and return process</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            <form action="{{ route('loan.index') }}" method="GET" class="position-relative mb-0">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" class="search-input-modern ps-5" placeholder="Search Doc No, Name..."
                    value="{{ request('search') }}">
            </form>
            <a href="{{ route('loan.create') }}" class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> New Loan
            </a>
        </div>
    </div>

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 border-start border-4 border-warning">
                <div class="card-body position-relative bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3 text-warning d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-0"
                                style="font-size: 0.7rem; letter-spacing: 0.5px;">Active Documents</h6>
                            <h2 class="fw-bold mb-0 text-dark">{{ $activeLoanDocs }}</h2>
                        </div>
                    </div>
                    <small class="text-muted ps-1">Loans In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 border-start border-4 border-primary">
                <div class="card-body position-relative bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-tools fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-0"
                                style="font-size: 0.7rem; letter-spacing: 0.5px;">Items Borrowed</h6>
                            <h2 class="fw-bold mb-0 text-dark">{{ $itemsOnLoan }}</h2>
                        </div>
                    </div>
                    <small class="text-muted ps-1">Total quantity out</small>
                </div>
            </div>
        </div>
    </div>

    <div class="modern-table-container">
        <div class="table-responsive">
            {{-- TABLE --}}
            <table class="table table-custom align-middle w-100 mb-0">
                <thead class="text-center">
                    <tr>
                        <th style="width:40px" class="text-center">#</th>
                        <th class="text-center">Document No.</th>
                        <th class="text-center">Document Date</th>
                        <th class="text-center">Employee Name</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Returned At</th>
                        <th style="width:140px" class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($loans as $codeObj)
                        @php
                            $loanGroup = $allLoans[$codeObj->loan_code] ?? collect();
                            $firstLoan = $loanGroup->first();
                            if (!$firstLoan)
                                continue;

                            // Status Aggregate
                            $hasOngoing = $loanGroup->contains('status', 'ongoing');
                            $hasDamage = $loanGroup->contains('status', 'returned_damage');
                            $hasLost = $loanGroup->contains('status', 'returned_lost');

                            if ($hasOngoing) {
                                $groupStatus = 'ongoing';
                                // Check overdue
                                if ($firstLoan->expected_return_date && now() > $firstLoan->expected_return_date) {
                                    $groupStatus = 'overdue';
                                }
                            } else {
                                if ($hasLost) {
                                    $groupStatus = 'Returned (Lost Items)';
                                } elseif ($hasDamage) {
                                    $groupStatus = 'Returned In Service';
                                } else {
                                    $groupStatus = 'Returned';
                                }
                            }

                            $badgeClass = 'bg-secondary-subtle text-secondary';
                            if ($groupStatus == 'ongoing') {
                                $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                            } elseif ($groupStatus == 'overdue') {
                                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                            } elseif ($groupStatus == 'Returned') {
                                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                            } elseif ($groupStatus == 'Returned (Lost Items)') {
                                $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                            } else {
                                // Returned with issues (Damage)
                                $badgeClass = 'bg-warning-subtle text-danger border border-warning-subtle';
                            }
                        @endphp

                        <tr>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border rounded-circle toggle-detail {{ request('expanded') == $firstLoan->loan_code ? 'active' : '' }}"
                                    data-target="loan-detail-{{ $firstLoan->id }}" title="Show Items"
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi {{ request('expanded') == $firstLoan->loan_code ? 'bi-chevron-up' : 'bi-chevron-down' }}"></i>
                                </button>
                            </td>
                            <td class="fw-bold text-primary text-center">{{ $firstLoan->loan_code }}</td>
                            <td class="text-center">{{ $firstLoan->loan_date->format('d M Y') }}</td>
                            <td class="text-center">{{ $firstLoan->borrower->name ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 {{ $badgeClass }}">
                                    {{ ucfirst($groupStatus) }}
                                </span>
                            </td>
                            <td class="text-center text-muted small">
                                @if(in_array($groupStatus, ['Returned', 'Returned In Service', 'Returned (Lost Items)']))
                                    @php
                                        $latestReturn = $loanGroup->max('actual_return_date');
                                    @endphp
                                    {{ $latestReturn ? \Carbon\Carbon::parse($latestReturn)->format('l, d M Y H:i') : '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('loan.print', $firstLoan->loan_code) }}" target="_blank"
                                    class="btn btn-sm btn-light border rounded-circle shadow-sm p-2 text-primary"
                                    title="View Document"
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                            </td>
                        </tr>

                        {{-- EXPAND DETAIL ROW --}}
                        <tr id="loan-detail-{{ $firstLoan->id }}" class="{{ request('expanded') == $firstLoan->loan_code ? '' : 'd-none' }} bg-light">
                            <td colspan="7" class="p-4 bg-light border-bottom">
                                <div class="bg-white rounded-3 shadow-sm border p-3">
                                    <h6 class="fw-bold small text-uppercase text-muted mb-3"><i
                                            class="bi bi-list-check me-2"></i> Borrowed Items</h6>
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="text-secondary" style="font-size: 0.8rem;">
                                            <tr>
                                                <th style="width:40px" class="text-center">No.</th>
                                                <th>Asset No.</th>
                                                <th>Item Name</th>
                                                <th>Storage / Bin</th>
                                                <th style="width:80px" class="text-center">QTY</th>
                                                <th class="text-center">Returned At</th>
                                                <th class="text-center">Status</th>
                                                <th style="width:140px" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 0.85rem;">
                                            @foreach($loanGroup as $index => $item)
                                                @php
                                                    // Calculate max returnable for this tool in this loan group
                                                    $maxReturnable = $loanGroup->where('tool_id', $item->tool_id)->where('status', 'ongoing')->count();
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="fw-bold text-dark">
                                                        {{ $item->item ? $item->item->serial_number : $item->tool->code }}
                                                    </td>
                                                    <td>{{ $item->tool->name }}</td>
                                                    <td class="text-muted">
                                                        {{ $item->tool->location->name ?? '-' }} <i
                                                            class="bi bi-chevron-right extra-small mx-1"></i>
                                                        {{ $item->tool->rack->code ?? '-' }}
                                                    </td>
                                                    <td class="text-center fw-bold">{{ $item->loan_quantity }}</td>
                                                    <td class="text-center text-muted small">
                                                        @if(in_array($item->status, ['returned', 'returned_damage', 'returned_lost', 'closed_with_issue']))
                                                            {{ $item->actual_return_date ? \Carbon\Carbon::parse($item->actual_return_date)->format('d M Y H:i') : '-' }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $statusClass = 'bg-secondary-subtle text-secondary';
                                                            $statusLabel = ucfirst($item->status);
                                                            
                                                            if ($item->status == 'ongoing') {
                                                                $statusClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                                            } elseif ($item->status == 'returned') {
                                                                $statusClass = 'bg-success-subtle text-success border border-success-subtle';
                                                            } elseif ($item->status == 'returned_damage' || $item->status == 'closed_with_issue') {
                                                                $statusClass = 'bg-warning-subtle text-danger border border-warning-subtle';
                                                                $statusLabel = 'Returned In Service'; 
                                                            } elseif ($item->status == 'returned_lost') {
                                                                $statusClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                                                $statusLabel = 'Returned (Lost)';
                                                            }
                                                        @endphp
                                                        <span class="badge rounded-pill {{ $statusClass }} small px-2">
                                                            {{ $statusLabel }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->status == 'ongoing')
                                                            <div class="dropdown">
                                                                <button
                                                                    class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-3 py-1"
                                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                                    style="font-size: 0.75rem;">
                                                                    Return
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                                                    <li>
                                                                        <button class="dropdown-item text-success small py-2"
                                                                            onclick="processReturn({{ $item->id }}, 'good', {{ $item->loan_quantity }})">
                                                                            <i class="bi bi-check-circle me-2"></i> Return Good
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider my-0">
                                                                    </li>
                                                                    <li>
                                                                        <button class="dropdown-item text-warning small py-2"
                                                                            onclick="processReturn({{ $item->id }}, 'damage', {{ $item->loan_quantity }})">
                                                                            <i class="bi bi-exclamation-triangle me-2"></i> Report
                                                                            Damage
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button class="dropdown-item text-danger small py-2"
                                                                            onclick="processReturn({{ $item->id }}, 'lost', {{ $item->loan_quantity }})">
                                                                            <i class="bi bi-x-circle me-2"></i> Report Lost
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <span class="text-success small fw-bold"><i
                                                                    class="bi bi-check-all me-1"></i> Returned</span>
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
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox display-1 text-light d-block mb-3"></i>
                                No loan transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-top bg-light bg-opacity-25 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Showing {{ $loans->firstItem() ?? 0 }} to {{ $loans->lastItem() ?? 0 }} of {{ $loans->total() }} entries
            </div>
            <div>
                {{ $loans->links() }}
            </div>
        </div>
    </div>

    <form id="formReturn" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="return_type" id="returnType">
        <input type="hidden" name="reason" id="returnReason">
        <input type="hidden" name="return_quantity" id="returnQty">
        {{-- Preserve State --}}
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="page" value="{{ request('page') }}">
    </form>

    {{-- MODAL CONFIRMATION --}}
    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="returnModalTitle">Return Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <div class="mb-3">
                        <i id="returnIcon" class="bi bi-question-circle text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-semibold mb-2" id="returnLeadText">Process this return?</h6>
                    <p class="text-muted small mb-0" id="returnMessageBody">
                        Are you sure you want to mark this item as returned?
                    </p>
                    <div id="quantityContainer" class="mt-3 text-start d-none">
                                                                <input type="hidden" id="modalQty">
                                                            </div>
                    <div id="reasonContainer" class="mt-3 text-start d-none">
                        <label class="form-label small fw-bold">Reason / Notes</label>
                        <textarea id="modalReason" class="form-control form-control-sm" rows="3"
                            placeholder="Explain why it's damaged or lost..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btnConfirmReturn" class="btn btn-primary px-4 shadow-sm">
                        Confirm Return
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TOGGLE SCRIPT --}}
    <script>
        document.querySelectorAll('.toggle-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                target.classList.toggle('d-none');

                this.classList.toggle('active');

                const icon = this.querySelector('i');
                icon.classList.toggle('bi-chevron-down');
                icon.classList.toggle('bi-chevron-up');
            });
        });

        function processReturn(loanId, type, rowQty) {
            const typeLabel = type.charAt(0).toUpperCase() + type.slice(1);
            const modalTitle = document.getElementById('returnModalTitle');
            const leadText = document.getElementById('returnLeadText');
            const bodyText = document.getElementById('returnMessageBody');
            const icon = document.getElementById('returnIcon');
            const btnConfirm = document.getElementById('btnConfirmReturn');

            // Set Qty
            const modalQty = document.getElementById('modalQty');
            modalQty.value = rowQty; // Auto-set to row quantity

            // Reset classes
            icon.className = 'bi mb-3';
            btnConfirm.className = 'btn px-4 shadow-sm';

            if (type === 'good') {
                modalTitle.innerText = 'Return as Good';
                leadText.innerText = 'Confirm Return?';
                bodyText.innerText = `Full quantity (${rowQty} item(s)) will be returned to the warehouse inventory.`;
                icon.classList.add('bi-check-circle', 'text-success');
                btnConfirm.classList.add('btn-success');
            } else if (type === 'damage') {
                modalTitle.innerText = 'Return as Damaged';
                leadText.innerText = 'Report Damage?';
                bodyText.innerText = `Full quantity (${rowQty} item(s)) will be marked as finished but NOT restored to stock. A damage report will be created.`;
                icon.classList.add('bi-exclamation-triangle', 'text-warning');
                btnConfirm.classList.add('btn-warning');
            } else if (type === 'lost') {
                modalTitle.innerText = 'Report as Lost';
                leadText.innerText = 'Confirm Lost?';
                bodyText.innerText = `Full quantity (${rowQty} item(s)) will be removed from circulation and a lost report will be generated.`;
                icon.classList.add('bi-x-circle', 'text-danger');
                btnConfirm.classList.add('btn-danger');
            }

            // Handle Reason Visibility
            const reasonContainer = document.getElementById('reasonContainer');
            const modalReason = document.getElementById('modalReason');
            if (type === 'good') {
                reasonContainer.classList.add('d-none');
                modalReason.value = ''; // clear
            } else {
                reasonContainer.classList.remove('d-none');
            }

            // Set Form Data
            const form = document.getElementById('formReturn');
            form.action = `/loan/${loanId}/return`;
            document.getElementById('returnType').value = type;

            // Bind click to submit
            btnConfirm.onclick = function () {
                if (type !== 'good' && !modalReason.value.trim()) {
                    alert('Please provide a reason!');
                    return;
                }

                document.getElementById('returnReason').value = modalReason.value;
                document.getElementById('returnQty').value = rowQty;
                form.submit();
            };

            // Show Modal
            const myModal = new bootstrap.Modal(document.getElementById('returnModal'));
            myModal.show();
        }
    </script>

@endsection