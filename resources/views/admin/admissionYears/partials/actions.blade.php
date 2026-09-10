@php($isAdmin = auth()->user()?->isAdmin() ?? false)

<div class="d-inline-flex align-items-center gap-2">
    <a href="{{ route($route.'show', $model->getKey()) }}" class="icp-action icp-action-view" title="View">
        @svg('heroicon-m-eye', 'icp-icon-sm')
    </a>

    @if($isAdmin)
        @unless($model->is_active)
            <button type="button" class="icp-action icp-action-activate js-activate" title="Make active"
                data-url="{{ route($route.'activate', $model->getKey()) }}"
                data-title="{{ $model->title }}">
                @svg('heroicon-m-bolt', 'icp-icon-sm')
            </button>
        @endunless

        <a href="{{ route($route.'edit', $model->getKey()) }}" class="icp-action icp-action-edit" title="Edit">
            @svg('heroicon-m-pencil-square', 'icp-icon-sm')
        </a>

        <button type="button"
            class="icp-action icp-action-delete js-delete {{ $model->is_active ? 'disabled' : '' }}"
            title="{{ $model->is_active ? 'The active admission year cannot be deleted' : 'Delete' }}"
            @disabled($model->is_active)
            data-url="{{ route($route.'destroy', $model->getKey()) }}"
            data-title="{{ $model->title }}">
            @svg('heroicon-m-trash', 'icp-icon-sm')
        </button>
    @endif
</div>
