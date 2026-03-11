@extends('layouts.app')

@section('title', 'Tools')

@section('full_width_header')
    @include('partials.topbar', ['title' => 'Master Data Tools'])
@endsection

@section('content')

    <style>
        /* Page Header */
        /* Page Header */
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
            /* overflow: hidden; Removed */
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
            color: #64748b;
            transition: all 0.2s;
        }
        .btn-action-icon:hover {
            background-color: #f1f5f9;
            color: #3b82f6;
        }

        /* Stats Cards Hover Effect */
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>

    {{-- HEADER --}}
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div class="position-relative z-1">
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Master Data</h6>
            <h3 class="fw-bold mb-0">
                Tools Management
                @if(request('status') == 'archived')
                    <span class="badge bg-danger fs-6 ms-2 align-middle">ARCHIVE</span>
                @endif
            </h3>
            <p class="text-white-50 mb-0 small mt-1">Manage tools, assets, and serialized items</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            {{-- SEARCH --}}
            <form action="{{ route('tools.index') }}" method="GET" class="position-relative mb-0">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <i class="bi bi-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="search-input-modern ps-5" placeholder="Code or Name..." autocomplete="off">
            </form>

            {{-- NEW BUTTON --}}
            <button class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm" onclick="window.location.href='{{ route('tools.create') }}'">
                <i class="bi bi-plus-lg me-1"></i> New Tool
            </button>
            
            {{-- MORE ACTIONS --}}
            <div class="dropdown">
                <button class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" style="width: 42px; height: 42px;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                    @if(request('status') == 'archived')
                        <li><a class="dropdown-item rounded-2 py-2 small" href="{{ route('tools.index') }}"><i class="bi bi-grid text-primary me-2"></i> View Active Data</a></li>
                    @else
                        <li><a class="dropdown-item rounded-2 py-2 small" href="{{ route('tools.index', ['status' => 'archived']) }}"><i class="bi bi-archive text-danger me-2"></i> View Archive</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item rounded-2 py-2 small" href="{{ route('tools.template') }}"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i> Template</a></li>
                    <li><a class="dropdown-item rounded-2 py-2 small" href="#" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload me-2 text-primary"></i> Upload</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stats-card position-relative overflow-hidden bg-white">
                <div class="card-body position-relative">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-grid-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-bold small mb-0" style="font-size: 0.7rem; letter-spacing: 0.5px;">Master Data</h6>
                            <h2 class="fw-bold mb-0 text-dark">{{ $totalMasterData }}</h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Tool Types</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
             <div class="card border-0 shadow-sm h-100 stats-card position-relative overflow-hidden bg-white">
                <div class="card-body position-relative">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-boxes fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-bold small mb-0" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Assets</h6>
                            <h2 class="fw-bold mb-0 text-dark">{{ $totalItems }}</h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Physical Items</small>
                        </div>
                    </div>
                </div>
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

    {{-- MAIN CONTENT CARD --}}
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table table-custom align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th class="small">No.</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Ref No.</th>
                        <th>Type</th>
                        <th>Location / Bin</th>
                        <th class="text-center">QTY (Avail/Total)</th>
                        <th class="text-center">PO Number</th>
                        <th class="text-center">UOM</th>
                        <th>Manufacturer</th>
                        <th>Price</th>
                        <th class="text-center">Status</th>
                        @if(request('status') != 'archived')
                            <th class="small text-center">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tools as $tool)
                        <tr>
                            <td class="text-center">
                                <button class="btn btn-expand toggle-detail border-0 bg-transparent p-0 text-secondary"
                                    data-target="tool-detail-{{ $tool->id }}" title="Show Serialized Items">
                                    <i class="bi bi-chevron-right transition-icon" style="font-size: 1rem; transition: transform 0.2s;"></i>
                                </button>
                            </td>
                            <td class="small text-center"><span class="text-muted">{{ $loop->iteration + $tools->firstItem() - 1 }}</span></td>
                            <td class="fw-bold text-nowrap text-primary">{{ $tool->code }}</td>
                            <td class="fw-bold">{{ $tool->name }}</td>
                            <td class="small">{{ $tool->serial_number ?? '-' }}</td>
                            <td class="small">
                                <div class="text-dark">{{ $tool->type->name ?? '-' }}</div>
                            </td>
                            <td class="small">
                                <div>{{ $tool->location->name ?? '-' }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $tool->rack->code ?? '-' }}</div>
                            </td>
                            <td class="text-center fw-bold">
                                <span class="{{ $tool->available_quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $tool->available_quantity }}
                                </span> / <span class="text-muted">{{ $tool->items->count() }}</span>
                            </td>
                            <td class="text-center small">{{ $tool->po_number ?? '-' }}</td>
                            <td class="text-center small">{{ $tool->uom ?? '-' }}</td>
                            <td class="small">{{ $tool->manufacturer->name ?? '-' }}</td>
                            <td class="text-end small">{{ $tool->price ? number_format($tool->price, 0, ',', '.') : '-' }}</td>
                            <td class="text-center">
                                @php
                                    $calStatus = null;
                                    if ($tool->next_calibration_date) {
                                        $remaining = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($tool->next_calibration_date), false);
                                        if ($remaining <= 14) {
                                            $calStatus = 'Need Calibration';
                                        }
                                    }
                                @endphp

                                @if($calStatus)
                                    <span class="badge bg-danger blink-badge">Need Calibration</span>
                                @else
                                    @php
                                        $items = $tool->items;
                                        $total = $items->count();
                                        $hasAvailable = $items->contains('status', 'available');
                                        
                                        $dispStatus = 'Unavailable';
                                        $dispBadge = 'bg-secondary';

                                        if ($hasAvailable) {
                                            $dispStatus = 'Available';
                                            $dispBadge = 'bg-success';
                                        } elseif ($total > 0) {
                                            $cntInUse = $items->where('status', 'in_use')->count();
                                            $cntDamage = $items->where('status', 'damage')->count();
                                            $cntLost = $items->where('status', 'lost')->count();
                                            
                                            if ($cntInUse == $total) {
                                                $dispStatus = 'In Use';
                                                $dispBadge = 'bg-info';
                                            } elseif ($cntDamage == $total) {
                                                $dispStatus = 'Damage';
                                                $dispBadge = 'bg-danger';
                                            } elseif ($cntLost == $total) {
                                                $dispStatus = 'Lost';
                                                $dispBadge = 'bg-dark';
                                            } elseif (($cntDamage + $cntLost) == $total) {
                                                $dispStatus = 'Damage/Lost';
                                                $dispBadge = 'bg-danger';
                                            } else {
                                                $dispStatus = 'Unavailable';
                                                $dispBadge = 'bg-warning';
                                            }
                                        } else {
                                            $dispStatus = ucfirst($tool->status);
                                            $dispBadge = ($tool->status === 'available') ? 'bg-success' : 'bg-warning';
                                            if ($total == 0) $dispBadge = 'bg-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $dispBadge }}">
                                        {{ $dispStatus }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-action-icon" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                        <li>
                                            <a class="dropdown-item small py-2" href="{{ route('tools.edit', ['tool' => $tool->id, 'page' => request('page')]) }}">
                                                <i class="bi bi-pencil-square text-success me-2"></i> Edit Tool
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item small py-2" href="{{ route('tools.good-receipt', $tool->id) }}" target="_blank">
                                                <i class="bi bi-file-earmark-text text-primary me-2"></i> View ID GR
                                            </a>
                                        </li>
                                        @if($tool->batch_id)
                                            <li>
                                                <a class="dropdown-item small py-2" href="{{ route('tools.print-batch', $tool->batch_id) }}" target="_blank">
                                                    <i class="bi bi-collection text-info me-2"></i> View Batch GR
                                                </a>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button class="dropdown-item small py-2 text-danger" onclick="openDeleteModal('{{ route('tools.destroy', $tool->id) }}', true)">
                                                <i class="bi bi-trash me-2"></i> Delete Tool
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- EXPAND DETAIL ROW --}}
                        <tr id="tool-detail-{{ $tool->id }}" class="d-none bg-light">
                            <td colspan="13" class="p-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom-0 py-2 d-flex align-items-center">
                                        <i class="bi bi-list-ol me-2 text-primary"></i>
                                        <h6 class="fw-bold small mb-0 text-primary">Serialized Items List</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        @if($tool->items->isEmpty())
                                            <div class="p-3 text-muted small">No serialized items generated.</div>
                                        @else
                                            <div class="table-responsive" style="max-height: 250px;">
                                                <table class="table table-sm table-striped mb-0">
                                                    <thead class="table-secondary sticky-top">
                                                        <tr>
                                                            <th class="ps-3">Serial Number</th>
                                                            <th class="text-center">Status</th>
                                                            <th>Condition</th>
                                                            <th>Input Date (Dibuat)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tool->items as $item)
                                                            @php
                                                                // Logic Override: If condition OR status is Damage, Display as Damage & Not Available
                                                                $cond = strtolower($item->condition ?? '');
                                                                $status = strtolower($item->status ?? '');
                                                                
                                                                // Check both fields for damage/rusak
                                                                $isDamage = str_contains($cond, 'damage') || str_contains($cond, 'rusak') || str_contains($status, 'damage');
                                                                
                                                                $displayStatus = ucfirst(str_replace('_', ' ', $item->status));
                                                                $badgeClass = $item->status === 'available' ? 'bg-success' : 
                                                                             ($item->status === 'in_use' ? 'bg-primary' : 
                                                                             ($item->status === 'lost' ? 'bg-secondary' : 'bg-danger'));

                                                                if($isDamage) {
                                                                    $displayStatus = 'Not Available';
                                                                    $badgeClass = 'bg-danger';
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td class="fw-bold ps-3">{{ $item->serial_number }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge {{ $badgeClass }} small">
                                                                        {{ $displayStatus }}
                                                                    </span>
                                                                </td>
                                                                <td class="small">
                                                                    @if($status === 'lost')
                                                                        <span>-</span>
                                                                    @elseif($isDamage)
                                                                        <span class="text-danger fw-bold">Damage</span>
                                                                    @else
                                                                        {{ $item->condition }}
                                                                    @endif
                                                                </td>
                                                                <td class="small">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-tools display-4 opacity-25 mb-3"></i>
                                    <h5 class="fw-normal">No tools found</h5>
                                    <p class="small text-muted mb-0">Start by creating a new tool entry</p>
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
                Showing {{ $tools->firstItem() ?? 0 }} to {{ $tools->lastItem() ?? 0 }} of {{ $tools->total() }} entries
            </div>
            <div>
                {{ $tools->links() }}
            </div>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0 py-3">
                    <h6 class="modal-title fw-bold">Import Tools</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('tools.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <div class="upload-zone p-5 text-center border border-2 border-dashed rounded-4 position-relative"
                                id="uploadZoneTools"
                                style="border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.2s;"
                                ondragover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='#eff6ff'; event.preventDefault();"
                                ondragleave="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';"
                                ondrop="handleDropTools(event)">

                                <input type="file" name="file" id="fileInputTools"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".csv, .txt, .xlsx, .xls"
                                    required style="cursor: pointer;"
                                    onchange="handleFileSelectTools(this)">

                                <!-- Default Placeholder -->
                                <div id="placeholderTools" style="pointer-events: none;">
                                    <div class="mb-3">
                                        <div class="bg-primary bg-opacity-10 d-inline-flex p-3 rounded-circle text-primary">
                                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Click to upload or drag and drop</h6>
                                    <p class="text-muted small mb-0">CSV or Excel file (max 2MB)</p>
                                </div>

                                <!-- Success State -->
                                <div id="successTools" class="d-none position-relative" style="z-index: 10;">
                                    <div class="mb-3">
                                        <div class="bg-success bg-opacity-10 d-inline-flex p-3 rounded-circle text-success">
                                            <i class="bi bi-file-earmark-spreadsheet fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" id="fileNameTools">filename.csv</h6>
                                    <p class="text-success small mb-3 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Ready to import
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="removeFileTools()">
                                        <i class="bi bi-x-circle me-1"></i> Remove File
                                    </button>
                                </div>

                                <!-- Error State -->
                                <div id="errorTools" class="d-none position-relative" style="z-index: 10;">
                                    <div class="mb-3">
                                        <div class="bg-danger bg-opacity-10 d-inline-flex p-3 rounded-circle text-danger">
                                            <i class="bi bi-exclamation-triangle fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-danger mb-1" id="errorMsgTools">Error</h6>
                                    <p class="text-muted small mb-3">Please try again</p>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="removeFileTools()">
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
                        function handleFileSelectTools(input) {
                            if (input.files.length > 0) {
                                validateAndDisplayTools(input.files[0]);
                            }
                        }

                        function handleDropTools(event) {
                            event.preventDefault();
                            const zone = document.getElementById('uploadZoneTools');
                            zone.style.borderColor = '#cbd5e1';
                            zone.style.backgroundColor = '#f8fafc';
                            
                            const input = document.getElementById('fileInputTools');
                            if (event.dataTransfer.files.length > 0) {
                                input.files = event.dataTransfer.files;
                                validateAndDisplayTools(event.dataTransfer.files[0]);
                            }
                        }

                        function validateAndDisplayTools(file) {
                            const maxSize = 2 * 1024 * 1024; // 2MB
                            const placeholder = document.getElementById('placeholderTools');
                            const success = document.getElementById('successTools');
                            const error = document.getElementById('errorTools');
                            const errorMsg = document.getElementById('errorMsgTools');
                            const fileName = document.getElementById('fileNameTools');
                            const input = document.getElementById('fileInputTools');

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

                        function removeFileTools() {
                            const input = document.getElementById('fileInputTools');
                            const placeholder = document.getElementById('placeholderTools');
                            const success = document.getElementById('successTools');
                            const error = document.getElementById('errorTools');

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

    <script>
        document.querySelectorAll('.toggle-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                // Close other expanded rows
                const targetId = this.getAttribute('data-target');
                document.querySelectorAll('.toggle-detail').forEach(otherBtn => {
                    if (otherBtn !== this) {
                        const otherTarget = document.getElementById(otherBtn.getAttribute('data-target'));
                        if (otherTarget && !otherTarget.classList.contains('d-none')) {
                            otherTarget.classList.add('d-none');
                            const icon = otherBtn.querySelector('i');
                            if(icon) {
                                icon.classList.remove('bi-chevron-down');
                                icon.classList.add('bi-chevron-right');
                            }
                        }
                    }
                });

                const target = document.getElementById(targetId);
                if(target) {
                    target.classList.toggle('d-none');
                    const icon = this.querySelector('i');
                    if (target.classList.contains('d-none')) {
                        icon.classList.remove('bi-chevron-down');
                        icon.classList.add('bi-chevron-right');
                    } else {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-down');
                    }
                }
            });
        });
    </script>

@endsection