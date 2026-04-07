<?php

namespace App\Http\Controllers\Admin\Ami;

use App\Http\Controllers\Controller;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UptSubStandarMutuController extends Controller
{
    public function tambah(Request $request)
    {
        $upt_sub = new UptSubStandarMutu();
        $upt_sub->upt_sub_standar_id = Str::uuid();
        $upt_sub->upt_id = $request->upt_id;
        $upt_sub->standar_mutu_id = $request->standar_mutu_id;
        $upt_sub->sub_standar_master_id = null;
        $upt_sub->nama_sub_standar = $request->nama_sub_standar;
        $upt_sub->save();

        return redirect()->back()->with('success', 'Sub standar berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $upt_sub = UptSubStandarMutu::findOrFail($request->upt_sub_standar_id);
        $upt_sub->nama_sub_standar = $request->nama_sub_standar;
        $upt_sub->save();

        return redirect()->back()->with('success', 'Sub standar berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $uptSubStandarId = $request->upt_sub_standar_id;

        // hapus semua item yang ada di sub standar ini
        UptItemSubStandarMutu::where('upt_sub_standar_id', $uptSubStandarId)->delete();

        // hapus sub standarnya
        UptSubStandarMutu::where('upt_sub_standar_id', $uptSubStandarId)->delete();

        return redirect()->back()->with('success', 'Sub standar berhasil dihapus');
    }
}
