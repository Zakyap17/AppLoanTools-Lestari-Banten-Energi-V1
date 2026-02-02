@extends('layouts.app')

@section('title', 'Calibration Issuing')

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
            overflow: hidden;
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
            <h3 class="fw-bold mb-0">Calibration Issuing</h3>
            <p class="text-white-50 mb-0 small mt-1">Manage vendor calibration schedules and issuing</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" class="search-input-modern ps-5" placeholder="Search document..." id="searchInput">
            </div>
            <a href="{{ route('calibration-issuing.create') }}"
                class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm" title="Create New Calibration">
                <i class="bi bi-plus-lg me-1"></i> New Calibration
            </a>
        </div>
    </div>

    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table table-custom align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Document No.</th>
                        <th>Issue Date</th>
                        <th>Asset No.</th>
                        <th>Item Name</th>
                        <th>Next Calibration</th>
                        <th class="text-center">Countdown</th>
                        <th>Notes</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4" style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calibrations as $cal)
                        <tr>
                            <td class="fs-6 fw-bold text-primary ps-4">{{ $cal->issue_code }}</td>
                            <td>{{ \Carbon\Carbon::parse($cal->issue_date)->format('d M Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $cal->tool->code ?? '-' }}</span></td>
                            <td class="fw-bold text-dark">{{ $cal->tool->name ?? '-' }}</td>
                            <td>{{ $cal->expected_completion_date ? \Carbon\Carbon::parse($cal->expected_completion_date)->format('d M Y') : '-' }}</td>
                            <td class="text-center">
                                @php
                                    $cnt = '-';
                                    if ($cal->status !== 'completed' && $cal->expected_completion_date) {
                                        $diff = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($cal->expected_completion_date), false);
                                        $cnt = round($diff);
                                    }
                                @endphp
                                @if($cnt !== '-')
                                    <span class="badge rounded-pill px-3 {{ $cnt < 0 ? 'bg-danger' : ($cnt < 30 ? 'bg-warning text-dark' : 'bg-success') }}">
                                        {{ $cnt < 0 ? abs($cnt) . ' Days Overdue' : $cnt . ' Days Left' }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="small text-muted">{{ Str::limit($cal->notes, 30) }}</td>
                            <td class="text-center">
                                @if($cal->status == 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Completed</span>
                                @else
                                    @if($cnt !== '-' && $cnt <= 14)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill blink-badge">Need Calibration</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">In Progress</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                @if($cal->status == 'in_progress')
                                    <button type="button" class="btn btn-sm btn-success px-3 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1"
                                        title="Mark as Done"
                                        onclick="confirmComplete('{{ route('calibration-issuing.complete', $cal->id) }}', '{{ $cal->issue_code }}')">
                                        <i class="bi bi-check-lg"></i> Done
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-light text-muted border px-3 rounded-pill d-inline-flex align-items-center gap-1" disabled>
                                        <i class="bi bi-check-all"></i> Done
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox display-4 opacity-25 mb-3"></i>
                                    <h6 class="fw-normal">No calibration records found</h6>
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
                Showing {{ $calibrations->firstItem() ?? 0 }} to {{ $calibrations->lastItem() ?? 0 }} of {{ $calibrations->total() }} entries
            </div>
            <div>
                {{ $calibrations->links() }}
            </div>
        </div>
    </div>

    {{-- CONFIRMATION MODAL --}}
    <div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Finish Calibration?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-semibold mb-2">Complete <span id="modalDocNo" class="text-primary"></span>?</h6>
                    <p class="text-muted small mb-0">
                        This will mark the tool as returned/calibrated and update the next calibration schedule date.
                    </p>
                </div>
                <div class="modal-footer border-top-0 pt-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                    <form id="completeForm" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            Yes, Complete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmComplete(url, docNo) {
            document.getElementById('completeForm').action = url;
            document.getElementById('modalDocNo').innerText = docNo;
            var myModal = new bootstrap.Modal(document.getElementById('completeModal'));
            myModal.show();
        }
    </script>

@endsection