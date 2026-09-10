<?php

namespace App\DataTables;

use App\Models\AdmissionYear;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdmissionYearDataTable extends DataTable
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
            ->editColumn('title', fn (AdmissionYear $row) => view('admin.admissionYears.partials.inline-cell', [
                'model' => $row,
                'field' => 'title',
                'value' => $row->title,
                'type' => 'text',
            ])->render())
            ->editColumn('year', fn (AdmissionYear $row) => view('admin.admissionYears.partials.inline-cell', [
                'model' => $row,
                'field' => 'year',
                'value' => $row->year,
                'type' => 'number',
            ])->render())
            ->editColumn('is_active', fn (AdmissionYear $row) => view('admin.admissionYears.partials.status', [
                'model' => $row,
            ])->render())
            ->editColumn('created_at', fn (AdmissionYear $row) => $row->created_at?->format('d M, Y'))
            ->addColumn('action', fn (AdmissionYear $row) => view('admin.admissionYears.partials.actions', [
                'model' => $row,
                'route' => 'admission-year.',
            ])->render())
            // Let the global search box understand "active" / "inactive" as well
            // as the plain text columns.
            ->filterColumn('is_active', function (EloquentBuilder $query, string $keyword): void {
                $keyword = strtolower(trim($keyword));

                if ($keyword === '') {
                    return;
                }

                if (str_contains('inactive', $keyword)) {
                    $query->orWhere('is_active', false);
                }

                if (str_contains('active', $keyword)) {
                    $query->orWhere('is_active', true);
                }
            })
            ->rawColumns(['title', 'year', 'is_active', 'action'])
            ->setRowId('id');
    }

    /**
     * The base query the table is built from.
     */
    public function query(AdmissionYear $model): EloquentBuilder
    {
        return $model->newQuery();
    }

    /**
     * Configure the client side table.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('admission-years-table')
            ->addTableClass('table align-middle w-100 icp-datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide()
            ->processing()
            ->responsive()
            ->autoWidth(false)
            ->orderBy(4, 'desc')
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
                'searchPlaceholder' => 'Search admission years...',
                'lengthMenu' => '_MENU_ / page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_',
                'infoEmpty' => 'No admission years to show',
                'zeroRecords' => 'No admission years match your search.',
                'emptyTable' => 'No admission years found.',
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
            Column::make('year')->title('Admission Year'),
            Column::make('is_active')->title('Status'),
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
        return 'admission-years_'.date('Y-m-d_His');
    }

    /**
     * Render a blade icon next to a DataTables button label.
     */
    protected function buttonLabel(string $icon, string $label): string
    {
        return svg($icon, 'icp-dt-btn-icon')->toHtml().'<span>'.e($label).'</span>';
    }
}
