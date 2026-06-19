<?php

namespace App\DataTables\Admin\Akun;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class KepalaP4mpDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('status_aktif', function (User $row) {
                if ($row->status_aktif) {
                    return '<span class="bg-blue-200/80 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">Aktif</span>';
                }

                return '<span class="bg-red-200/80 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Tidak Aktif</span>';
            })
            ->addColumn('action', function (User $row) {
                $statusButton = $row->status_aktif ? '
                        <button data-modal-target="modal-aktivasi"
                            data-modal-toggle="modal-aktivasi"
                            data-id="' . e($row->id) . '"
                            data-status="' . e($row->status_aktif) . '"
                            data-name="' . e($row->name) . '"
                            class="button-aktivasi rounded bg-orange-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-orange-700">
                            Nonaktifkan
                        </button>
                ' : '
                        <button data-modal-target="modal-aktivasi"
                            data-modal-toggle="modal-aktivasi"
                            data-id="' . e($row->id) . '"
                            data-status="' . e($row->status_aktif) . '"
                            data-name="' . e($row->name) . '"
                            class="button-aktivasi rounded bg-blue-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-blue-700">
                            Aktifkan
                        </button>
                ';

                return '
                    <div class="flex items-center gap-2">
                        <button data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            class="button-edit rounded bg-yellow-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-yellow-700"
                            data-id="' . e($row->id) . '"
                            data-name="' . e($row->name) . '"
                            data-email="' . e($row->email) . '">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        ' . $statusButton . '
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="' . e($row->id) . '"
                            data-name="' . e($row->name) . '"
                            class="button-hapus rounded bg-red-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-red-700">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status_aktif', 'action']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->select(['id', 'name', 'email', 'status_aktif'])
            ->where('role', 'kepala_p4mp');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('kepala-p4mp-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->parameters([
                'responsive' => false,
                'autoWidth' => false,
            ]);
    }

    public function getColumns(): array
    {
        return [
            ['data' => 'name', 'name' => 'name', 'title' => 'Nama'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'status_aktif', 'name' => 'status_aktif', 'title' => 'Status Aktif'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'Kepala_P4MP_' . date('YmdHis');
    }
}
