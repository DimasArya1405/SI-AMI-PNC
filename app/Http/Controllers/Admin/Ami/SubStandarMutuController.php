<?php

namespace App\Http\Controllers\Admin\Ami;

use App\DataTables\Admin\Ami\SubStandarMutuDataTable;
use App\Http\Controllers\Controller;
use App\Models\StandarMutu;
use App\Models\SubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubStandarMutuController extends Controller
{
    public function index(SubStandarMutuDataTable $dataTable, $standar_mutu_id)
    {
        $standar = StandarMutu::findOrFail($standar_mutu_id);
        return $dataTable->setStandarMutuId($standar_mutu_id)->render('admin.ami.sub-standar-mutu', compact('standar_mutu_id', 'standar'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'standar_mutu_id' => 'required|exists:standar_mutu,standar_mutu_id',
            'nama_sub_standar_mutu' => 'required|string'
        ]);

        // cari urutan terakhir
        $urutanTerakhir = SubStandarMutu::where('standar_mutu_id', $request->standar_mutu_id)
            ->max('urutan');

        $sub_standar_mutu = new SubStandarMutu();
        $sub_standar_mutu->sub_standar_id = Str::uuid();
        $sub_standar_mutu->standar_mutu_id = $request->standar_mutu_id;
        $sub_standar_mutu->nama_sub_standar = $request->nama_sub_standar_mutu;
        $sub_standar_mutu->urutan = ($urutanTerakhir ?? 0) + 1;
        $sub_standar_mutu->save();

        return redirect()
            ->route('admin.ami.sub_standar_mutu', $request->standar_mutu_id)
            ->with('success', 'Sub Standar Mutu berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $sub_standar_mutu = SubStandarMutu::find($request->sub_standar_id);
        $sub_standar_mutu->nama_sub_standar = $request->nama_sub_standar_mutu;
        $sub_standar_mutu->save();
        return redirect()->route('admin.ami.sub_standar_mutu', $request->standar_mutu_id)->with('success', 'Sub Standar Mutu berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $sub_standar_mutu = SubStandarMutu::find($request->sub_standar_id);
        $sub_standar_mutu->delete();
        return redirect()->route('admin.ami.sub_standar_mutu', $request->standar_mutu_id)->with('success', 'Sub Standar Mutu berhasil dihapus');
    }
}
