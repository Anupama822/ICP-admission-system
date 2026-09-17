@extends('admin.templates.index')

@section('title', $title)

@section('index_content')

    <form id="add-document-type-form" class="d-flex flex-wrap gap-2 mb-4">
        @csrf
        <input type="text" name="name" id="new-document-type-name" placeholder="e.g. NEB" required maxlength="100"
            class="form-control icp-input" style="max-width:280px">
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
            @svg('heroicon-m-plus', 'icp-icon-sm')
            <span>Add</span>
        </button>
    </form>

    <p id="document-types-empty" class="small text-muted mb-0" @if($items->isNotEmpty()) style="display:none" @endif>
        No educational boards yet &mdash; add one above to offer it on the student enrollment form.
    </p>

    <div class="table-responsive" id="document-types-table-wrapper" @if($items->isEmpty()) style="display:none" @endif>
        <table class="table icp-table mb-0" id="document-types-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Format example <span class="text-muted fw-normal">(shown on the student form)</span></th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="document-types-tbody">
                @foreach($items as $item)
                    @include('admin.document-types.partials.row')
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="icp-page-subtitle small mb-0 mt-3 d-flex align-items-center gap-2">
        @svg('heroicon-m-information-circle', 'icp-icon-sm')
        <span>Click a name to edit it right here &mdash; <kbd>Enter</kbd> saves, <kbd>Esc</kbd> cancels.</span>
    </p>

@endsection

@push('scripts')
    <script>
        (function ($) {
            'use strict';

            var csrf = $('meta[name="csrf-token"]').attr('content');
            var $tbody = $('#document-types-tbody');
            var $wrapper = $('#document-types-table-wrapper');
            var $empty = $('#document-types-empty');

            // ── Toasts ───────────────────────────────────────────────────────
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

            // ── Add ──────────────────────────────────────────────────────────
            $('#add-document-type-form').on('submit', function (e) {
                e.preventDefault();

                var $input = $('#new-document-type-name');
                var name = $input.val().trim();
                if (!name) { return; }

                $.ajax({
                    url: '{{ route('admin.document-types.store') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: { _token: csrf, name: name }
                }).done(function (response) {
                    $tbody.append(response.data.row);

                    $input.val('');
                    $wrapper.show();
                    $empty.hide();
                    toast(response.data.message || 'Added.', 'success');
                }).fail(function (xhr) {
                    toast(errorFrom(xhr, 'Could not add this educational board.'), 'danger');
                });
            });

            // ── Inline editing ───────────────────────────────────────────────
            // The cell keeps its rendered markup; the editor is only an extra
            // input appended while `is-editing` is on.
            function openEditor(cell) {
                if (cell.hasClass('is-editing')) {
                    return;
                }

                var current = cell.find('.icp-inline-edit-value').text().trim();

                cell.addClass('is-editing').data('previous', current);

                $('<input>', {
                    type: 'text',
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
                    if (cell.data('field') === 'name') {
                        cell.closest('tr').find('.js-delete').attr('data-name', response.data.value);
                    }
                    toast(response.data.message || 'Saved.', 'success');
                }).fail(function (xhr) {
                    closeEditor(cell, previous);
                    toast(errorFrom(xhr, 'Could not save the change.'), 'danger');
                });
            }

            $(document)
                .on('click keydown', '#document-types-table .icp-inline-edit', function (e) {
                    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
                        return;
                    }

                    if ($(e.target).is('.icp-inline-input')) {
                        return;
                    }

                    e.preventDefault();
                    openEditor($(this));
                })
                .on('keydown', '#document-types-table .icp-inline-input', function (e) {
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
                .on('focusout', '#document-types-table .icp-inline-input', function () {
                    var cell = $(this).closest('.icp-inline-edit');

                    if (!cell.hasClass('is-saving')) {
                        save(cell);
                    }
                });

            // ── Delete ───────────────────────────────────────────────────────
            $(document).on('click', '#document-types-table .js-delete', function () {
                var button = $(this);

                if (!window.confirm('Delete the educational board "' + button.data('name') + '"? This cannot be undone.')) {
                    return;
                }

                $.ajax({
                    url: button.data('url'),
                    method: 'POST',
                    dataType: 'json',
                    data: { _token: csrf, _method: 'DELETE' }
                }).done(function (response) {
                    button.closest('tr').remove();
                    if (!$tbody.children().length) {
                        $wrapper.hide();
                        $empty.show();
                    }
                    toast(response.data.message, 'success');
                }).fail(function (xhr) {
                    toast(errorFrom(xhr, 'Could not delete this educational board.'), 'danger');
                });
            });
        })(jQuery);
    </script>
@endpush
