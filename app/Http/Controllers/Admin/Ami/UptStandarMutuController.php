<?php

namespace App\Http\Controllers\Admin\Ami;

use App\DataTables\Admin\Ami\UptStandarMutuDataTable;
use App\Http\Controllers\Controller;
use App\Imports\UptStandarMutuImport;
use App\Models\ItemSubStandarMutu;
use App\Models\Periode;
use App\Models\StandarMutu;
use App\Models\SubStandarMutu;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UptStandarMutuController extends Controller
{
    public function index(UptStandarMutuDataTable $dataTable)
    {
        $standarMutu = StandarMutu::all();
        $uptUnitBagian = UPT::where('kategori_upt', 'Unit/Bagian')->get();
        $periodeList = Periode::whereNull('deleted_at')->orderBy('tahun', 'desc')->get();

        return $dataTable->render('admin.ami.upt-standar-mutu', compact('standarMutu', 'uptUnitBagian', 'periodeList'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode,id',
            'target_type' => 'required|in:all_prodi,unit_bagian',
            'standar_mutu_ids' => 'required|array|min:1',
            'standar_mutu_ids.*' => 'exists:standar_mutu,standar_mutu_id',
            'upt_ids' => 'nullable|array',
            'upt_ids.*' => 'exists:upt,upt_id',
        ]);

        $uptIds = $this->getTargetUptIds($request);

        if (empty($uptIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu Unit/Bagian');
        }

        DB::transaction(function () use ($uptIds, $request) {
            foreach ($uptIds as $uptId) {
                foreach ($request->standar_mutu_ids as $standarMutuId) {
                    $this->sinkronisasiStandarDanTurunannya(
                        $uptId,
                        $standarMutuId,
                        $request->periode_id
                    );
                }
            }
        });

        return redirect()
            ->route('admin.ami.upt_standar_mutu')
            ->with('success', 'Pemetaan standar mutu berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'upt_id' => 'required|exists:upt,upt_id',
            'periode_id' => 'required|exists:periode,id',
            'standar_mutu_ids' => 'required|array|min:1',
            'standar_mutu_ids.*' => 'exists:standar_mutu,standar_mutu_id',
        ]);

        $uptId = $request->upt_id;
        $standarBaru = $request->standar_mutu_ids;

        DB::transaction(function () use ($uptId, $standarBaru, $request) {
            $standarLama = UptStandarMutu::where('upt_id', $uptId)
                ->where('periode_id', $request->periode_id)
                ->pluck('standar_mutu_id')
                ->toArray();

            $standarUntukDitambah = array_diff($standarBaru, $standarLama);
            $standarUntukDihapus = array_diff($standarLama, $standarBaru);

            foreach ($standarUntukDitambah as $standarMutuId) {
                $this->sinkronisasiStandarDanTurunannya($uptId, $standarMutuId, $request->periode_id);
            }

            foreach ($standarUntukDihapus as $standarMutuId) {
                $this->hapusStandarDanTurunannya($uptId, $standarMutuId, $request->periode_id);
            }
        });

        return redirect()
            ->route('admin.ami.upt_standar_mutu')
            ->with('success', 'Pemetaan standar berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $request->validate([
            'upt_id' => 'required|exists:upt,upt_id',
            'periode_id' => 'required|exists:periode,id',
        ]);

        DB::transaction(function () use ($request) {
            $uptId = $request->upt_id;
            $periodeId = $request->periode_id;

            UptItemSubStandarMutu::where('upt_id', $uptId)
                ->where('periode_id', $periodeId)
                ->delete();

            UptSubStandarMutu::where('upt_id', $uptId)
                ->where('periode_id', $periodeId)
                ->delete();

            UptStandarMutu::where('upt_id', $uptId)
                ->where('periode_id', $periodeId)
                ->delete();
        });

        return redirect()
            ->route('admin.ami.upt_standar_mutu')
            ->with('success', 'Pemetaan standar berhasil dihapus');
    }

    public function detail($upt_id, $periode_id)
    {
        $upt = UPT::findOrFail($upt_id);
        $periode = Periode::find($periode_id);

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->where('upt_standar_mutu.upt_id', $upt_id)
            ->where('upt_standar_mutu.periode_id', $periode_id)
            ->orderBy('standar_mutu.urutan', 'asc')
            ->select('upt_standar_mutu.*')
            ->get();

        $uptSubStandar = UptSubStandarMutu::with('standar_mutu')
            ->where('upt_id', $upt_id)
            ->where('periode_id', $periode_id)
            ->orderBy('urutan', 'asc')
            ->get();

        $uptItemSubStandar = UptItemSubStandarMutu::where('upt_id', $upt_id)
            ->where('periode_id', $periode_id)
            ->orderBy('urutan', 'asc')
            ->get()
            ->groupBy('upt_sub_standar_id');

        return view('admin.ami.upt-sub-standar-mutu', compact(
            'upt',
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'periode_id',
            'periode',
        ));
    }

    public function copyPeriode(Request $request)
    {
        $request->validate([
            'periode_sumber_id' => 'required|exists:periode,id',
            'periode_tujuan_id' => 'required|exists:periode,id|different:periode_sumber_id',
        ]);

        $periodeSumber = $request->periode_sumber_id;
        $periodeTujuan = $request->periode_tujuan_id;

        DB::transaction(function () use ($periodeSumber, $periodeTujuan) {

            // ambil semua mapping standar dari periode sumber
            $standarList = UptStandarMutu::where('periode_id', $periodeSumber)->get();

            foreach ($standarList as $standar) {
                $sudahAda = UptStandarMutu::where('upt_id', $standar->upt_id)
                    ->where('standar_mutu_id', $standar->standar_mutu_id)
                    ->where('periode_id', $periodeTujuan)
                    ->exists();

                if ($sudahAda) {
                    continue;
                }

                // copy header standar
                UptStandarMutu::create([
                    'upt_standar_mutu_id' => Str::uuid(),
                    'upt_id' => $standar->upt_id,
                    'standar_mutu_id' => $standar->standar_mutu_id,
                    'periode_id' => $periodeTujuan,
                ]);

                // ambil semua sub standar pada periode sumber
                $subStandarList = UptSubStandarMutu::where('upt_id', $standar->upt_id)
                    ->where('standar_mutu_id', $standar->standar_mutu_id)
                    ->where('periode_id', $periodeSumber)
                    ->orderBy('urutan', 'asc')
                    ->get();

                foreach ($subStandarList as $subStandar) {
                    $subBaru = UptSubStandarMutu::create([
                        'upt_sub_standar_id' => Str::uuid(),
                        'upt_id' => $subStandar->upt_id,
                        'standar_mutu_id' => $subStandar->standar_mutu_id,
                        'periode_id' => $periodeTujuan,
                        'sub_standar_master_id' => $subStandar->sub_standar_master_id,
                        'nama_sub_standar' => $subStandar->nama_sub_standar,
                        'urutan' => $subStandar->urutan,
                    ]);

                    $this->copyItemUptPeriode(
                        $subStandar->upt_id,
                        $subStandar->upt_sub_standar_id,
                        $subBaru->upt_sub_standar_id,
                        $periodeSumber,
                        $periodeTujuan
                    );
                }
            }
        });

        return redirect()->back()->with('success', 'Pemetaan berhasil disalin ke periode tujuan');
    }

    private function getTargetUptIds(Request $request): array
    {
        if ($request->target_type === 'all_prodi') {
            return UPT::where('kategori_upt', 'Prodi')
                ->pluck('upt_id')
                ->toArray();
        }

        return $request->upt_ids ?? [];
    }

    private function hapusStandarDanTurunannya(string $uptId, string $standarMutuId, string $periodeId): void
    {
        $uptSubStandarIds = UptSubStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->where('periode_id', $periodeId)
            ->pluck('upt_sub_standar_id');

        UptItemSubStandarMutu::whereIn('upt_sub_standar_id', $uptSubStandarIds)
            ->where('periode_id', $periodeId)
            ->delete();

        UptSubStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->where('periode_id', $periodeId)
            ->delete();

        UptStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->where('periode_id', $periodeId)
            ->delete();
    }

    private function copyItemUptPeriode(
        string $uptId,
        string $uptSubStandarSumberId,
        string $uptSubStandarTujuanId,
        string $periodeSumber,
        string $periodeTujuan
    ): void {
        $itemSumber = UptItemSubStandarMutu::where('upt_id', $uptId)
            ->where('upt_sub_standar_id', $uptSubStandarSumberId)
            ->where('periode_id', $periodeSumber)
            ->orderBy('urutan', 'asc')
            ->get();

        $mappingItem = [];

        // tahap 1: copy semua item dulu
        foreach ($itemSumber as $item) {
            $itemBaru = UptItemSubStandarMutu::create([
                'upt_item_sub_standar_id' => Str::uuid(),
                'upt_id' => $item->upt_id,
                'upt_sub_standar_id' => $uptSubStandarTujuanId,
                'periode_id' => $periodeTujuan,
                'item_sub_standar_master_id' => $item->item_sub_standar_master_id,
                'parent_upt_item_id' => null,
                'nama_item' => $item->nama_item,
                'level' => $item->level,
                'urutan' => $item->urutan,
            ]);

            $mappingItem[$item->upt_item_sub_standar_id] = $itemBaru->upt_item_sub_standar_id;
        }

        // tahap 2: hubungkan parent baru
        foreach ($itemSumber as $item) {
            if ($item->parent_upt_item_id && isset($mappingItem[$item->parent_upt_item_id])) {
                UptItemSubStandarMutu::where('upt_sub_standar_id', $uptSubStandarTujuanId)
                    ->where('periode_id', $periodeTujuan)
                    ->where('nama_item', $item->nama_item)
                    ->where('urutan', $item->urutan)
                    ->update([
                        'parent_upt_item_id' => $mappingItem[$item->parent_upt_item_id],
                    ]);
            }
        }
    }

    private function sinkronisasiItemUpt(string $uptId, string $uptSubStandarId, string $subStandarMasterId, string $periodeId): void
    {
        $itemMasterList = ItemSubStandarMutu::where('sub_standar_id', $subStandarMasterId)
            ->orderBy('urutan', 'asc')
            ->get();

        $mappingItemBaru = [];

        // tahap 1: tambahkan item yang belum ada
        foreach ($itemMasterList as $itemMaster) {
            $uptItem = UptItemSubStandarMutu::where('upt_id', $uptId)
                ->where('upt_sub_standar_id', $uptSubStandarId)
                ->where('periode_id', $periodeId)
                ->where('item_sub_standar_master_id', $itemMaster->item_sub_standar_id)
                ->first();

            if (!$uptItem) {
                $uptItem = UptItemSubStandarMutu::create([
                    'upt_item_sub_standar_id' => Str::uuid(),
                    'upt_id' => $uptId,
                    'upt_sub_standar_id' => $uptSubStandarId,
                    'periode_id' => $periodeId,
                    'item_sub_standar_master_id' => $itemMaster->item_sub_standar_id,
                    'parent_upt_item_id' => null,
                    'nama_item' => $itemMaster->nama_item,
                    'level' => $itemMaster->level ?? 1,
                    'urutan' => $itemMaster->urutan,
                ]);
            }

            $mappingItemBaru[$itemMaster->item_sub_standar_id] = $uptItem->upt_item_sub_standar_id;
        }

        // tahap 2: update parent hanya untuk item yang parentnya belum tersambung
        foreach ($itemMasterList as $itemMaster) {
            if ($itemMaster->parent_item_id && isset($mappingItemBaru[$itemMaster->parent_item_id])) {
                UptItemSubStandarMutu::where('upt_id', $uptId)
                    ->where('upt_sub_standar_id', $uptSubStandarId)
                    ->where('periode_id', $periodeId)
                    ->where('item_sub_standar_master_id', $itemMaster->item_sub_standar_id)
                    ->whereNull('parent_upt_item_id')
                    ->update([
                        'parent_upt_item_id' => $mappingItemBaru[$itemMaster->parent_item_id],
                    ]);
            }
        }
    }

    private function sinkronisasiStandarDanTurunannya(string $uptId, string $standarMutuId, string $periodeId): void
    {
        // 1. pastikan header standar ada
        $uptStandar = UptStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->where('periode_id', $periodeId)
            ->first();

        if (!$uptStandar) {
            UptStandarMutu::create([
                'upt_standar_mutu_id' => Str::uuid(),
                'upt_id' => $uptId,
                'standar_mutu_id' => $standarMutuId,
                'periode_id' => $periodeId,
            ]);
        }

        // 2. ambil semua sub standar master
        $subStandarList = SubStandarMutu::where('standar_mutu_id', $standarMutuId)
            ->orderBy('urutan', 'asc')
            ->get();

        foreach ($subStandarList as $subStandar) {
            // cek sub standar di mapping UPT
            $uptSubStandar = UptSubStandarMutu::where('upt_id', $uptId)
                ->where('standar_mutu_id', $standarMutuId)
                ->where('periode_id', $periodeId)
                ->where('sub_standar_master_id', $subStandar->sub_standar_id)
                ->first();

            if (!$uptSubStandar) {
                $uptSubStandar = UptSubStandarMutu::create([
                    'upt_sub_standar_id' => Str::uuid(),
                    'upt_id' => $uptId,
                    'standar_mutu_id' => $standarMutuId,
                    'periode_id' => $periodeId,
                    'sub_standar_master_id' => $subStandar->sub_standar_id,
                    'nama_sub_standar' => $subStandar->nama_sub_standar,
                    'urutan' => $subStandar->urutan,
                ]);
            }

            // sinkron item per sub standar
            $this->sinkronisasiItemUpt(
                $uptId,
                $uptSubStandar->upt_sub_standar_id,
                $subStandar->sub_standar_id,
                $periodeId
            );
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode,id',
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(
            new UptStandarMutuImport($request->periode_id),
            $request->file('file')
        );

        return redirect()
            ->back()
            ->with('success', 'Data pemetaan berhasil diimport.');
    }
}
