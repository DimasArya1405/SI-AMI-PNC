<?php

namespace App\Http\Controllers\Admin\Ami;

use App\Http\Controllers\Controller;
use App\Models\UptItemSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UptItemSubStandarMutuController extends Controller
{
    public function tambah(Request $request)
    {
        $request->validate([
            'upt_id' => 'required|exists:upt,upt_id',
            'upt_sub_standar_id' => 'required|exists:upt_sub_standar_mutu,upt_sub_standar_id',
            'periode_id' => 'required|exists:periode,id',
            'nama_item' => 'required|string',
            'parent_upt_item_id' => 'nullable|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
        ]);

        $level = 1;
        $urutanBaru = 1;

        // kalau tambah anak item
        if ($request->parent_upt_item_id) {
            $parent = UptItemSubStandarMutu::findOrFail($request->parent_upt_item_id);
            $level = ($parent->level ?? 1) + 1;

            // cari urutan anak terakhir dari parent
            $urutanAnakTerakhir = UptItemSubStandarMutu::where('parent_upt_item_id', $parent->upt_item_sub_standar_id)
                ->where('periode_id', $request->periode_id)
                ->max('urutan');

            if ($urutanAnakTerakhir) {
                $urutanBaru = $urutanAnakTerakhir + 1;
            } else {
                $urutanBaru = $parent->urutan + 1;
            }

            // geser item lain di bawah posisi sisipan
            UptItemSubStandarMutu::where('upt_sub_standar_id', $request->upt_sub_standar_id)
                ->where('periode_id', $request->periode_id)
                ->where('urutan', '>=', $urutanBaru)
                ->increment('urutan');
        } else {
            // item utama: taruh paling bawah
            $urutanTerakhir = UptItemSubStandarMutu::where('upt_sub_standar_id', $request->upt_sub_standar_id)
                ->where('periode_id', $request->periode_id)
                ->max('urutan');

            $urutanBaru = ($urutanTerakhir ?? 0) + 1;
        }

        $uptItem = new UptItemSubStandarMutu();
        $uptItem->upt_item_sub_standar_id = Str::uuid();
        $uptItem->upt_id = $request->upt_id;
        $uptItem->upt_sub_standar_id = $request->upt_sub_standar_id;
        $uptItem->periode_id = $request->periode_id; // tambahkan ini
        $uptItem->parent_upt_item_id = $request->parent_upt_item_id;
        $uptItem->item_sub_standar_master_id = null;
        $uptItem->nama_item = $request->nama_item;
        $uptItem->level = $level;
        $uptItem->urutan = $urutanBaru;
        $uptItem->save();

        // return redirect()->back()->with('success', 'Item berhasil ditambahkan');
        return $this->redirectToPosition(
            $request,
            'Item berhasil ditambahkan.',
            'item-' . $uptItem->upt_item_sub_standar_id
        );
    }

    public function edit(Request $request)
    {
        $request->validate([
            'upt_item_sub_standar_id' => 'required|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
            'nama_item' => 'required|string',
        ]);

        $upt_item = UptItemSubStandarMutu::findOrFail($request->upt_item_sub_standar_id);
        $upt_item->nama_item = $request->nama_item;
        $upt_item->save();

        // return redirect()->back()->with('success', 'Item berhasil diubah');
        return $this->redirectToPosition(
            $request,
            'Item berhasil diubah.',
            'item-' . $upt_item->upt_item_sub_standar_id
        );
    }

    public function hapus(Request $request)
    {
        $request->validate([
            'upt_item_sub_standar_id' => 'required|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
        ]);

        $upt_item = UptItemSubStandarMutu::findOrFail($request->upt_item_sub_standar_id);

        // hapus anak-anaknya dulu
        UptItemSubStandarMutu::where('parent_upt_item_id', $upt_item->upt_item_sub_standar_id)->delete();

        // lalu hapus item utama
        $upt_item->delete();

        // return redirect()->back()->with('success', 'Item berhasil dihapus');
        return $this->redirectToPosition(
            $request,
            'Item berhasil dihapus.',
            $request->target_scroll
        );
    }

    private function redirectToPosition(Request $request, string $message, ?string $target = null)
    {
        $activeTab = $request->active_tab;
        $targetScroll = $target ?: $request->target_scroll;

        $url = url()->previous();

        if ($targetScroll) {
            $url .= '#' . $targetScroll;
        }

        return redirect()
            ->to($url)
            ->with('success', $message)
            ->with('active_tab', $activeTab)
            ->with('target_scroll', $targetScroll);
    }
}
