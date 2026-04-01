<?php

namespace App\Http\Controllers\Admin\Data;

use App\DataTables\Admin\Data\UPTDataTable;
use App\Http\Controllers\Controller;
use App\Models\UPT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UPTController extends Controller
{
    public function index(UPTDataTable $dataTable)
    {
        return $dataTable->render('admin.data.upt');
    }

    public function tambah(Request $request)
    {
        $upt = new UPT();
        $upt->upt_id = Str::uuid();
        $upt->kode_upt = $request->kode_upt;
        $upt->nama_upt = $request->nama_upt;
        $upt->kategori_upt = $request->kategori_upt;
        $upt->save();
        return redirect()->route('admin.data.upt')->with('success', 'UPT berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $upt = UPT::find($request->upt_id);
        $upt->kode_upt = $request->kode_upt;
        $upt->nama_upt = $request->nama_upt;
        $upt->kategori_upt = $request->kategori_upt;
        $upt->save();
        return redirect()->route('admin.data.upt')->with('success', 'UPT berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $upt = UPT::find($request->upt_id);
        $upt->delete();
        return redirect()->route('admin.data.upt')->with('success', 'UPT berhasil dihapus');
    }
}
