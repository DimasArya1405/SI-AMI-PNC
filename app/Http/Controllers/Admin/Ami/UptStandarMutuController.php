<?php

namespace App\Http\Controllers\Admin\Ami;

use App\DataTables\Admin\Ami\UptStandarMutuDataTable;
use App\Http\Controllers\Controller;
use App\Models\StandarMutu;
use App\Models\UPT;
use App\Models\UptStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UptStandarMutuController extends Controller
{
    public function index(UptStandarMutuDataTable $dataTable)
    {
        $standarMutu = StandarMutu::all();
        $uptUnitBagian = UPT::where('kategori_upt', 'Unit/Bagian')->get();
        return $dataTable->render('admin.ami.upt-standar-mutu', compact('standarMutu', 'uptUnitBagian'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:all_prodi,unit_bagian',
            'standar_mutu_ids' => 'required|array|min:1',
            'standar_mutu_ids.*' => 'exists:standar_mutu,standar_mutu_id',
            'upt_ids' => 'nullable|array',
            'upt_ids.*' => 'exists:upt,upt_id',
        ]);

        if ($request->target_type == 'all_prodi') {
            $uptIds = Upt::where('kategori_upt', 'Prodi')->pluck('upt_id');
        } else {
            if (!$request->has('upt_ids') || count($request->upt_ids) == 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Pilih minimal satu Unit/Bagian');
            }

            $uptIds = $request->upt_ids;
        }

        foreach ($uptIds as $uptId) {
            foreach ($request->standar_mutu_ids as $standarMutuId) {
                $cek = UptStandarMutu::where('upt_id', $uptId)
                    ->where('standar_mutu_id', $standarMutuId)
                    ->first();

                if (!$cek) {
                    $uptStandarMutu = new UptStandarMutu();
                    $uptStandarMutu->upt_standar_mutu_id = Str::uuid();
                    $uptStandarMutu->upt_id = $uptId;
                    $uptStandarMutu->standar_mutu_id = $standarMutuId;
                    $uptStandarMutu->save();
                }
            }
        }

        return redirect()
            ->route('admin.ami.upt_standar_mutu')
            ->with('success', 'Pemetaan standar mutu berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        if (!$request->has('standar_mutu_ids')) {
            return redirect()->back()->with('error', 'Pilih minimal 1 standar mutu');
        }

        UptStandarMutu::where('upt_id', $request->upt_id)->delete();

        foreach ($request->standar_mutu_ids as $standar_mutu_id) {
            $uptStandarMutu = new UptStandarMutu();
            $uptStandarMutu->upt_standar_mutu_id = Str::uuid();
            $uptStandarMutu->upt_id = $request->upt_id;
            $uptStandarMutu->standar_mutu_id = $standar_mutu_id;
            $uptStandarMutu->save();
        }

        return redirect()->route('admin.ami.upt_standar_mutu')->with('success', 'Pemetaan standar berhasil diubah');
    }

    public function hapus(Request $request)
    {
        UptStandarMutu::where('upt_id', $request->upt_id)->delete();

        return redirect()->route('admin.ami.upt_standar_mutu')->with('success', 'Pemetaan standar berhasil dihapus');
    }

    public function detail($upt_id)
    {
        $upt = UPT::findOrFail($upt_id);

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->where('upt_id', $upt_id)
            ->get();

        return view('admin.ami.upt-sub-standar-mutu', compact('upt', 'pemetaanStandar'));
    }
}
