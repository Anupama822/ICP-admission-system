@extends('admin.templates.show')

@section('title', 'View '.$title)

@section('form_content')
    <dl class="row g-0 mb-0 icp-detail-list">
        <dt class="col-12 col-sm-4">Name</dt>
        <dd class="col-12 col-sm-8">{{ $item->name }}</dd>

        <dt class="col-12 col-sm-4">Email</dt>
        <dd class="col-12 col-sm-8">{{ $item->email }}</dd>

        <dt class="col-12 col-sm-4">Position</dt>
        <dd class="col-12 col-sm-8">{{ $item->position }}</dd>

        <dt class="col-12 col-sm-4">Status</dt>
        <dd class="col-12 col-sm-8">
            @include('admin.staff.partials.status', ['model' => $item])
        </dd>

        <dt class="col-12 col-sm-4">Created at</dt>
        <dd class="col-12 col-sm-8">{{ $item->created_at?->format('d M, Y \a\t H:i') }}</dd>
    </dl>

    <form method="POST" action="{{ route($route.'toggle-status', $item->getKey()) }}" class="mt-4">
        @csrf
        <button type="submit" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-4 py-2">
            @svg($item->status === 'active' ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open', 'icp-icon-sm')
            <span>{{ $item->status === 'active' ? 'Deactivate this staff member' : 'Activate this staff member' }}</span>
        </button>
    </form>

    {{-- ── Permissions ── --}}
    <div class="icp-card mt-4" id="permissions-card">
        <div class="icp-card-header">
            <p class="icp-card-title">Permissions</p>
            <p class="icp-card-subtitle">
                Granted by default when the account was created &mdash; untick anything {{ $item->name }} shouldn't have, then save.
            </p>
        </div>
        <div class="p-3">
            @include('admin.staff.partials.permission-fields', ['checkedPermissions' => $grantedPermissions])

            <div class="d-flex justify-content-end mt-2">
                <button type="button" id="js-save-permissions" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                    @svg('heroicon-m-check', 'icp-icon-sm')
                    <span>Update Permissions</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function ($) {
            'use strict';

            var csrf = $('meta[name="csrf-token"]').attr('content');
            var updateUrl = @json(route('admin.staff.permissions.update', $item->getKey()));

            function toast(message, variant) {
                var host = $('#icp-toast-host');

                if (!host.length) {
                    host = $('<div id="icp-toast-host" class="icp-toast-host"></div>').appendTo('body');
                }

                var el = $('<div class="icp-toast"></div>')
                    .addClass('icp-toast-' + (variant || 'success'))
                    .text(message)
                    .appendTo(host);

                setTimeout(function () {
                    el.addClass('is-leaving');
                    setTimeout(function () { el.remove(); }, 250);
                }, 3200);
            }

            function errorFrom(xhr, fallback) {
                return (xhr.responseJSON && xhr.responseJSON.message) || fallback;
            }

            $(document).on('click', '#js-save-permissions', function () {
                var button = $(this);
                var checkboxes = $('#permissions-card .js-permission-toggle');

                var permissions = checkboxes.filter(':checked').map(function () {
                    return this.value;
                }).get();

                button.prop('disabled', true);
                checkboxes.prop('disabled', true);

                $.ajax({
                    url: updateUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrf,
                        _method: 'PUT',
                        permissions: permissions
                    }
                }).done(function (response) {
                    toast(response.data.message || 'Permissions updated.', 'success');
                }).fail(function (xhr) {
                    toast(errorFrom(xhr, 'Could not update permissions.'), 'danger');
                }).always(function () {
                    button.prop('disabled', false);
                    checkboxes.prop('disabled', false);
                });
            });
        })(jQuery);
    </script>
@endpush
