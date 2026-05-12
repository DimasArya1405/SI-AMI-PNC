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
        $request->validate([
            'upt_standar_mutu_id' => 'required|exists:upt_standar_mutu,upt_standar_mutu_id',
            'nama_sub_standar' => 'required|string|max:255',
        ]);

        $upt_sub = new UptSubStandarMutu();
        $upt_sub->upt_sub_standar_id = (string) Str::uuid();
        $upt_sub->upt_standar_mutu_id = $request->upt_standar_mutu_id;
        $upt_sub->sub_standar_master_id = null;
        $upt_sub->nama_sub_standar = $request->nama_sub_standar;
        $upt_sub->save();

        return $this->redirectToPosition(
            $request,
            'Sub standar berhasil ditambahkan.',
            'sub-' . $upt_sub->upt_sub_standar_id
        );
    }

    public function edit(Request $request)
    {
        $request->validate([
            'upt_sub_standar_id' => 'required|exists:upt_sub_standar_mutu,upt_sub_standar_id',
            'nama_sub_standar' => 'required|string|max:255',
        ]);

        $upt_sub = UptSubStandarMutu::findOrFail($request->upt_sub_standar_id);
        $upt_sub->nama_sub_standar = $request->nama_sub_standar;
        $upt_sub->save();

        return $this->redirectToPosition(
            $request,
            'Sub standar berhasil diubah.',
            'sub-' . $upt_sub->upt_sub_standar_id
        );
    }

    public function hapus(Request $request)
    {
        $request->validate([
            'upt_sub_standar_id' => 'required|exists:upt_sub_standar_mutu,upt_sub_standar_id',
        ]);

        $uptSubStandarId = $request->upt_sub_standar_id;

        UptItemSubStandarMutu::where('upt_sub_standar_id', $uptSubStandarId)->delete();

        UptSubStandarMutu::where('upt_sub_standar_id', $uptSubStandarId)->delete();

        return $this->redirectToPosition(
            $request,
            'Sub standar berhasil dihapus.',
            $request->target_scroll ?: $request->active_tab
        );
    }

    private function redirectToPosition(Request $request, string $message, ?string $target = null)
    {
        $activeTab = $request->active_tab;
        $targetScroll = $target ?: $request->target_scroll;
        $openAccordion = $request->open_accordion;

        $url = url()->previous();

        if ($targetScroll) {
            $url .= '#' . $targetScroll;
        }

        return redirect()
            ->to($url)
            ->with('success', $message)
            ->with('active_tab', $activeTab)
            ->with('target_scroll', $targetScroll)
            ->with('open_accordion', $openAccordion);
    }
}