@extends('layouts.app')

@section('title', 'Receive')

@section('content')

    <style>
        /* Page Header */
        .page-header-modern {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 0;
            margin-bottom: 2rem;
            position: relative;

            /* Full Width Breakout */
            margin-top: -24px;
            /* Counteract container padding + border */
            margin-left: -20px;
            margin-right: -20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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
            width: 250px;
            transition: all 0.2s;
        }

        .search-input-modern:focus {
            outline: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            width: 300px;
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
            <h3 class="fw-bold mb-0">Receive</h3>
            <p class="text-white-50 mb-0 small mt-1">Manage incoming tools and assets</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            {{-- SEARCH --}}
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" class="search-input-modern ps-5" placeholder="Search Doc No..." autocomplete="off">
            </div>

            {{-- NEW BUTTON --}}
            <button class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> New Receive
            </button>
        </div>
    </div>

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 border-start border-4 border-success">
                <div class="card-body position-relative bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-0"
                                style="font-size: 0.7rem; letter-spacing: 0.5px;">Received Today</h6>
                            <h2 class="fw-bold mb-0 text-dark">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table table-custom align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width:40px" class="text-center">#</th>
                        <th>Document No.</th>
                        <th>Document Date</th>
                        <th>Reference PO No.</th>
                        <th>Employee Name</th>
                        <th>Reason</th>
                        <th style="width:120px" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- SINGLE DUMMY ROW --}}
                    <tr>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light border rounded-circle toggle-detail"
                                data-target="receive-detail-1" title="Show Items"
                                style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </td>
                        <td class="fw-bold text-primary">REC00011</td>
                        <td>27 May 2023</td>
                        <td>4100010062</td>
                        <td>Jahrotul Uyun</td>
                        <td class="text-muted small">Initial Stock</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-2 text-primary"
                                title="View Document"
                                style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-text"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- EXPAND DETAIL --}}
                    <tr id="receive-detail-1" class="d-none bg-light">
                        <td colspan="7" class="p-4 bg-light border-bottom">
                            <div class="bg-white rounded-3 shadow-sm border overflow-hidden p-3">
                                <h6 class="fw-bold small text-uppercase text-muted mb-3"><i
                                        class="bi bi-list-check me-2"></i> Received Items</h6>
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="text-secondary" style="font-size: 0.8rem;">
                                        <tr>
                                            <th style="width:40px">No.</th>
                                            <th>Storage</th>
                                            <th>Bin</th>
                                            <th>Items No.</th>
                                            <th>Items Name</th>
                                            <th style="width:80px" class="text-center">QTY</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.85rem;">
                                        <tr class="text-muted">
                                            <td class="text-center text-muted" colspan="7">No item details available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-top bg-light bg-opacity-25 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Showing 1 to 1 of 1 entries
            </div>
            <div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    {{-- TOGGLE SCRIPT --}}
    <script>
        document.querySelectorAll('.toggle-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                // close all
                document.querySelectorAll('[id^="receive-detail"]').forEach(row => {
                    if (row.id !== this.dataset.target) {
                        row.classList.add('d-none');
                    }
                });

                document.querySelectorAll('.toggle-detail').forEach(otherBtn => {
                    if (otherBtn !== this) {
                        const icon = otherBtn.querySelector('i');
                        icon.classList.remove('bi-chevron-up');
                        icon.classList.add('bi-chevron-down');
                    }
                });

                // open target
                const target = document.getElementById(this.dataset.target);
                target.classList.toggle('d-none');

                const icon = this.querySelector('i');
                icon.classList.toggle('bi-chevron-down');
                icon.classList.toggle('bi-chevron-up');
            });
        });
    </script>

@endsection