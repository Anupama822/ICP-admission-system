@extends('admin.templates.index')

@section('title', $title)

@section('index_content')

    <div class="icp-datatable-wrapper">
        {!! $dataTable->table() !!}
    </div>

    <p class="icp-page-subtitle small mb-0 mt-3 d-flex align-items-center gap-2">
        @svg('heroicon-m-information-circle', 'icp-icon-sm')
        <span>Click a title, credits, or description to edit it right here &mdash; <kbd>Enter</kbd> saves, <kbd>Esc</kbd> cancels.</span>
    </p>

@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        (function ($) {
            'use strict';

            var TABLE = '#courses-table';
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

            // ── Inline editing ────────────────────────────────────────────────
            // The cell keeps its rendered markup; the editor is only an extra
            // input appended while `is-editing` is on, so nothing has to be
            // re-created by hand when the editor closes.
            function openEditor(cell) {
                if (cell.hasClass('is-editing')) {
                    return;
                }

                var current = cell.find('.icp-inline-edit-value').text().trim();

                cell.addClass('is-editing').data('previous', current);

                $('<input>', {
                    type: cell.data('type') === 'number' ? 'number' : 'text',
                    'class': 'form-control form-control-sm icp-inline-input',
                    value: current
                }).appendTo(cell).trigger('focus').trigger('select');
            }

            function closeEditor(cell, value) {
                cell.removeClass('is-editing is-saving');
                cell.find('.icp-inline-input').remove();
                cell.find('.icp-inline-edit-value').text(value);
            }

            function save(cell) {
                var input = cell.find('.icp-inline-input');

                if (!input.length) {
                    return;
                }

                var value = input.val();
                var previous = cell.data('previous');

                if (String(value) === String(previous)) {
                    closeEditor(cell, previous);
                    return;
                }

                cell.addClass('is-saving');
                input.prop('disabled', true);

                $.ajax({
                    url: cell.data('url'),
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrf,
                        _method: 'PATCH',
                        field: cell.data('field'),
                        value: value
                    }
                }).done(function (response) {
                    closeEditor(cell, response.data.value);
                    toast(response.data.message || 'Saved.', 'success');
                    reload();
                }).fail(function (xhr) {
                    closeEditor(cell, previous);
                    toast(errorFrom(xhr, 'Could not save the change.'), 'danger');
                });
            }

            $(document)
                .on('click keydown', TABLE + ' .icp-inline-edit', function (e) {
                    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
                        return;
                    }

                    if ($(e.target).is('.icp-inline-input')) {
                        return;
                    }

                    e.preventDefault();
                    openEditor($(this));
                })
                .on('keydown', TABLE + ' .icp-inline-input', function (e) {
                    var cell = $(this).closest('.icp-inline-edit');

                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                        save(cell);
                    }

                    if (e.key === 'Escape') {
                        e.preventDefault();
                        e.stopPropagation();
                        closeEditor(cell, cell.data('previous'));
                    }
                })
                .on('focusout', TABLE + ' .icp-inline-input', function () {
                    var cell = $(this).closest('.icp-inline-edit');

                    // Enter already kicked off the save and disabled the input.
                    if (!cell.hasClass('is-saving')) {
                        save(cell);
                    }
                });

            // ── Row actions ───────────────────────────────────────────────────
            $(document).on('click', TABLE + ' .js-delete', function () {
                var button = $(this);

                if (!window.confirm('Delete the course "' + button.data('title') + '"? This cannot be undone.')) {
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
                    toast(errorFrom(xhr, 'Could not delete this course.'), 'danger');
                });
            });
        })(jQuery);
    </script>
@endpush
