@extends('layouts.app')

@section('title', 'Employee Directory')

@section('full_width_header')
    @include('partials.topbar')
@endsection

@section('content')

    <style>
        /* DASHBOARD HEADER */
        .page-header-modern {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            position: relative;
            overflow: visible;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }

        .search-input-modern {
            border: 1px solid #e2e8f0;
            padding: 12px 20px;
            border-radius: 12px;
            background: white;
            color: #1e293b;
            width: 300px;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-input-modern::placeholder {
            color: #94a3b8;
        }

        .search-input-modern:focus {
            background: white;
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            width: 340px;
        }

        /* LIST LAYOUT */
        .employee-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .employee-card {
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            transition: all 0.2s;
            border: 1px solid transparent;
            position: relative;
            /* overflow: hidden;  REMOVED TO FIX DROPDOWN CLIPPING */
        }

        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-color: #e2e8f0;
            z-index: 5;
        }

        /* COLOR STRIP (Left Side) */
        .dept-strip {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        /* Avatar with Initials */
        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-right: 1.5rem;
            flex-shrink: 0;
            background-color: #f1f5f9;
            /* Default fallback */
            color: #475569;
        }

        /* Avatar Image (if available in future) */
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* Layout Grid for Info */
        .info-row {
            display: flex;
            align-items: center;
            flex: 1;
            gap: 2rem;
            flex-wrap: wrap;
            /* Friendly for smaller screens */
        }

        .col-name {
            width: 22%;
            min-width: 180px;
        }

        .col-role {
            width: 18%;
            min-width: 140px;
        }

        .col-dept {
            width: 15%;
            min-width: 100px;
        }

        .col-email {
            width: 25%;
            min-width: 200px;
        }

        .name-main {
            font-weight: 700;
            color: #1e293b;
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .sub-text {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Department/Theme Colors */

        /* OHS (RED) */
        .theme-ohs .dept-strip {
            background: #ef4444;
        }

        .theme-ohs .role-text {
            color: #b91c1c;
        }

        .theme-ohs .avatar-circle {
            background: #fef2f2;
            color: #ef4444;
        }

        /* DEFAULT (GRAY/ABU) */
        .theme-default .dept-strip {
            background: #94a3b8;
        }

        .theme-default .role-text {
            color: #64748b;
        }

        .theme-default .avatar-circle {
            background: #f8fafc;
            color: #64748b;
        }

        .role-text {
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>

    {{-- HEADER --}}
    <div class="page-header-modern d-flex flex-wrap gap-3 justify-content-between align-items-center animate-up"
        style="animation-delay: 0.1s;">
        <div class="position-relative z-1">
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Master Data</h6>
            <h3 class="fw-bold mb-0">Employee Directory</h3>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center flex-wrap">
            {{-- SEARCH --}}
            <form action="{{ route('employee.index') }}" method="GET" class="position-relative mb-0">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="search-input-modern ps-5"
                    placeholder="Search employees..." autocomplete="off">
            </form>

            {{-- Actions --}}
            <button class="btn btn-primary rounded-pill fw-bold shadow-lg hover-scale px-4 py-2"
                onclick="window.location.href='{{ route('employee.create') }}'">
                <i class="bi bi-person-plus me-1"></i> Add Employee
            </button>

            <div class="dropdown">
                <button class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center shadow-lg"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 42px; height: 42px;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-1">
                    <li>
                        <h6 class="dropdown-header text-uppercase small letter-spacing-1 text-muted">Bulk Actions</h6>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('employee.template') }}">
                            <i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i> Download Template
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item py-2" type="button" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="bi bi-upload me-2 text-primary"></i> Import Data
                        </button>
                    </li>
                    {{-- 
                    @if(config('services.ldap.enabled'))
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button class="dropdown-item py-2" type="button" onclick="confirmLdapSync()">
                            <i class="bi bi-cloud-arrow-down me-2 text-info"></i> Sync from LDAP
                        </button>
                    </li>
                    <form id="ldapSyncForm" action="{{ route('employee.sync-ldap') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    @endif
                    --}}
                </ul>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0 py-3">
                    <h6 class="modal-title fw-bold">Import Employees</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('employee.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <div class="upload-zone p-5 text-center border border-2 border-dashed rounded-4 position-relative"
                                id="uploadZoneEmp"
                                style="border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.2s;"
                                ondragover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='#eff6ff'; event.preventDefault();"
                                ondragleave="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';"
                                ondrop="handleDropEmp(event)">

                                <input type="file" name="file" id="fileInputEmp"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    accept=".csv, .txt, .xlsx, .xls" required style="cursor: pointer;"
                                    onchange="handleFileSelectEmp(this)">

                                <!-- Default Placeholder -->
                                <div id="placeholderEmp" style="pointer-events: none;">
                                    <div class="mb-3">
                                        <div class="bg-primary bg-opacity-10 d-inline-flex p-3 rounded-circle text-primary">
                                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Click to upload or drag and drop</h6>
                                    <p class="text-muted small mb-0">CSV or Excel file (max 2MB)</p>
                                </div>

                                <!-- Success State -->
                                <div id="successEmp" class="d-none position-relative" style="z-index: 10;">
                                    <div class="mb-3">
                                        <div class="bg-success bg-opacity-10 d-inline-flex p-3 rounded-circle text-success">
                                            <i class="bi bi-file-earmark-spreadsheet fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" id="fileNameEmp">filename.csv</h6>
                                    <p class="text-success small mb-3 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Ready to import
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        onclick="removeFileEmp()">
                                        <i class="bi bi-x-circle me-1"></i> Remove File
                                    </button>
                                </div>

                                <!-- Error State -->
                                <div id="errorEmp" class="d-none position-relative" style="z-index: 10;">
                                    <div class="mb-3">
                                        <div class="bg-danger bg-opacity-10 d-inline-flex p-3 rounded-circle text-danger">
                                            <i class="bi bi-exclamation-triangle fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-danger mb-1" id="errorMsgEmp">Error</h6>
                                    <p class="text-muted small mb-3">Please try again</p>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                        onclick="removeFileEmp()">
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
                        function handleFileSelectEmp(input) {
                            if (input.files.length > 0) {
                                validateAndDisplayEmp(input.files[0]);
                            }
                        }

                        function handleDropEmp(event) {
                            event.preventDefault();
                            const zone = document.getElementById('uploadZoneEmp');
                            zone.style.borderColor = '#cbd5e1';
                            zone.style.backgroundColor = '#f8fafc';

                            const input = document.getElementById('fileInputEmp');
                            if (event.dataTransfer.files.length > 0) {
                                input.files = event.dataTransfer.files;
                                validateAndDisplayEmp(event.dataTransfer.files[0]);
                            }
                        }

                        function validateAndDisplayEmp(file) {
                            const maxSize = 2 * 1024 * 1024; // 2MB
                            const placeholder = document.getElementById('placeholderEmp');
                            const success = document.getElementById('successEmp');
                            const error = document.getElementById('errorEmp');
                            const errorMsg = document.getElementById('errorMsgEmp');
                            const fileName = document.getElementById('fileNameEmp');
                            const input = document.getElementById('fileInputEmp');

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

                        function removeFileEmp() {
                            const input = document.getElementById('fileInputEmp');
                            const placeholder = document.getElementById('placeholderEmp');
                            const success = document.getElementById('successEmp');
                            const error = document.getElementById('errorEmp');

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

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 animate-up" role="alert"
            style="animation-delay: 0.2s;">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- NEW LIST LIST --}}
    <div class="employee-list">
        @forelse ($employees as $employee)
            @php
                // Initials
                $initials = collect(explode(' ', $employee->name))->map(function ($word) {
                    return strtoupper(substr($word, 0, 1));
                })->take(2)->join('');

                // Theme Logic
                $dept = strtoupper($employee->department);
                $themeClass = 'theme-default'; // Default Gray

                if (str_contains($dept, 'OHS') || str_contains($dept, 'HSE') || str_contains($dept, 'K3'))
                    $themeClass = 'theme-ohs'; // RED

            @endphp

            <div class="employee-card {{ $themeClass }} animate-up"
                style="animation-delay: {{ 0.1 + ($loop->iteration * 0.05) }}s;">
                <div class="dept-strip"></div>

                {{-- Avatar --}}
                <div class="avatar-circle">
                    {{ $initials }}
                </div>

                {{-- Info Row --}}
                <div class="info-row">
                    {{-- NAME --}}
                    <div class="col-name">
                        <div class="name-main">{{ $employee->name }}</div>
                        <div class="sub-text" style="font-family: monospace;">{{ $employee->nik }}</div>
                    </div>

                    {{-- ROLE --}}
                    <div class="col-role">
                        <div class="role-text">{{ $employee->position }}</div>
                    </div>

                    {{-- DEPT --}}
                    <div class="col-dept">
                        <div class="text-dark fw-bold small">{{ $employee->department }}</div>
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-email">
                        <div class="text-dark small text-truncate" title="{{ $employee->email }}">{{ $employee->email }}</div>
                    </div>
                </div>

                {{-- Actions (Right Aligned) --}}
                <div class="ms-auto ps-3 border-start">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li>
                                <a class="dropdown-item small" href="{{ route('employee.edit', $employee->id) }}">
                                    <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item small text-danger"
                                    onclick="openDeleteModal('{{ route('employee.destroy', $employee->id) }}', true)">
                                    <i class="bi bi-trash me-2"></i> Delete
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people display-4 opacity-50"></i>
                <p class="mt-3">No employees found.</p>
            </div>
        @endforelse
    </div>

    {{-- FOOTER PAGINATION --}}
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted small">
            Showing {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }}
        </div>
        <div>
            {{ $employees->links() }}
        </div>
    </div>

    {{-- 
    <script>
        function confirmLdapSync() {
            Swal.fire({
                title: 'Sync from LDAP?',
                text: "This will pull and update all employee data from the LDAP server. It might take a moment.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, sync now!',
                showLoaderOnConfirm: true,
                didOpen: () => {
                    // Optional: customize loader
                },
                preConfirm: () => {
                    Swal.showLoading();
                    document.getElementById('ldapSyncForm').submit();
                }
            });
        }
    </script>
    --}}
@endsection