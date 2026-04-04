<?php

namespace App\Http\Controllers\Admin\Ami;

use App\DataTables\Admin\Ami\StandarMutuDataTable;
use App\Http\Controllers\Controller;
use App\Models\StandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StandarMutuController extends Controller
{
    public function index(StandarMutuDataTable $dataTable)
    {
        return $dataTable->render('admin.ami.standar-mutu');
    }

    public function tambah(Request $request)
    {
        $standar_mutu = new StandarMutu();
        $standar_mutu->standar_mutu_id = Str::uuid();
        $standar_mutu->nama_standar_mutu = $request->nama_standar_mutu;
        $standar_mutu->save();
        return redirect()->route('admin.ami.standar_mutu')->with('success', 'Standar Mutu berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $standar_mutu = StandarMutu::find($request->standar_mutu_id);
        $standar_mutu->nama_standar_mutu = $request->nama_standar_mutu;
        $standar_mutu->save();
        return redirect()->route('admin.ami.standar_mutu')->with('success', 'Standar Mutu berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $standar_mutu = StandarMutu::find($request->standar_mutu_id);
        $standar_mutu->delete();
        return redirect()->route('admin.ami.standar_mutu')->with('success', 'Standar Mutu berhasil dihapus');
    }
}
