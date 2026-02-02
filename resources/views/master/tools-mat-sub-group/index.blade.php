@extends('layouts.app')

@section('title', 'Tools MAT Sub Group')

@section('full_width_header')
    @include('partials.topbar')
@endsection

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
            <h6 class="text-white-50 mb-1 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Master Data</h6>
            <h3 class="fw-bold mb-0">Tools MAT Sub Group</h3>
            <p class="text-white-50 mb-0 small mt-1">Manage material sub-groups hierarchy</p>
        </div>
        <div class="position-relative z-1 d-flex gap-3 align-items-center">
            {{-- SEARCH --}}
            <form action="{{ route('tools-mat-sub-group.index') }}" method="GET" class="position-relative mb-0">
                <i class="bi bi-search position-absolute text-muted"
                    style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="search-input-modern ps-5"
                    placeholder="Search sub group..." autocomplete="off">
            </form>

            {{-- NEW BUTTON --}}
            <button class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm"
                onclick="window.location.href='{{ route('tools-mat-sub-group.create') }}'">
                <i class="bi bi-plus-lg me-1"></i> New Sub Group
            </button>

            {{-- MORE ACTIONS --}}
            <div class="dropdown">
                <button class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center"
                    data-bs-toggle="dropdown" style="width: 42px; height: 42px;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                    <li><a class="dropdown-item rounded-2 py-2 small" href="#"><i
                                class="bi bi-file-earmark-spreadsheet me-2 text-success"></i> Template</a></li>
                    <li><a class="dropdown-item rounded-2 py-2 small" href="#"><i
                                class="bi bi-upload me-2 text-primary"></i> Upload</a></li>
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
                        <th width="50" class="text-center">No.</th>
                        <th width="150">Mat Group Code</th>
                        <th width="150">Sub Group Code</th>
                        <th>Sub Group Description</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subGroups as $sub)
                        <tr>
                            <td class="text-center"><span
                                    class="text-muted small">{{ $loop->iteration + $subGroups->firstItem() - 1 }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $sub->mat_group_code }}</span></td>
                            <td class="fw-bold text-primary">{{ $sub->sub_code }}</td>
                            <td class="text-dark">{{ $sub->sub_description }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('tools-mat-sub-group.edit', $sub->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button onclick="openDeleteModal('{{ route('tools-mat-sub-group.destroy', $sub->id) }}')"
                                        class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-collection display-4 opacity-25 mb-3"></i>
                                    <h5 class="fw-normal">No sub groups found</h5>
                                    <p class="small text-muted mb-0">Start by adding a new sub group</p>
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
                Showing {{ $subGroups->firstItem() ?? 0 }} to {{ $subGroups->lastItem() ?? 0 }} of {{ $subGroups->total() }}
                entries
            </div>
            <div>
                {{ $subGroups->links() }}
            </div>
        </div>
    </div>

@endsection