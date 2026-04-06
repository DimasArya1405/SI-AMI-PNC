<?php

namespace App\Http\Controllers\Admin\Ami;

use App\DataTables\Admin\Ami\UptStandarMutuDataTable;
use App\Http\Controllers\Controller;
use App\Models\ItemSubStandarMutu;
use App\Models\StandarMutu;
use App\Models\SubStandarMutu;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $uptIds = $this->getTargetUptIds($request);

        if (empty($uptIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu Unit/Bagian');
        }

        DB::transaction(function () use ($uptIds, $request) {
            foreach ($uptIds as $uptId) {
                foreach ($request->standar_mutu_ids as $standarMutuId) {
                    $sudahAda = UptStandarMutu::where('upt_id', $uptId)
                        ->where('standar_mutu_id', $standarMutuId)
                        ->exists();

                    if (!$sudahAda) {
                        $this->simpanStandarDanTurunannya($uptId, $standarMutuId);
                    }
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
            'standar_mutu_ids' => 'required|array|min:1',
            'standar_mutu_ids.*' => 'exists:standar_mutu,standar_mutu_id',
        ]);

        $uptId = $request->upt_id;
        $standarBaru = $request->standar_mutu_ids;

        DB::transaction(function () use ($uptId, $standarBaru) {
            $standarLama = UptStandarMutu::where('upt_id', $uptId)
                ->pluck('standar_mutu_id')
                ->toArray();

            $standarUntukDitambah = array_diff($standarBaru, $standarLama);
            $standarUntukDihapus = array_diff($standarLama, $standarBaru);

            foreach ($standarUntukDitambah as $standarMutuId) {
                $this->simpanStandarDanTurunannya($uptId, $standarMutuId);
            }

            foreach ($standarUntukDihapus as $standarMutuId) {
                $this->hapusStandarDanTurunannya($uptId, $standarMutuId);
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
        ]);

        DB::transaction(function () use ($request) {
            $uptId = $request->upt_id;

            UptItemSubStandarMutu::where('upt_id', $uptId)->delete();
            UptSubStandarMutu::where('upt_id', $uptId)->delete();
            UptStandarMutu::where('upt_id', $uptId)->delete();
        });

        return redirect()
            ->route('admin.ami.upt_standar_mutu')
            ->with('success', 'Pemetaan standar berhasil dihapus');
    }

    public function detail($upt_id)
    {
        $upt = UPT::findOrFail($upt_id);

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->where('upt_id', $upt_id)
            ->get();

        $uptSubStandar = UptSubStandarMutu::with('standar_mutu')
            ->where('upt_id', $upt_id)
            ->get();

        $uptItemSubStandar = UptItemSubStandarMutu::where('upt_id', $upt_id)
            ->orderBy('urutan', 'asc')
            ->get()
            ->groupBy('upt_sub_standar_id');

        return view('admin.ami.upt-sub-standar-mutu', compact(
            'upt',
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar'
        ));
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

    private function simpanStandarDanTurunannya(string $uptId, string $standarMutuId): void
    {
        UptStandarMutu::create([
            'upt_standar_mutu_id' => Str::uuid(),
            'upt_id' => $uptId,
            'standar_mutu_id' => $standarMutuId,
        ]);

        $subStandarList = SubStandarMutu::where('standar_mutu_id', $standarMutuId)
            ->orderBy('urutan', 'asc')
            ->get();

        foreach ($subStandarList as $subStandar) {
            $uptSubStandar = UptSubStandarMutu::create([
                'upt_sub_standar_id' => Str::uuid(),
                'upt_id' => $uptId,
                'standar_mutu_id' => $standarMutuId,
                'sub_standar_master_id' => $subStandar->sub_standar_id,
                'nama_sub_standar' => $subStandar->nama_sub_standar,
                'urutan' => $subStandar->urutan,
            ]);

            $this->generateItemUpt(
                $uptId,
                $uptSubStandar->upt_sub_standar_id,
                $subStandar->sub_standar_id
            );
        }
    }

    private function generateItemUpt(string $uptId, string $uptSubStandarId, string $subStandarMasterId): void
    {
        $itemList = ItemSubStandarMutu::where('sub_standar_id', $subStandarMasterId)
            ->orderBy('urutan', 'asc')
            ->get();

        $mappingItem = [];

        foreach ($itemList as $item) {
            $uptItem = UptItemSubStandarMutu::create([
                'upt_item_sub_standar_id' => Str::uuid(),
                'upt_id' => $uptId,
                'upt_sub_standar_id' => $uptSubStandarId,
                'item_sub_standar_master_id' => $item->item_sub_standar_id,
                'parent_upt_item_id' => null,
                'nama_item' => $item->nama_item,
                'level' => $item->level ?? 1,
                'urutan' => $item->urutan, // ini yang penting
            ]);

            $mappingItem[$item->item_sub_standar_id] = $uptItem->upt_item_sub_standar_id;
        }

        foreach ($itemList as $item) {
            if ($item->parent_item_id && isset($mappingItem[$item->parent_item_id])) {
                UptItemSubStandarMutu::where('upt_sub_standar_id', $uptSubStandarId)
                    ->where('item_sub_standar_master_id', $item->item_sub_standar_id)
                    ->update([
                        'parent_upt_item_id' => $mappingItem[$item->parent_item_id],
                    ]);
            }
        }
    }

    private function hapusStandarDanTurunannya(string $uptId, string $standarMutuId): void
    {
        $uptSubStandarIds = UptSubStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->pluck('upt_sub_standar_id');

        UptItemSubStandarMutu::whereIn('upt_sub_standar_id', $uptSubStandarIds)->delete();

        UptSubStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->delete();

        UptStandarMutu::where('upt_id', $uptId)
            ->where('standar_mutu_id', $standarMutuId)
            ->delete();
    }
}
