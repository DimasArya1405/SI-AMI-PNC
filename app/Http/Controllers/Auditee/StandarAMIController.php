<?php

namespace App\Http\Controllers\Auditee;

use App\DataTables\Auditee\StandarAMIDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\ItemBuktiDosen;
use App\Models\JawabanAMI;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StandarAMIController extends Controller
{
    public function index(StandarAMIDataTable $dataTable)
    {
        return $dataTable->render('auditee.standar-ami');
    }

    public function detail($upt_id, $periode_id)
    {
        $user = Auth::user();

        $auditee = Auditee::where('user_id', $user->id)->firstOrFail();

        if ($auditee->upt_id !== $upt_id) {
            abort(403, 'Anda tidak memiliki akses ke pemetaan ini.');
        }

        $upt = UPT::findOrFail($upt_id);
        $periode = Periode::findOrFail($periode_id);
        $status_periode = $periode->status == 0;

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->where('upt_standar_mutu.upt_id', $upt_id)
            ->where('upt_standar_mutu.periode_id', $periode_id)
            ->orderBy('standar_mutu.urutan', 'asc')
            ->select('upt_standar_mutu.*')
            ->get();

        $uptStandarIds = $pemetaanStandar->pluck('upt_standar_mutu_id');

        $uptSubStandar = UptSubStandarMutu::with('uptStandarMutu.standar_mutu')
            ->whereIn('upt_standar_mutu_id', $uptStandarIds)
            ->orderBy('urutan', 'asc')
            ->get();

        $uptSubStandarIds = $uptSubStandar->pluck('upt_sub_standar_id');

        $uptItemSubStandar = UptItemSubStandarMutu::whereIn('upt_sub_standar_id', $uptSubStandarIds)
            ->orderBy('urutan', 'asc')
            ->get()
            ->groupBy('upt_sub_standar_id');

        $penugasan = Penugasan::with('rka')
            ->where('upt_id', $auditee->upt_id)
            ->where('periode_id', $periode_id)
            ->firstOrFail();
        $rkaFinal = $this->isRkaFinal($penugasan);

        $buktiDukung = JawabanAMI::where('penugasan_id', $penugasan->penugasan_id)
            ->with('dosen')
            ->get()
            ->groupBy('upt_item_sub_standar_id');

        $itemDosenIds = ItemBuktiDosen::where('penugasan_id', $penugasan->penugasan_id)
            ->pluck('upt_item_sub_standar_id')
            ->toArray();

        return view('auditee.standar-ami-detail', compact(
            'upt',
            'periode',
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'buktiDukung',
            'status_periode',
            'penugasan',
            'itemDosenIds',
            'rkaFinal'
        ));
    }

    public function updateItemDosen(Request $request, $penugasan_id)
    {
        $validated = $request->validate([
            'all_item_ids' => 'nullable|array',
            'all_item_ids.*' => 'exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::with('rka')
            ->where('penugasan_id', $penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        $periode = Periode::findOrFail($penugasan->periode_id);

        if ($periode->status == 0) {
            return back()->with('error', 'Periode sudah tidak aktif. Pilihan item dosen tidak dapat diubah.');
        }

        if ($this->isRkaFinal($penugasan)) {
            return back()->with('error', 'RKA sudah difinalisasi. Pilihan item dan bukti AMI tidak dapat diubah lagi.');
        }

        $allItemIds = collect($validated['all_item_ids'] ?? [])
            ->filter()
            ->unique()
            ->values();

        $validItemIds = UptItemSubStandarMutu::whereIn('upt_item_sub_standar_id', $allItemIds)
            ->whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($auditee, $penugasan) {
                $query->where('upt_id', $auditee->upt_id)
                    ->where('periode_id', $penugasan->periode_id);
            })
            ->pluck('upt_item_sub_standar_id');

        $selectedIds = collect($validated['item_ids'] ?? [])
            ->intersect($validItemIds)
            ->values();

        DB::transaction(function () use ($penugasan, $validItemIds, $selectedIds) {
            ItemBuktiDosen::where('penugasan_id', $penugasan->penugasan_id)
                ->whereIn('upt_item_sub_standar_id', $validItemIds)
                ->delete();

            foreach ($selectedIds as $itemId) {
                ItemBuktiDosen::create([
                    'item_bukti_dosen_id' => Str::uuid()->toString(),
                    'penugasan_id' => $penugasan->penugasan_id,
                    'upt_item_sub_standar_id' => $itemId,
                    'assigned_by_user_id' => Auth::id(),
                ]);
            }
        });

        return back()->with('success', 'Pilihan item untuk dosen berhasil disimpan.');
    }

    public function uploadBukti(Request $request)
    {
        $validated = $request->validate([
            'upt_item_sub_standar_id' => 'required|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
            'periode_id' => 'required|exists:periode,id',
            'file_bukti' => 'required|array',
            'file_bukti.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string|max:1000',
            'active_tab' => 'nullable|string',
        ], [
            'file_bukti.required' => 'File bukti wajib diupload.',
            'file_bukti.array' => 'File bukti tidak valid.',
            'file_bukti.*.uploaded' => 'Ukuran salah satu file terlalu besar atau gagal diupload. Maksimal 5 MB per file.',
            'file_bukti.*.file' => 'File bukti tidak valid.',
            'file_bukti.*.mimes' => 'Tipe file tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.',
            'file_bukti.*.max' => 'Ukuran salah satu file terlalu besar. Maksimal 5 MB per file.',
        ]);

        $periode = Periode::findOrFail($validated['periode_id']);

        if ($periode->status == 0) {
            return back()->with('error', 'Periode sudah tidak aktif. Upload dokumen tidak diperbolehkan.');
        }

        $auditee = Auditee::with('upt')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $penugasan = Penugasan::with('rka')
            ->where('upt_id', $auditee->upt_id)
            ->where('periode_id', $validated['periode_id'])
            ->firstOrFail();

        if ($this->isRkaFinal($penugasan)) {
            return back()->with('error', 'RKA sudah difinalisasi. Upload bukti AMI tidak dapat dilakukan lagi.');
        }

        $item = UptItemSubStandarMutu::with('uptSubStandar.uptStandarMutu.standar_mutu')
            ->where('upt_item_sub_standar_id', $validated['upt_item_sub_standar_id'])
            ->whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($auditee, $validated) {
                $query->where('upt_id', $auditee->upt_id)
                    ->where('periode_id', $validated['periode_id']);
            })
            ->firstOrFail();

        $namaStandar = optional($item->uptSubStandar?->uptStandarMutu?->standar_mutu)->nama_standar_mutu ?? 'standar';
        $standarSlug = Str::slug($namaStandar);

        $uptNama = Str::slug($auditee->upt->nama_upt ?? 'upt');
        $periodeTahun = $periode->tahun;

        $folder = 'bukti-dukung/upt-' . $uptNama . '/periode-' . $periodeTahun . '/' . $standarSlug;

        foreach ($request->file('file_bukti') as $file) {
            $namaAsli = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $namaBersih = Str::slug($namaAsli);
            $namaFileBaru = $namaBersih . '-' . Str::random(5) . '.' . $extension;

            $path = $folder . '/' . $namaFileBaru;

            Storage::disk('google')->put(
                $path,
                file_get_contents($file->getRealPath())
            );

            JawabanAMI::create([
                'jawaban_id' => Str::uuid()->toString(),
                'upt_item_sub_standar_id' => $item->upt_item_sub_standar_id,
                'penugasan_id' => $penugasan->penugasan_id,
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'keterangan' => $validated['keterangan'] ?? null,
                'sumber' => 'auditee',
                'uploaded_by_user_id' => Auth::id(),
                'status_validasi' => 'diterima',
                'validated_by_user_id' => Auth::id(),
                'validated_at' => now(),
            ]);
        }

        return redirect()
            ->to(url()->previous() . '#item-' . $item->upt_item_sub_standar_id)
            ->with([
                'success' => 'Bukti dukung berhasil diupload.',
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function hapusBukti(Request $request, $id)
    {
        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $dokumen = JawabanAMI::with(['item.uptSubStandar.uptStandarMutu', 'penugasan.rka'])
            ->where('jawaban_id', $id)
            ->whereHas('penugasan.upt.auditee', function ($query) use ($auditee) {
                $query->where('auditee_id', $auditee->auditee_id);
            })
            ->firstOrFail();

        $itemId = $dokumen->upt_item_sub_standar_id;

        $periodeId = $dokumen->item?->uptSubStandar?->uptStandarMutu?->periode_id;
        $periode = Periode::find($periodeId);

        if ($this->isRkaFinal($dokumen->penugasan)) {
            return redirect()
                ->to(url()->previous() . '#item-' . $itemId)
                ->with('error', 'RKA sudah difinalisasi. Bukti AMI tidak dapat dihapus lagi.')
                ->with('active_tab', $request->active_tab);
        }

        if ($periode && $periode->status == 0) {
            return redirect()
                ->to(url()->previous() . '#item-' . $itemId)
                ->with('error', 'Periode sudah tidak aktif. File tidak dapat dihapus.')
                ->with('active_tab', $request->active_tab);
        }

        if ($dokumen->file_path && Storage::disk('google')->exists($dokumen->file_path)) {
            Storage::disk('google')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return redirect()
            ->to(url()->previous() . '#item-' . $itemId)
            ->with([
                'success' => 'Bukti dukung berhasil dihapus.',
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function validasiBukti(Request $request, $id)
    {
        $validated = $request->validate([
            'status_validasi' => 'required|in:diterima,ditolak',
            'catatan_validasi' => 'nullable|string|max:1000',
            'active_tab' => 'nullable|string',
            'open_accordion' => 'nullable|string',
            'target_scroll' => 'nullable|string',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $dokumen = JawabanAMI::with(['item.uptSubStandar.uptStandarMutu', 'penugasan.rka'])
            ->where('jawaban_id', $id)
            ->where('sumber', 'dosen')
            ->whereHas('penugasan.upt.auditee', function ($query) use ($auditee) {
                $query->where('auditee_id', $auditee->auditee_id);
            })
            ->firstOrFail();

        if ($this->isRkaFinal($dokumen->penugasan)) {
            return redirect()
                ->to(url()->previous() . '#item-' . $dokumen->upt_item_sub_standar_id)
                ->with('error', 'RKA sudah difinalisasi. Validasi bukti dosen tidak dapat diubah lagi.')
                ->with('active_tab', $request->active_tab);
        }

        $dokumen->update([
            'status_validasi' => $validated['status_validasi'],
            'catatan_validasi' => $validated['catatan_validasi'] ?? null,
            'validated_by_user_id' => Auth::id(),
            'validated_at' => now(),
        ]);

        $pesan = $validated['status_validasi'] === 'diterima'
            ? 'Bukti dosen berhasil diterima.'
            : 'Bukti dosen berhasil ditolak.';

        return redirect()
            ->to(url()->previous() . '#item-' . $dokumen->upt_item_sub_standar_id)
            ->with([
                'success' => $pesan,
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function downloadBukti($id)
    {
        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $dokumen = JawabanAMI::where('jawaban_id', $id)
            ->whereHas('penugasan.upt.auditee', function ($query) use ($auditee) {
                $query->where('auditee_id', $auditee->auditee_id);
            })
            ->firstOrFail();

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan di Google Drive.');
        }

        return response(Storage::disk('google')->get($dokumen->file_path), 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    public function previewBukti($id)
    {
        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $dokumen = JawabanAMI::where('jawaban_id', $id)
            ->whereHas('penugasan.upt.auditee', function ($query) use ($auditee) {
                $query->where('auditee_id', $auditee->auditee_id);
            })
            ->firstOrFail();

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $file = Storage::disk('google')->get($dokumen->file_path);

        $mimeType = Storage::disk('google')->mimeType($dokumen->file_path)
            ?? 'application/octet-stream';

        $namaFile = str_replace('"', '', $dokumen->nama_file);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    private function isRkaFinal(?Penugasan $penugasan): bool
    {
        if (!$penugasan) {
            return false;
        }

        $rka = $penugasan->relationLoaded('rka') ? $penugasan->rka : $penugasan->rka()->first();

        return $rka && ($rka->status === 'final' || filled($rka->finalized_at));
    }
}
