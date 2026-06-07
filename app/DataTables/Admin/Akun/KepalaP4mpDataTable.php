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
            ->editColumn('created_at', function (User $row) {
                return $row->created_at?->translatedFormat('d F Y H:i') ?? '-';
            })
            ->addColumn('action', function (User $row) {
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
            ->rawColumns(['action']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->select(['id', 'name', 'email', 'created_at'])
            ->where('role', 'kepala_p4mp');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('kepala-p4mp-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(3, 'desc')
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
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Tanggal Dibuat'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'Kepala_P4MP_' . date('YmdHis');
    }
}
