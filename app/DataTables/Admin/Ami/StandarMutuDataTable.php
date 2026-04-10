<?php

namespace App\DataTables\Admin\Ami;

use App\Models\StandarMutu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class StandarMutuDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                        <a href="'.url('/admin/ami/standar-mutu/sub-standar-mutu/'.$row->standar_mutu_id).'"
                            class="hover:bg-blue-700 transition duration-300 ease-in-out py-1 px-2 bg-blue-500 rounded text-white">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                        <button data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            class="hover:bg-yellow-700 button-edit transition duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white"
                            data-id="'.$row->standar_mutu_id.'"
                            data-nama="'.$row->nama_standar_mutu.'">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="'.$row->standar_mutu_id.'"
                            class="hover:bg-red-700 transition button-hapus duration-300 ease-in-out py-1 px-2 bg-red-500 rounded text-white">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(StandarMutu $model): QueryBuilder
    {
        return $model->newQuery()->select(
            'standar_mutu.*'
        )
        ->orderBy('urutan', 'asc');
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
            ->orderBy(0, 'desc')
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
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama_standar_mutu', 'name' => 'nama_standar_mutu', 'title' => 'Nama Standar Mutu'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'StandarMutu_' . date('YmdHis');
    }
}
