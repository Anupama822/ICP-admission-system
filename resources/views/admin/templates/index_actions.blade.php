@php($key = $id ?? $item['id'])

<div class="d-inline-flex align-items-center gap-2">
    @unless($hideShow ?? false)
        <a href="{{ route($route.'show', $key) }}" class="icp-action icp-action-view" title="View">
            @svg('heroicon-m-eye', 'icp-icon-sm')
        </a>
    @endunless

    @unless($hideEdit ?? false)
        <a href="{{ route($route.'edit', $key) }}" class="icp-action icp-action-edit" title="Edit">
            @svg('heroicon-m-pencil-square', 'icp-icon-sm')
        </a>
    @endunless

    @unless($hideDelete ?? false)
        <form class="d-inline js-confirm-delete" action="{{ route($route.'destroy', $key) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="icp-action icp-action-delete" title="Delete">
                @svg('heroicon-m-trash', 'icp-icon-sm')
            </button>
        </form>
    @endunless

    @foreach($actions ?? [] as $action)
        {!! $action !!}
    @endforeach
</div>
