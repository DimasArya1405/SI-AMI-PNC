<?php

namespace App\DataTables\Admin\Akun;

use App\Models\Auditee;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AuditeeDataTable extends DataTable
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
            ->editColumn('status_aktif', function ($row) {
                if ($row->status_aktif) {
                    return '<span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Aktif</span>';
                }

                return '<span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <button class="text-blue-500">Edit</button>
                <button class="text-red-500">Delete</button>
                ';
            })
            ->rawColumns(['status_aktif', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Auditee $model): QueryBuilder
    {
        return $model->newQuery()->select([
            'nip',
            'nama_lengkap',
            'jabatan',
            'no_telp',
            'email',
            'status_aktif'
        ]);
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
            ['data' => 'nip', 'name' => 'nip', 'title' => 'NIP'],
            ['data' => 'nama_lengkap', 'name' => 'nama_lengkap', 'title' => 'Nama Lengkap'],
            ['data' => 'jabatan', 'name' => 'jabatan', 'title' => 'Jabatan'],
            ['data' => 'no_telp', 'name' => 'no_telp', 'title' => 'No Telp'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'status_aktif', 'name' => 'status_aktif', 'title' => 'Status Aktif'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Auditee_' . date('YmdHis');
    }
}
