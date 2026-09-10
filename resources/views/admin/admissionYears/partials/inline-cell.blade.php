@php($editable = auth()->user()?->isAdmin() ?? false)

@if($editable)
<span class="icp-inline-edit" role="button" tabindex="0" title="Click to edit"
    data-url="{{ route('admission-year.inline-update', $model->getKey()) }}"
    data-field="{{ $field }}"
    data-type="{{ $type }}"><span class="icp-inline-edit-value">{{ $value }}</span>@svg('heroicon-m-pencil-square', 'icp-inline-edit-icon')</span>
@else
<span>{{ $value }}</span>
@endif
