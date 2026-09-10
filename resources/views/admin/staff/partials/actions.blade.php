<div class="d-inline-flex align-items-center gap-2">
    <a href="{{ route($route.'show', $model->getKey()) }}" class="icp-action icp-action-view" title="View">
        @svg('heroicon-m-eye', 'icp-icon-sm')
    </a>

    <button type="button" class="icp-action js-toggle-status" title="{{ $model->status === 'active' ? 'Deactivate' : 'Activate' }}"
        data-url="{{ route($route.'toggle-status', $model->getKey()) }}"
        data-name="{{ $model->name }}">
        @svg($model->status === 'active' ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open', 'icp-icon-sm')
    </button>

    <a href="{{ route($route.'edit', $model->getKey()) }}" class="icp-action icp-action-edit" title="Edit">
        @svg('heroicon-m-pencil-square', 'icp-icon-sm')
    </a>

    <button type="button" class="icp-action icp-action-delete js-delete" title="Delete"
        data-url="{{ route($route.'destroy', $model->getKey()) }}"
        data-title="{{ $model->name }}">
        @svg('heroicon-m-trash', 'icp-icon-sm')
    </button>
</div>
