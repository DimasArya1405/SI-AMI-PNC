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
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            'target_type' => 'required|in:all_prodi,unit_bagian',
            'upt_ids' => 'exclude_unless:target_type,unit_bagian|required|array',
            'upt_ids.*' => 'required|exists:upt,upt_id',
            'file_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $spreadsheet = IOFactory::load($request->file('file_excel')->getPathname());

                $uptIds = $this->getTargetUptIdsForImport($request);

                $uptIds = array_values(array_filter($uptIds));

                if (empty($uptIds)) {
                    throw new \Exception('Target UPT belum dipilih.');
                }

                if ($request->target_type === 'unit_bagian') {
                    $totalImport = 0;

                    foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                        foreach ($uptIds as $uptId) {
                            $totalImport += $this->importUnitBagianLangsung(
                                $sheet,
                                $uptId,
                                $request->periode_id
                            );
                        }
                    }

                    if ($totalImport === 0) {
                        throw new \Exception('Tidak ada data yang berhasil diimport dari Excel.');
                    }

                    return;
                }

                $standarMutuIds = [];
                $urutanStandar = 1;

                foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                    $namaStandar = trim((string) $sheet->getCell('E6')->getCalculatedValue());

                    if ($namaStandar === '') {
                        $namaStandar = trim($sheet->getTitle());
                    }

                    if ($namaStandar === '') {
                        continue;
                    }

                    $standar = StandarMutu::updateOrCreate(
                        [
                            'nama_standar_mutu' => $namaStandar,
                        ],
                        [
                            'urutan' => $urutanStandar,
                        ]
                    );

                    $standarMutuIds[] = $standar->standar_mutu_id;

                    $currentSubStandar = null;
                    $lastLevel1Item = null;
                    $lastLevel2Item = null;

                    $subUrutan = 1;
                    $itemUrutan = 1;

                    for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
                        $colA = trim((string) $sheet->getCell("A{$row}")->getCalculatedValue());
                        $colB = trim((string) $sheet->getCell("B{$row}")->getCalculatedValue());

                        if ($colA === '' && $colB === '') {
                            continue;
                        }

                        if ($this->isImportHeaderRow($colA, $colB)) {
                            continue;
                        }

                        // SUB STANDAR
                        if ($colA !== '' && !is_numeric($colA) && $colB === '') {
                            $currentSubStandar = SubStandarMutu::updateOrCreate(
                                [
                                    'standar_mutu_id' => $standar->standar_mutu_id,
                                    'nama_sub_standar' => $colA,
                                ],
                                [
                                    'urutan' => $subUrutan,
                                ]
                            );

                            $lastLevel1Item = null;
                            $lastLevel2Item = null;
                            $subUrutan++;
                            $itemUrutan = 1;

                            continue;
                        }

                        if (!$currentSubStandar || $colB === '') {
                            continue;
                        }

                        $namaItem = $colB;

                        $level = 1;
                        $parentItemId = null;

                        if (is_numeric($colA)) {
                            $level = 1;
                            $parentItemId = null;
                        } elseif (preg_match('/^[a-zA-Z]\./', $namaItem)) {
                            $level = 2;
                            $parentItemId = $lastLevel1Item?->item_sub_standar_id;
                        } elseif (str_starts_with($namaItem, '-')) {
                            $level = 3;
                            $parentItemId = $lastLevel2Item?->item_sub_standar_id
                                ?? $lastLevel1Item?->item_sub_standar_id;
                        } else {
                            $level = 2;
                            $parentItemId = $lastLevel1Item?->item_sub_standar_id;
                        }

                        $tipeItem = $level === 1 ? 'pertanyaan' : 'sub_pertanyaan';

                        $item = ItemSubStandarMutu::updateOrCreate(
                            [
                                'sub_standar_id' => $currentSubStandar->sub_standar_id,
                                'parent_item_id' => $parentItemId,
                                'nama_item' => $namaItem,
                                'level' => $level,
                            ],
                            [
                                'tipe_item' => $tipeItem,
                                'urutan' => $itemUrutan,
                            ]
                        );

                        if ($level === 1) {
                            $lastLevel1Item = $item;
                            $lastLevel2Item = null;
                        }

                        if ($level === 2) {
                            $lastLevel2Item = $item;
                        }

                        $itemUrutan++;
                    }

                    $urutanStandar++;
                }

                if ($request->target_type === 'all_prodi') {
                    foreach ($uptIds as $uptId) {
                        foreach (array_unique($standarMutuIds) as $standarMutuId) {
                            $this->sinkronisasiStandarDanTurunannya(
                                $uptId,
                                $standarMutuId,
                                $request->periode_id
                            );
                        }
                    }
                }
            });

            return redirect()
                ->route('admin.ami.upt_standar_mutu')
                ->with('success', 'Import Excel berhasil. Data master dan pemetaan UPT berhasil disinkronkan.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    private function importUnitBagianLangsung($sheet, $uptId, $periodeId): int
    {
        $totalInsert = 0;

        $namaStandar = trim((string) $sheet->getCell('E6')->getCalculatedValue());

        if ($namaStandar === '') {
            $namaStandar = trim($sheet->getTitle());
        }

        if ($namaStandar === '') {
            return 0;
        }

        $standar = StandarMutu::firstOrCreate(
            ['nama_standar_mutu' => $namaStandar],
            [
                'standar_mutu_id' => (string) Str::uuid(),
                'urutan' => 0,
            ]
        );

        $uptSubIds = DB::table('upt_sub_standar_mutu')
            ->where('upt_id', $uptId)
            ->where('periode_id', $periodeId)
            ->where('standar_mutu_id', $standar->standar_mutu_id)
            ->pluck('upt_sub_standar_id')
            ->toArray();

        if (!empty($uptSubIds)) {
            DB::table('upt_item_sub_standar_mutu')
                ->whereIn('upt_sub_standar_id', $uptSubIds)
                ->delete();

            DB::table('upt_sub_standar_mutu')
                ->whereIn('upt_sub_standar_id', $uptSubIds)
                ->delete();
        }

        $currentUptSubId = null;
        $currentIndikatorItemId = null;

        $subUrutan = 1;
        $itemUrutan = 1;

        for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
            $colA = trim((string) $sheet->getCell("A{$row}")->getCalculatedValue());
            $colB = trim((string) $sheet->getCell("B{$row}")->getCalculatedValue());

            if ($colA === '' && $colB === '') {
                continue;
            }

            $upperA = strtoupper($colA);
            $upperB = strtoupper($colB);

            if (
                $upperA === 'NO' ||
                $upperB === 'PERTANYAAN DAN PERNYATAAN' ||
                $upperB === 'YA' ||
                $upperB === 'TIDAK' ||
                $upperB === 'URAIAN (BUKTI)' ||
                $upperB === 'AUDITEE'
            ) {
                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | SUB STANDAR
        |--------------------------------------------------------------------------
        */
            if (
                $colA !== '' &&
                $colB === '' &&
                str_starts_with($upperA, 'STANDAR ') &&
                !str_starts_with($upperA, 'INDIKATOR')
            ) {
                $currentUptSubId = (string) Str::uuid();

                DB::table('upt_sub_standar_mutu')->insert([
                    'upt_sub_standar_id' => $currentUptSubId,
                    'upt_id' => $uptId,
                    'standar_mutu_id' => $standar->standar_mutu_id,
                    'periode_id' => $periodeId,
                    'sub_standar_master_id' => null,
                    'nama_sub_standar' => $colA,
                    'urutan' => $subUrutan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalInsert++;

                $currentIndikatorItemId = null;
                $subUrutan++;
                $itemUrutan = 1;

                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | ITEM UTAMA LEVEL 1: INDIKATOR KEBERHASILAN
        |--------------------------------------------------------------------------
        */
            if (
                $currentUptSubId &&
                $colA !== '' &&
                $colB === '' &&
                str_starts_with($upperA, 'INDIKATOR KEBERHASILAN')
            ) {
                $currentIndikatorItemId = (string) Str::uuid();

                DB::table('upt_item_sub_standar_mutu')->insert([
                    'upt_item_sub_standar_id' => $currentIndikatorItemId,
                    'upt_id' => $uptId,
                    'periode_id' => $periodeId,
                    'upt_sub_standar_id' => $currentUptSubId,
                    'item_sub_standar_master_id' => null,
                    'parent_upt_item_id' => null,
                    'nama_item' => $colA,
                    'tipe_item' => 'pertanyaan',
                    'level' => 1,
                    'urutan' => $itemUrutan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalInsert++;
                $itemUrutan++;

                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | ANAK ITEM LEVEL 2: PERTANYAAN
        |--------------------------------------------------------------------------
        */
            if ($currentUptSubId && $currentIndikatorItemId && is_numeric($colA) && $colB !== '') {
                DB::table('upt_item_sub_standar_mutu')->insert([
                    'upt_item_sub_standar_id' => (string) Str::uuid(),
                    'upt_id' => $uptId,
                    'periode_id' => $periodeId,
                    'upt_sub_standar_id' => $currentUptSubId,
                    'item_sub_standar_master_id' => null,
                    'parent_upt_item_id' => $currentIndikatorItemId,
                    'nama_item' => $colB,
                    'tipe_item' => 'sub_pertanyaan',
                    'level' => 2,
                    'urutan' => $itemUrutan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalInsert++;
                $itemUrutan++;
            }
        }

        return $totalInsert;
    }

    private function isImportHeaderRow($colA, $colB): bool
    {
        return strtoupper($colA) === 'NO'
            || strtoupper($colB) === 'PERTANYAAN DAN PERNYATAAN'
            || strtoupper($colB) === 'YA'
            || strtoupper($colB) === 'TIDAK'
            || strtoupper($colA) === 'INDIKATOR KEBERHASILAN';
    }

    private function getTargetUptIdsForImport(Request $request): array
    {
        if ($request->target_type === 'all_prodi') {
            return UPT::where('kategori_upt', 'Prodi')
                ->pluck('upt_id')
                ->toArray();
        }

        return $request->upt_ids ?? [];
    }
}
