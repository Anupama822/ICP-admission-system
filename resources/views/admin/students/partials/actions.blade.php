<div class="d-inline-flex align-items-center gap-2">
    @can('students.view')
        <a href="{{ route($route.'show', $model->getKey()) }}" class="icp-action icp-action-view" title="View">
            @svg('heroicon-m-eye', 'icp-icon-sm')
        </a>
    @endcan

    @can('students.edit')
        <a href="{{ route($route.'edit', $model->getKey()) }}" class="icp-action icp-action-edit" title="Edit">
            @svg('heroicon-m-pencil-square', 'icp-icon-sm')
        </a>
    @endcan

    @can('students.export-pdf')
        <a href="{{ route($route.'export-pdf', $model->getKey()) }}" class="icp-action" title="Download PDF">
            @svg('heroicon-m-document-arrow-down', 'icp-icon-sm')
        </a>
    @endcan

    @can('students.delete')
        <button type="button" class="icp-action icp-action-delete js-delete" title="Delete"
            data-url="{{ route($route.'destroy', $model->getKey()) }}"
            data-title="{{ $model->fullName() }}">
            @svg('heroicon-m-trash', 'icp-icon-sm')
        </button>
    @endcan
</div>
