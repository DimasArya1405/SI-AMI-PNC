<?php

namespace App\DataTables\Admin\Ami;

use App\Models\SubStandarMutu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class SubStandarMutuDataTable extends DataTable
{
    protected $standar_mutu_id;

    public function setStandarMutuId($standar_mutu_id)
    {
        $this->standar_mutu_id = $standar_mutu_id;
        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                        <a href="' . url('/admin/ami/standar-mutu/sub-standar-mutu/item/' . $row->sub_standar_id) . '"
                            class="hover:bg-blue-700 transition duration-300 ease-in-out py-1 px-2 bg-blue-500 rounded text-white">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                        <button data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            class="hover:bg-yellow-700 button-edit transition duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white"
                            data-id="' . $row->sub_standar_id . '"
                            data-nama="' . $row->nama_sub_standar . '">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="' . $row->sub_standar_id . '"
                            class="hover:bg-red-700 transition button-hapus duration-300 ease-in-out py-1 px-2 bg-red-500 rounded text-white">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action']);
    }

    public function query(SubStandarMutu $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('standar_mutu')
            ->where('standar_mutu_id', $this->standar_mutu_id)
            ->orderBy('urutan', 'asc')
            ->select('sub_standar_mutu.*');
    }

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

    public function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama_sub_standar', 'name' => 'nama_sub_standar', 'title' => 'Nama Sub Standar Mutu'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'SubStandarMutu_' . date('YmdHis');
    }
}