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
            {{-- NEW BUTTON --}}
            <a href="{{ route('calibration-issuing.create') }}"
                class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm" title="Create New Calibration">
                <i class="bi bi-plus-lg me-1"></i> New Calibration
            </a>

            {{-- MORE ACTIONS --}}
            <div class="dropdown">
                <button class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" style="width: 42px; height: 42px;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                    <li><a class="dropdown-item rounded-2 py-2 small" href="{{ route('calibration-issuing.template') }}"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i> Template</a></li>
                    <li><a class="dropdown-item rounded-2 py-2 small" href="#" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload me-2 text-primary"></i> Upload</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {!! session('warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0 py-3">
                    <h6 class="modal-title fw-bold">Import Calibration Issuing</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('calibration-issuing.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <div class="upload-zone p-5 text-center border border-2 border-dashed rounded-4 position-relative"
                                id="uploadZoneCalib"
                                style="border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.2s;"
                                ondragover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='#eff6ff'; event.preventDefault();"
                                ondragleave="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';"
                                ondrop="handleDropCalib(event)">

                                <input type="file" name="file" id="fileInputCalib"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".csv, .txt, .xlsx, .xls"
                                    required style="cursor: pointer;"
                                    onchange="handleFileSelectCalib(this)">

                                <!-- Default Placeholder -->
                                <div id="placeholderCalib" style="pointer-events: none;">
                                    <div class="mb-3">
                                        <div class="bg-primary bg-opacity-10 d-inline-flex p-3 rounded-circle text-primary">
                                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Click to upload or drag and drop</h6>
                                    <p class="text-muted small mb-0">CSV or Excel file (max 2MB)</p>
                                </div>

                                <!-- Success State -->
                                <div id="successCalib" class="d-none position-relative" style="z-index: 10;">
                                    <div class="mb-3">
                                        <div class="bg-success bg-opacity-10 d-inline-flex p-3 rounded-circle text-success">
                                            <i class="bi bi-file-earmark-spreadsheet fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" id="fileNameCalib">filename.csv</h6>
                                    <p class="text-success small mb-3 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Ready to import
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="removeFileCalib()">
                                        <i class="bi bi-x-circle me-1"></i> Remove File
                                    </button>
                                </div>

                                <!-- Error State -->
                                <div id="errorCalib" class="d-none position-relative" style="z-index: 10;">
                                    <div class="mb-3">
                                        <div class="bg-danger bg-opacity-10 d-inline-flex p-3 rounded-circle text-danger">
                                            <i class="bi bi-exclamation-triangle fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-danger mb-1" id="errorMsgCalib">Error</h6>
                                    <p class="text-muted small mb-3">Please try again</p>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="removeFileCalib()">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Try Again
                                    </button>
                                </div>
                            </div>
                            <div class="form-text small text-center mt-2">
                                <i class="bi bi-info-circle me-1"></i> Supported: CSV, TXT, XLSX, XLS.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i> Import Data
                            </button>
                        </div>
                    </form>

                    <script>
                        function handleFileSelectCalib(input) {
                            if (input.files.length > 0) {
                                validateAndDisplayCalib(input.files[0]);
                            }
                        }

                        function handleDropCalib(event) {
                            event.preventDefault();
                            const zone = document.getElementById('uploadZoneCalib');
                            zone.style.borderColor = '#cbd5e1';
                            zone.style.backgroundColor = '#f8fafc';
                            
                            const input = document.getElementById('fileInputCalib');
                            if (event.dataTransfer.files.length > 0) {
                                input.files = event.dataTransfer.files;
                                validateAndDisplayCalib(event.dataTransfer.files[0]);
                            }
                        }

                        function validateAndDisplayCalib(file) {
                            const maxSize = 2 * 1024 * 1024; // 2MB
                            const placeholder = document.getElementById('placeholderCalib');
                            const success = document.getElementById('successCalib');
                            const error = document.getElementById('errorCalib');
                            const errorMsg = document.getElementById('errorMsgCalib');
                            const fileName = document.getElementById('fileNameCalib');
                            const input = document.getElementById('fileInputCalib');

                            placeholder.classList.add('d-none');

                            if (file.size > maxSize) {
                                success.classList.add('d-none');
                                error.classList.remove('d-none');
                                errorMsg.textContent = 'File too large (> 2MB)!';
                                input.value = '';
                            } else {
                                error.classList.add('d-none');
                                success.classList.remove('d-none');
                                fileName.textContent = file.name;
                            }
                        }

                        function removeFileCalib() {
                            const input = document.getElementById('fileInputCalib');
                            const placeholder = document.getElementById('placeholderCalib');
                            const success = document.getElementById('successCalib');
                            const error = document.getElementById('errorCalib');

                            input.value = '';
                            success.classList.add('d-none');
                            error.classList.add('d-none');
                            placeholder.classList.remove('d-none');
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>

@endsection