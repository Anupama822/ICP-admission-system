<tr data-id="{{ $item->getKey() }}">
    <td>
        <span class="icp-inline-edit" role="button" tabindex="0" title="Click to edit"
            data-url="{{ route('admin.document-types.inline-update', $item->getKey()) }}"
            data-field="name" data-type="text"><span class="icp-inline-edit-value">{{ $item->name }}</span>@svg('heroicon-m-pencil-square', 'icp-inline-edit-icon')</span>
    </td>
    <td>
        <span class="icp-inline-edit" role="button" tabindex="0" title="Click to edit"
            data-url="{{ route('admin.document-types.inline-update', $item->getKey()) }}"
            data-field="format_hint" data-type="text"><span class="icp-inline-edit-value">{{ $item->format_hint }}</span>@svg('heroicon-m-pencil-square', 'icp-inline-edit-icon')</span>
    </td>
    <td class="text-end">
        <button type="button" class="btn btn-sm text-danger js-delete"
            data-url="{{ route('admin.document-types.destroy', $item->getKey()) }}"
            data-name="{{ $item->name }}" title="Delete">
            @svg('heroicon-m-trash', 'icp-icon-sm')
        </button>
    </td>
</tr>
