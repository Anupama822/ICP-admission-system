@extends('admin.templates.index')

@section('title', $title)

@section('index_content')

    @can('students.export-csv')
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.students.export-csv') }}" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-4 py-2">
                @svg('heroicon-m-document-arrow-down', 'icp-icon-sm')
                <span>Export CSV</span>
            </a>
        </div>
    @endcan

    <div class="icp-datatable-wrapper">
        {!! $dataTable->table() !!}
    </div>

@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        (function ($) {
            'use strict';

            var TABLE = '#students-table';
            var csrf = $('meta[name="csrf-token"]').attr('content');

            function table() {
                return $(TABLE).DataTable();
            }

            function reload() {
                table().ajax.reload(null, false);
            }

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

            $(document).on('click', TABLE + ' .js-delete', function () {
                var button = $(this);

                if (!window.confirm('Delete the enrollment for "' + button.data('title') + '"? This cannot be undone.')) {
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
                    toast(errorFrom(xhr, 'Could not delete this student.'), 'danger');
                });
            });
        })(jQuery);
    </script>
@endpush
