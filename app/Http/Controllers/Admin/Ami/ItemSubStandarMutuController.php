<?php

namespace App\Http\Controllers\Admin\Ami;

use App\DataTables\Admin\Ami\ItemSubStandarMutuDataTable;
use App\Http\Controllers\Controller;
use App\Models\ItemSubStandarMutu;
use App\Models\SubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemSubStandarMutuController extends Controller
{
    public function index(ItemSubStandarMutuDataTable $dataTable, $sub_standar_id)
    {
        $sub_standar = SubStandarMutu::with('standar_mutu')->findOrFail($sub_standar_id);
        return $dataTable->setSubStandarId($sub_standar_id)->render('admin.ami.item-sub-standar-mutu', compact('sub_standar', 'sub_standar_id'));
    }

    public function tambah(Request $request)
    {
        $item_sub_standar_mutu = new ItemSubStandarMutu();
        $item_sub_standar_mutu->item_sub_standar_id = Str::uuid();
        $item_sub_standar_mutu->sub_standar_id = $request->sub_standar_id;
        $item_sub_standar_mutu->nama_item = $request->nama_item;
        $item_sub_standar_mutu->save();
        return redirect()->back()->with('success', 'Item berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $item_sub_standar_mutu = ItemSubStandarMutu::find($request->item_sub_standar_id);
        $item_sub_standar_mutu->nama_item = $request->nama_item;
        $item_sub_standar_mutu->save();
        return redirect()->back()->with('success', 'Item berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $sub_standar_mutu = ItemSubStandarMutu::find($request->item_sub_standar_id);
        $sub_standar_mutu->delete();
        return redirect()->back()->with('success', 'Item berhasil dihapus');
    }
}
