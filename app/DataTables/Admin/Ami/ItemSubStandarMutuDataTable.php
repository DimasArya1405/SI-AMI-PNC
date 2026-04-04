<?php

namespace App\DataTables\Admin\Ami;

use App\Models\ItemSubStandarMutu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class ItemSubStandarMutuDataTable extends DataTable
{
    protected $sub_standar_id;

    public function setSubStandarId($sub_standar_id)
    {
        $this->sub_standar_id = $sub_standar_id;
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
                        <button data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            class="hover:bg-yellow-700 button-edit transition duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white"
                            data-id="'.$row->item_sub_standar_id.'"
                            data-nama="'.$row->nama_item.'">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="'.$row->item_sub_standar_id.'"
                            class="hover:bg-red-700 transition button-hapus duration-300 ease-in-out py-1 px-2 bg-red-500 rounded text-white">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action']);
    }

    public function query(ItemSubStandarMutu $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('sub_standar_mutu')
            ->where('sub_standar_id', $this->sub_standar_id)
            ->select('item_sub_standar.*');
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
            ['data' => 'nama_item', 'name' => 'nama_item', 'title' => 'Pertanyaan dan Pernyataan'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'ItemSubStandarMutu_' . date('YmdHis');
    }
}