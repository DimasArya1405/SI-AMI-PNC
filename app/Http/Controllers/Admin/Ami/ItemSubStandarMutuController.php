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
        $parentItems = ItemSubStandarMutu::where('sub_standar_id', $sub_standar_id)->get();
        return $dataTable->setSubStandarId($sub_standar_id)->render('admin.ami.item-sub-standar-mutu', compact('sub_standar', 'sub_standar_id', 'parentItems'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'sub_standar_id' => 'required|exists:sub_standar_mutu,sub_standar_id',
            'nama_item' => 'required|string',
            'parent_item_id' => 'nullable|exists:item_sub_standar,item_sub_standar_id',
        ]);

        $level = 1;
        $urutanBaru = 1;

        // kalau tambah anak item
        if ($request->parent_item_id) {
            $parent = ItemSubStandarMutu::findOrFail($request->parent_item_id);
            $level = ($parent->level ?? 1) + 1;

            // cari urutan anak terakhir dari parent
            $urutanAnakTerakhir = ItemSubStandarMutu::where('parent_item_id', $parent->item_sub_standar_id)
                ->max('urutan');

            if ($urutanAnakTerakhir) {
                $urutanBaru = $urutanAnakTerakhir + 1;
            } else {
                $urutanBaru = $parent->urutan + 1;
            }

            // geser item lain di bawah posisi sisipan
            ItemSubStandarMutu::where('sub_standar_id', $request->sub_standar_id)
                ->where('urutan', '>=', $urutanBaru)
                ->increment('urutan');
        } else {
            // item utama: taruh paling bawah
            $urutanTerakhir = ItemSubStandarMutu::where('sub_standar_id', $request->sub_standar_id)
                ->max('urutan');

            $urutanBaru = ($urutanTerakhir ?? 0) + 1;
        }

        $item = new ItemSubStandarMutu();
        $item->item_sub_standar_id = Str::uuid();
        $item->sub_standar_id = $request->sub_standar_id;
        $item->parent_item_id = $request->parent_item_id;
        $item->nama_item = $request->nama_item;
        $item->level = $level;
        $item->urutan = $urutanBaru;
        $item->save();

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
        $item_sub_standar_mutu = ItemSubStandarMutu::find($request->item_sub_standar_id);

        if (!$item_sub_standar_mutu) {
            return redirect()->back()->with('error', 'Data item tidak ditemukan');
        }
        ItemSubStandarMutu::where('parent_item_id', $item_sub_standar_mutu->item_sub_standar_id)->delete();
        $item_sub_standar_mutu->delete();
        return redirect()->back()->with('success', 'Item berhasil dihapus');
    }
}
