<?php

namespace App\DataTables;

use App\Models\Course;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CourseDataTable extends DataTable
{
    /**
     * The action column holds buttons and forms, which are meaningless in an
     * export or a print preview.
     */
    protected array $excludeFromExport = ['action'];

    protected array $excludeFromPrint = ['action'];

    /** Branded replacement for the package's default print preview. */
    protected string $printPreview = 'admin.exports.print';

    /**
     * Build the DataTable response for the ajax request.
     */
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('title', fn (Course $row) => view('admin.courses.partials.inline-cell', [
                'model' => $row,
                'field' => 'title',
                'value' => $row->title,
                'type' => 'text',
            ])->render())
            ->editColumn('display_title', fn (Course $row) => e($row->displayTitle()))
            ->addColumn('levels', fn (Course $row) => $row->levels ? e(implode(', ', $row->levels)) : '—')
            ->editColumn('credits', fn (Course $row) => view('admin.courses.partials.inline-cell', [
                'model' => $row,
                'field' => 'credits',
                'value' => $row->credits === null ? '' : rtrim(rtrim((string) $row->credits, '0'), '.'),
                'type' => 'number',
            ])->render())
            ->editColumn('description', fn (Course $row) => view('admin.courses.partials.inline-cell', [
                'model' => $row,
                'field' => 'description',
                'value' => (string) $row->description,
                'type' => 'text',
            ])->render())
            ->editColumn('created_at', fn (Course $row) => $row->created_at?->format('d M, Y'))
            ->addColumn('action', fn (Course $row) => view('admin.courses.partials.actions', [
                'model' => $row,
                'route' => 'admin.courses.',
            ])->render())
            ->rawColumns(['title', 'credits', 'description', 'action'])
            ->setRowId('id');
    }

    /**
     * The base query the table is built from.
     */
    public function query(Course $model): EloquentBuilder
    {
        return $model->newQuery();
    }

    /**
     * Configure the client side table.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('courses-table')
            ->addTableClass('table align-middle w-100 icp-datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide()
            ->processing()
            ->responsive()
            ->autoWidth(false)
            ->orderBy(1, 'asc')
            ->pageLength(10)
            ->lengthMenu([[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']])
            ->dom(
                '<"icp-dt-top d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"'.
                '<"d-flex flex-wrap align-items-center gap-2"lf>'.
                '<"d-flex flex-wrap align-items-center gap-2"B>'.
                '>'.
                'rt'.
                '<"icp-dt-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"ip>'
            )
            ->language([
                'search' => '',
                'searchPlaceholder' => 'Search courses...',
                'lengthMenu' => '_MENU_ / page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_',
                'infoEmpty' => 'No courses to show',
                'zeroRecords' => 'No courses match your search.',
                'emptyTable' => 'No courses found.',
                'processing' => '<div class="icp-dt-processing"><span class="spinner-border spinner-border-sm"></span> Loading...</div>',
                'paginate' => [
                    'previous' => '&laquo;',
                    'next' => '&raquo;',
                ],
            ])
            ->buttons(
                // Button::make('csv')->className('icp-dt-btn')->text($this->buttonLabel('heroicon-m-document-text', 'CSV')),
                // Button::make('pdf')->className('icp-dt-btn')->text($this->buttonLabel('heroicon-m-document-arrow-down', 'PDF')),
                // Button::make('excel')->className('icp-dt-btn')->text($this->buttonLabel('heroicon-m-table-cells', 'Excel')),
                // Button::make('print')->className('icp-dt-btn')->text($this->buttonLabel('heroicon-m-printer', 'Print')),
                Button::make('reload')->className('icp-dt-btn')->text($this->buttonLabel('heroicon-m-arrow-path', 'Reload')),
            );
    }

    /**
     * Column definitions.
     *
     * @return array<int, Column>
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->width(50)
                ->searchable(false)
                ->orderable(false)
                ->addClass('text-center'),
            Column::make('title')->title('Title'),
            Column::make('display_title')->title('Display Title'),
            Column::computed('levels')->title('Levels')->searchable(false)->orderable(false),
            Column::make('credits')->title('Credits'),
            Column::make('description')->title('Description'),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->width(150)
                ->addClass('text-end'),
        ];
    }

    /**
     * Filename used by the csv / excel / pdf exports.
     */
    protected function filename(): string
    {
        return 'courses_'.date('Y-m-d_His');
    }

    /**
     * Render a blade icon next to a DataTables button label.
     */
    protected function buttonLabel(string $icon, string $label): string
    {
        return svg($icon, 'icp-dt-btn-icon')->toHtml().'<span>'.e($label).'</span>';
    }
}
