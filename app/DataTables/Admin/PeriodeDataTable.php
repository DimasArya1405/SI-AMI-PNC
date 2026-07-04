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
            ->filterColumn('status_aktif', function ($query, $keyword) {
                $keyword = strtolower(trim($keyword));

                if ($keyword === '') {
                    return;
                }

                if (str_contains('aktif', $keyword)) {
                    $query->where('status', '1');
                    return;
                }

                if (str_contains('tidak aktif', $keyword) || str_contains('nonaktif', $keyword)) {
                    $query->where('status', '0');
                }
            })
            ->addColumn('action', function ($row) {
                if (request()->routeIs('admin.periode')) {
                    $statusButton = $row->status == '1'
                        ? '<button data-modal-target="modal-aktivasi"
                            data-modal-toggle="modal-aktivasi"
                            data-id="' . e($row->id) . '"
                            data-tahun="' . e($row->tahun) . '"
                            data-status="' . e($row->status) . '"
                            class="button-aktivasi rounded bg-orange-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-orange-700">
                            Nonaktifkan
                        </button>'
                        : '<button data-modal-target="modal-aktivasi"
                            data-modal-toggle="modal-aktivasi"
                            data-id="' . e($row->id) . '"
                            data-tahun="' . e($row->tahun) . '"
                            data-status="' . e($row->status) . '"
                            class="button-aktivasi rounded bg-blue-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-blue-700">
                            Aktifkan
                        </button>';

                    return '
                    <div class="flex items-center gap-2">
                        ' . $statusButton . '
                        <button data-modal-target="modal-edit"
                            data-modal-toggle="modal-edit"
                            data-id="' . e($row->id) . '"
                            data-tahun="' . e($row->tahun) . '"
                            class="button-edit rounded bg-yellow-500 px-2 py-1 text-white transition duration-300 ease-in-out hover:bg-yellow-700">
                            <i class="bi bi-pencil text-xs"></i>
                        </button>
                        <button data-modal-target="modal-hapus"
                            data-modal-toggle="modal-hapus"
                            data-id="' . e($row->id) . '"
                            data-tahun="' . e($row->tahun) . '"
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
                    return '
                        <div class="flex items-center gap-2">
                            <a href="' . route('admin.ami.penugasan.detail', $row->id) . '"
                                class="bg-blue-500 hover:bg-blue-600 transition duration-200 ease-in-out px-2 py-1 text-white rounded">
                                Lihat Jadwal
                            </a>
                        </div>
                    ';
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
            ['data' => 'status_aktif', 'name' => 'status', 'title' => 'Status Aktif'],
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
