<?php

namespace App\DataTables\Admin;

use App\Models\Periode;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PeriodeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('status_aktif', function ($row) {
                if ($row->status == '1') {
                    return '<span class="bg-blue-200/80 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">Aktif</span>';
                } else {
                    return '<span class="bg-red-200/80 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Tidak Aktif</span>';
                }
            })
            ->editColumn('tahun', function ($row) {
                return $row->tahun;
            })
            ->addColumn('action', function ($row) {
                if (request()->routeIs('admin.periode')) {
                    return '
                    <div class="flex items-center gap-2">
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="' . $row->id . '"
                            class="hover:bg-red-700 transition button-hapus duration-300 ease-in-out py-1 px-2 bg-red-500 rounded text-white">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </div>
            ';
                }

                if (request()->routeIs('admin.ami.penugasan')) {
                    if($row->status == '1'){
                        return '
                        <div class="flex items-center gap-2">
                            <a href="' . route('admin.ami.penugasan.detail', $row->id) . '"
                                class="bg-yellow-500 hover:bg-yellow-600 transition duration-200 ease-in-out px-2 py-1 text-white rounded">
                                Buat Penugasan
                            </a>
                        </div>
                        ';
                }else{
                    return '<div class="text-red-500 text-sm">Periode tidak aktif</div>';
                }
                }
            })
            ->rawColumns(['action', 'status_aktif']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Periode $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('id', 'tahun', 'status')
            ->orderByDesc('tahun'); // opsional: tahun terbaru dulu
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('auditee-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->orderBy(2, 'desc')
            ->parameters([
                'responsive' => false,
                'autoWidth' => false,
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO',  'orderable' => false, 'searchable' => false],
            ['data' => 'tahun', 'name' => 'tahun', 'title' => 'Tahun'],
            ['data' => 'status_aktif', 'name' => 'status_aktif', 'title' => 'Status Aktif'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Periode_' . date('YmdHis');
    }
}
