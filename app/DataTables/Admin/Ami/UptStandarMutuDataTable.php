<?php

namespace App\DataTables\Admin\Ami;

use App\Models\UptStandarMutu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class UptStandarMutuDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                        <a href="' . route('admin.upt_standar_mutu.detail', ['upt_id' => $row->upt_id, 'periode_id' => $row->periode_id]) . '"
                            class="hover:bg-blue-700 transition duration-300 ease-in-out py-1 px-2 bg-blue-500 rounded text-white">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                        <button type="button"
                            data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            class="hover:bg-yellow-700 button-edit transition duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white"
                            data-upt-id="' . $row->upt_id . '"
                            data-nama-upt="' . e($row->nama_upt) . '"
                            data-standar-ids="' . e($row->standar_mutu_ids) . '"
                            data-periode-id="' . $row->periode_id . '">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        <button type="button"
                            data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            class="hover:bg-red-700 button-hapus transition duration-300 ease-in-out py-1 px-2 bg-red-500 rounded text-white"
                            data-upt-id="' . $row->upt_id . '"
                            data-periode-id="' . $row->periode_id . '">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                        <a href="' . route('admin.upt_standar_mutu.export', [
                            'upt_id' => $row->upt_id,
                            'periode_id' => $row->periode_id
                                            ]) . '"
                            class="hover:bg-green-700 transition duration-300 ease-in-out py-1 px-2 bg-green-500 rounded text-white">
                                <i class="bi bi-file-earmark-excel text-xs"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action']);
    }

    public function query(UptStandarMutu $model): QueryBuilder
    {
        return $model->newQuery()
            ->join('upt', 'upt_standar_mutu.upt_id', '=', 'upt.upt_id')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->join('periode', 'upt_standar_mutu.periode_id', '=', 'periode.id')
            ->select(
                'upt_standar_mutu.upt_id',
                'upt.nama_upt',
                'periode.id as periode_id',
                'periode.tahun as periode_tahun',

                DB::raw("GROUP_CONCAT(DISTINCT standar_mutu.standar_mutu_id ORDER BY standar_mutu.nama_standar_mutu SEPARATOR ',') as standar_mutu_ids"),
                DB::raw("GROUP_CONCAT(DISTINCT standar_mutu.nama_standar_mutu ORDER BY standar_mutu.nama_standar_mutu SEPARATOR ', ') as nama_standar_mutu")
            )
            ->groupBy(
                'upt_standar_mutu.upt_id',
                'upt.nama_upt',
                'periode.id',
                'periode.tahun'
            );
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('auditee-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc')
            ->parameters([
                'responsive' => false,
                'autoWidth' => false,
            ]);
    }

    public function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama_upt', 'name' => 'upt.nama_upt', 'title' => 'Nama UPT'],
            ['data' => 'periode_tahun', 'name' => 'periode.tahun', 'title' => 'Periode'],
            ['data' => 'nama_standar_mutu', 'name' => 'nama_standar_mutu', 'title' => 'Nama Standar Mutu', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'UptStandarMutu_' . date('YmdHis');
    }
}
