<div class="d-flex justify-content-center gap-1">
    {{-- View --}}
    @if(isset($showRoute))
        <a href="{{ $showRoute }}" class="btn btn-sm btn-info text-white" title="View">
            <i class="bi bi-file-earmark-text"></i>
        </a>
    @endif

    {{-- Edit --}}
    @if(isset($editRoute))
        <a href="{{ $editRoute }}" class="btn btn-sm btn-success" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
    @endif

    {{-- Delete --}}
    @if(isset($destroyRoute))
        <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="openDeleteModal('{{ $destroyRoute }}')">
            <i class="bi bi-trash"></i>
        </button>
    @endif
</div>