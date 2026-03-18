<?php

namespace App\DataTables\Admin\Akun;

use App\Models\Dosen;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DosenDataTable extends DataTable
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
                    return '<span class="bg-blue-200/80 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">Aktif</span>';
                } else {
                    return '<span class="bg-red-200/80 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Tidak Aktif</span>';
                }
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                        <button data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            class="hover:bg-yellow-700 button-edit transition duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white"
                            data-id="'.$row->dosen_id.'"
                            data-nip="'.$row->nip.'"
                            data-nama="'.$row->nama_lengkap.'"
                            data-jabatan="'.$row->jabatan.'"
                            data-prodi="'.$row->prodi_id.'"
                            data-email="'.$row->email.'"
                            data-no_telp="'.$row->no_telp.'">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="'.$row->dosen_id.'"
                            data-email="'.$row->email.'"
                            class="hover:bg-red-700 transition button-hapus duration-300 ease-in-out py-1 px-2 bg-red-500 rounded text-white">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status_aktif', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Dosen $model): QueryBuilder
    {
        return $model->newQuery()->select(
            'dosen_id',
            'prodi_id',
            'nip',
            'nama_lengkap',
            'jabatan',
            'no_telp',
            'email',
            'status_aktif',
        );
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
        return 'Dosen_' . date('YmdHis');
    }
}
