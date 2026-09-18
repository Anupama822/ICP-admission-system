<?php

namespace App\DataTables;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StudentDataTable extends DataTable
{
    protected array $excludeFromExport = ['action'];

    protected array $excludeFromPrint = ['action'];

    protected string $printPreview = 'admin.exports.print';

    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', fn (Student $row) => view('admin.students.partials.student-cell', [
                'model' => $row,
            ])->render())
            ->addColumn('course_title', fn (Student $row) => e($row->course?->title))
            ->addColumn('intake_title', fn (Student $row) => e($row->intake?->title))
            ->editColumn('created_at', fn (Student $row) => $row->created_at?->format('d M, Y'))
            ->addColumn('action', fn (Student $row) => view('admin.students.partials.actions', [
                'model' => $row,
                'route' => 'admin.students.',
            ])->render())
            ->filterColumn('name', function (EloquentBuilder $query, string $keyword): void {
                $query->where(function (EloquentBuilder $query) use ($keyword) {
                    $query->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email_1', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['name', 'action'])
            ->setRowId('id');
    }

    /**
     * Rooted on Student (the route-bound model behind every row's actions).
     * Course and intake are Student's own relations, but admission_id
     * only lives on enrollments, so that table is left-joined in to keep
     * it working as a plain, sortable/searchable column via the
     * "enrollments.admission_id" alias below, rather than needing Yajra's
     * relation-column support.
     */
    public function query(Student $model): EloquentBuilder
    {
        return $model->newQuery()
            ->select('students.*')
            ->addSelect('enrollments.admission_id as admission_id')
            ->leftJoin('enrollments', 'enrollments.student_id', '=', 'students.id')
            ->with(['course', 'intake']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('students-table')
            ->addTableClass('table align-middle w-100 icp-datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide()
            ->processing()
            ->responsive()
            ->autoWidth(false)
            ->orderBy(6, 'desc')
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
                'searchPlaceholder' => 'Search students...',
                'lengthMenu' => '_MENU_ / page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_',
                'infoEmpty' => 'No students to show',
                'zeroRecords' => 'No students match your search.',
                'emptyTable' => 'No students enrolled yet.',
                'processing' => '<div class="icp-dt-processing"><span class="spinner-border spinner-border-sm"></span> Loading...</div>',
                'paginate' => [
                    'previous' => '&laquo;',
                    'next' => '&raquo;',
                ],
            ])
            ->buttons(
                Button::make('reload')->className('icp-dt-btn')->text($this->buttonLabel('heroicon-m-arrow-path', 'Reload')),
            );
    }

    /**
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
            Column::make('admission_id')->title('Student ID'),
            Column::computed('name')->title('Student')->orderable(false),
            Column::computed('course_title')->title('Course')->orderable(false)->searchable(false),
            Column::computed('intake_title')->title('Intake')->orderable(false)->searchable(false),
            Column::make('mobile')->title('Mobile'),
            Column::make('created_at')->title('Enrolled On'),
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

    protected function filename(): string
    {
        return 'students_'.date('Y-m-d_His');
    }

    protected function buttonLabel(string $icon, string $label): string
    {
        return svg($icon, 'icp-dt-btn-icon')->toHtml().'<span>'.e($label).'</span>';
    }
}
