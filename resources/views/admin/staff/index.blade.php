@extends('admin.templates.index')

@section('title', $title)

@section('index_content')

    <div class="icp-datatable-wrapper">
        {!! $dataTable->table() !!}
    </div>

@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        (function ($) {
            'use strict';

            var TABLE = '#staff-table';
            var csrf = $('meta[name="csrf-token"]').attr('content');

            function table() {
                return $(TABLE).DataTable();
            }

            function reload() {
                // Keep the current page / search / ordering.
                table().ajax.reload(null, false);
            }

            // ── Toasts ────────────────────────────────────────────────────────
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

            // ── Row actions ───────────────────────────────────────────────────
            $(document).on('click', TABLE + ' .js-toggle-status', function () {
                var button = $(this);

                $.ajax({
                    url: button.data('url'),
                    method: 'POST',
                    dataType: 'json',
                    data: { _token: csrf }
                }).done(function (response) {
                    toast(response.data.message, 'success');
                    reload();
                }).fail(function (xhr) {
                    toast(errorFrom(xhr, 'Could not update this staff member\'s status.'), 'danger');
                });
            });

            $(document).on('click', TABLE + ' .js-delete', function () {
                var button = $(this);

                if (!window.confirm('Delete the staff member "' + button.data('title') + '"? This cannot be undone.')) {
                    return;
                }

                $.ajax({
                    url: button.data('url'),
                    method: 'POST',
                    dataType: 'json',
                    data: { _token: csrf, _method: 'DELETE' }
                }).done(function (response) {
                    toast(response.data.message, 'success');
                    reload();
                }).fail(function (xhr) {
                    toast(errorFrom(xhr, 'Could not delete this staff member.'), 'danger');
                });
            });
        })(jQuery);
    </script>
@endpush
