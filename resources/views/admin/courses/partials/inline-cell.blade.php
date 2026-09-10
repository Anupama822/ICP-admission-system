{{-- This page is only ever reached by admin (route-level enforced), so every
     field here is always editable. --}}
<span class="icp-inline-edit" role="button" tabindex="0" title="Click to edit"
    data-url="{{ route('admin.courses.inline-update', $model->getKey()) }}"
    data-field="{{ $field }}"
    data-type="{{ $type }}"><span class="icp-inline-edit-value">{{ $value }}</span>@svg('heroicon-m-pencil-square', 'icp-inline-edit-icon')</span>
