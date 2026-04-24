<?php

namespace App\DataTables\Auditee;

use App\Models\Auditee;
use App\Models\StandarAMI;
use App\Models\UptStandarMutu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class StandarAMIDataTable extends DataTable
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
                    <a href="' . route('auditee.ami.detail', ['upt_id' => $row->upt_id, 'periode_id' => $row->periode_id]) . '"
                        class="hover:bg-blue-700 transition duration-300 ease-in-out py-1 px-2 bg-blue-500 rounded text-white">
                        <i class="bi bi-eye text-xs"></i>
                    </a>
                ';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(UptStandarMutu $model): QueryBuilder
    {
        $user = Auth::user();

        $auditee = Auditee::where('user_id', $user->id)->first();
        $uptId = $auditee?->upt_id;

        return $model->newQuery()
            ->join('upt', 'upt_standar_mutu.upt_id', '=', 'upt.upt_id')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->join('periode', 'upt_standar_mutu.periode_id', '=', 'periode.id')
            ->where('upt_standar_mutu.upt_id', $uptId)
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

    /**
     * Optional method if you want to use the html builder.
     */
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

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'periode_tahun', 'name' => 'periode.tahun', 'title' => 'Periode'],
            ['data' => 'nama_upt', 'name' => 'upt.nama_upt', 'title' => 'Nama UPT'],
            ['data' => 'nama_standar_mutu', 'name' => 'nama_standar_mutu', 'title' => 'Standar Mutu', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Detail', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'StandarAMI_' . date('YmdHis');
    }
}
