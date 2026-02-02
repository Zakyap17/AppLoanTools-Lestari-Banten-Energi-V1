@extends('layouts.app')

@section('title', 'Locations')

@section('full_width_header')
    @include('partials.topbar', ['title' => 'Master Data Locations'])
@endsection

@section('content')

    <style>
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
            /* Removed negative margins to make it a box */
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
            /* overflow: hidden; Removed to allow dropdowns */
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

        .btn-action-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-action-icon:hover {
            background-color: #f1f5f9;
        }
    </style>

    {{-- HEADER --}}
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div class="position-relative z-1">
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Master Data</h6>
            <h3 class="fw-bold mb-0">Locations</h3>
            <p class="text-white-50 mb-0 small mt-1">Manage warehouse storage, rooms, and bins</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            {{-- SEARCH --}}
            <form action="{{ route('locations.index') }}" method="GET" class="position-relative mb-0">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="search-input-modern ps-5"
                    placeholder="Search code, name..." autocomplete="off">
            </form>

            {{-- NEW BUTTON --}}
            <button class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm"
                onclick="window.location.href='{{ route('locations.create') }}'">
                <i class="bi bi-plus-lg me-1"></i> New Location
            </button>

            {{-- MORE ACTIONS --}}
            <div class="dropdown">
                <button class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center"
                    data-bs-toggle="dropdown" style="width: 42px; height: 42px;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                    <li><a class="dropdown-item rounded-2 py-2 small" href="{{ route('locations.template') }}"><i
                                class="bi bi-file-earmark-spreadsheet me-2 text-success"></i> Template</a></li>
                    <li><a class="dropdown-item rounded-2 py-2 small" href="#" data-bs-toggle="modal"
                            data-bs-target="#importModal"><i class="bi bi-upload me-2 text-primary"></i>
                            Upload CSV</a></li>
                </ul>
            </div>
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

    {{-- MAIN CONTENT CARD --}}
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table table-custom align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th> {{-- Expander --}}
                        <th>Tools Room Code</th>
                        <th>Tools Room Name</th>
                        <th>Storage Type</th>
                        <th>Warehouse</th>
                        <th>Total Bin</th>
                        <th class="text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $item)
                        {{-- PARENT ROW --}}
                        <tr>
                            <td class="text-center">
                                <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $item->id }}" aria-expanded="false"
                                    aria-controls="collapse-{{ $item->id }}">
                                    <i class="bi bi-chevron-right" style="transition: transform 0.2s;"></i>
                                </button>
                            </td>
                            <td class="fw-bold">{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->storage_type ?? '-' }}</span></td>
                            <td>{{ $item->warehouse ?? '-' }}</td>
                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ $item->total_bin }} Bins</span></td>
                            <td class="text-center">
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('locations.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button onclick="openDeleteModal('{{ route('locations.destroy', $item->id) }}')"
                                        class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                            </td>
                        </tr>

                        {{-- CHILD ROW (ACCORDION) --}}
                        <tr class="collapse bg-light" id="collapse-{{ $item->id }}">
                            <td colspan="7" class="p-0 border-bottom">
                                <div class="px-4 py-3">
                                    <h6 class="small fw-bold text-uppercase text-muted mb-3">Bin Locations in {{ $item->name }}
                                    </h6>
                                    <div class="card border-0 shadow-sm">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3 py-2 text-muted fw-semibold small">Bin Code</th>
                                                    <th class="py-2 text-muted fw-semibold small">Description</th>
                                                    <th class="text-center py-2 text-muted fw-semibold small"
                                                        style="width: 100px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($item->racks as $rack)
                                                    <tr>
                                                        <td class="ps-3">{{ $rack->code }}</td>
                                                        <td class="text-muted small">{{ $rack->description ?? '-' }}</td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <a href="{{ route('racks.edit', $rack->id) }}"
                                                                    class="btn btn-sm btn-link text-secondary p-1" title="Edit">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-danger p-1"
                                                                    onclick="openDeleteModal('{{ route('racks.destroy', $rack->id) }}')"
                                                                    title="Delete"
                                                                    style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                                                    <i class="bi bi-trash" style="font-size: 0.8rem;"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted fst-italic small py-3">
                                                            No racks available for this storage.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-geo-alt display-4 opacity-25 mb-3"></i>
                                    <h5 class="fw-normal">No locations found</h5>
                                    <p class="small text-muted mb-0">Start by adding a new storage location</p>
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
                Showing {{ $locations->firstItem() ?? 0 }} to {{ $locations->lastItem() ?? 0 }} of {{ $locations->total() }}
                entries
            </div>
            <div>
                {{ $locations->links() }}
            </div>
        </div>
    </div>

    <script>
        // Simple script to toggle chevron icon
        document.addEventListener('DOMContentLoaded', function () {
            const collapseElements = document.querySelectorAll('.collapse');
            collapseElements.forEach(el => {
                el.addEventListener('show.bs.collapse', function () {
                    const btn = document.querySelector(`[data-bs-target="#${this.id}"]`);
                    if (btn) btn.querySelector('i').classList.replace('bi-chevron-right', 'bi-chevron-down');
                });
                el.addEventListener('hide.bs.collapse', function () {
                    const btn = document.querySelector(`[data-bs-target="#${this.id}"]`);
                    if (btn) btn.querySelector('i').classList.replace('bi-chevron-down', 'bi-chevron-right');
                });
            });
        });
    </script>

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0 py-3">
                    <h6 class="modal-title fw-bold">Import Locations (CSV & Excel)</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('locations.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <div class="upload-zone p-5 text-center border border-2 border-dashed rounded-4 position-relative"
                                style="border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.2s;"
                                ondragover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='#eff6ff'; event.preventDefault();"
                                ondragleave="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';"
                                ondrop="handleDropLoc(event)">

                                <input type="file" name="file" id="fileInputLoc"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    accept=".csv, .txt, .xlsx, .xls" required style="cursor: pointer;"
                                    onchange="handleFileSelectLoc(this)">

                                <div style="pointer-events: none;">
                                    <div class="mb-3">
                                        <div class="bg-primary bg-opacity-10 d-inline-flex p-3 rounded-circle text-primary">
                                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Click to upload or drag and drop</h6>
                                    <p class="text-muted small mb-0" id="fileNameDisplayLoc">CSV or Excel file (max 2MB)</p>
                                </div>
                            </div>
                            <div class="form-text small text-center mt-2">
                                <i class="bi bi-info-circle me-1"></i> Supported formats: CSV (semicolon/comma) or Excel
                                (.xlsx, .xls).
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i> Import Data
                            </button>
                        </div>
                    </form>

                    <script>
                        function handleFileSelectLoc(input) {
                            if (input.files.length > 0) {
                                validateAndDisplayLoc(input.files[0], input);
                            }
                        }

                        function handleDropLoc(event) {
                            event.preventDefault();
                            const zone = event.currentTarget;
                            zone.style.borderColor = '#cbd5e1';
                            zone.style.backgroundColor = '#f8fafc';

                            const input = document.getElementById('fileInputLoc');
                            if (event.dataTransfer.files.length > 0) {
                                input.files = event.dataTransfer.files;
                                validateAndDisplayLoc(event.dataTransfer.files[0], input);
                            }
                        }

                        function validateAndDisplayLoc(file, input) {
                            const display = document.getElementById('fileNameDisplayLoc');
                            const maxSize = 2 * 1024 * 1024; // 2MB

                            if (file.size > maxSize) {
                                // Error
                                display.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-circle-fill me-1"></i> File too large (> 2MB)!</span>';
                                input.value = ''; // Clear input
                            } else {
                                // Success
                                display.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> ' + file.name + '</span>';
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>

@endsection