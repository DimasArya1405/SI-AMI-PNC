<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\PeriodeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index(PeriodeDataTable $dataTable)
    {
        $periode = Periode::all();
        return $dataTable->render('admin.periode', compact('periode'));
    }

    public function tambah(Request $request)
    {
        Periode::query()->update(['status' => '0']);

        $periode = new Periode();
        $periode->tahun = $request->tahun;
        $periode->status = '1';
        $periode->save();
        return redirect()->back()->with('success', 'Periode berhasil ditambahkan');
    }

    public function hapus(Request $request)
    {
        $periode = Periode::find($request->periode_id);
        $periode->delete();
        return redirect()->back()->with('success', 'Periode berhasil dihapus');
    }
}
