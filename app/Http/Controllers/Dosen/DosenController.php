<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Dosen;
use App\Models\ItemBuktiDosen;
use App\Models\JawabanAMI;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\Prodi;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $dosen = Dosen::where('user_id', $userId)->first();

        $periodeNow = Periode::where('status', '1')->first();

        $prodi = null;
        $upt = null;
        $auditee = null;

        if ($dosen) {
            $prodi = Prodi::where('prodi_id', $dosen->prodi_id)->first();

            if ($prodi) {
                $upt = UPT::where('nama_upt', $prodi->nama_prodi)->first();

                if ($upt) {
                    $auditee = Auditee::with('upt')
                        ->where('upt_id', $upt->upt_id)
                        ->first();
                }
            }
        }

        return view('dosen.dashboard', [
            'dosen' => $dosen,
            'prodi' => $prodi,
            'upt' => $upt,
            'auditee' => $auditee,
            'periode_now' => $periodeNow,
            'nama_unit' => $auditee?->upt?->nama_upt ?? '-',
        ]);
    }

    public function dokumen()
    {
        $context = $this->getDosenAuditContext();

        $pemetaanStandar = collect();
        $uptSubStandar = collect();
        $uptItemSubStandar = collect();
        $buktiDukung = collect();
        $assignedItemIds = collect();

        if ($context['upt'] && $context['periode_now'] && $context['penugasan']) {
            $assignedItemIds = ItemBuktiDosen::where('penugasan_id', $context['penugasan']->penugasan_id)
                ->pluck('upt_item_sub_standar_id');

            $pemetaanStandar = UptStandarMutu::with('standar_mutu')
                ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
                ->where('upt_standar_mutu.upt_id', $context['upt']->upt_id)
                ->where('upt_standar_mutu.periode_id', $context['periode_now']->id)
                ->orderBy('standar_mutu.urutan', 'asc')
                ->select('upt_standar_mutu.*')
                ->get();

            $uptStandarIds = $pemetaanStandar->pluck('upt_standar_mutu_id');

            if ($assignedItemIds->isNotEmpty()) {
                $assignedSubStandarIds = UptItemSubStandarMutu::whereIn('upt_item_sub_standar_id', $assignedItemIds)
                    ->pluck('upt_sub_standar_id')
                    ->unique();

                $assignedUptStandarIds = UptSubStandarMutu::whereIn('upt_sub_standar_id', $assignedSubStandarIds)
                    ->pluck('upt_standar_mutu_id')
                    ->unique();

                $pemetaanStandar = $pemetaanStandar
                    ->whereIn('upt_standar_mutu_id', $assignedUptStandarIds)
                    ->values();

                $uptSubStandar = UptSubStandarMutu::with('uptStandarMutu.standar_mutu')
                    ->whereIn('upt_standar_mutu_id', $assignedUptStandarIds)
                    ->whereIn('upt_sub_standar_id', $assignedSubStandarIds)
                    ->orderBy('urutan', 'asc')
                    ->get();

                $uptSubStandarIds = $uptSubStandar->pluck('upt_sub_standar_id');

                $uptItemSubStandar = UptItemSubStandarMutu::whereIn('upt_sub_standar_id', $uptSubStandarIds)
                    ->whereIn('upt_item_sub_standar_id', $assignedItemIds)
                    ->orderBy('urutan', 'asc')
                    ->get()
                    ->groupBy('upt_sub_standar_id');
            }

            $buktiDukung = JawabanAMI::with('dosen')
                ->where('penugasan_id', $context['penugasan']->penugasan_id)
                ->where('dosen_id', $context['dosen']->dosen_id)
                ->get()
                ->groupBy('upt_item_sub_standar_id');
        }

        return view('dosen.bukti-dukung', array_merge($context, compact(
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'buktiDukung',
            'assignedItemIds'
        )));
    }

    public function uploadDokumen(Request $request)
    {
        $validated = $request->validate([
            'upt_item_sub_standar_id' => 'required|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
            'file_bukti' => 'required|array',
            'file_bukti.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string|max:1000',
            'active_tab' => 'nullable|string',
            'open_accordion' => 'nullable|string',
            'target_scroll' => 'nullable|string',
        ], [
            'file_bukti.required' => 'File bukti wajib diupload.',
            'file_bukti.*.mimes' => 'Tipe file tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.',
            'file_bukti.*.max' => 'Ukuran salah satu file terlalu besar. Maksimal 5 MB per file.',
        ]);

        $context = $this->getDosenAuditContext();

        if (!$context['periode_now'] || !$context['penugasan'] || !$context['upt']) {
            return back()->with('error', 'Belum ada periode aktif atau penugasan AMI untuk prodi Anda.');
        }

        $itemDitugaskan = ItemBuktiDosen::where('penugasan_id', $context['penugasan']->penugasan_id)
            ->where('upt_item_sub_standar_id', $validated['upt_item_sub_standar_id'])
            ->exists();

        if (!$itemDitugaskan) {
            return back()->with('error', 'Item ini belum dibuka oleh auditee untuk upload dokumen dosen.');
        }

        $item = UptItemSubStandarMutu::with('uptSubStandar.uptStandarMutu.standar_mutu')
            ->where('upt_item_sub_standar_id', $validated['upt_item_sub_standar_id'])
            ->whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($context) {
                $query->where('upt_id', $context['upt']->upt_id)
                    ->where('periode_id', $context['periode_now']->id);
            })
            ->firstOrFail();

        $namaStandar = optional($item->uptSubStandar?->uptStandarMutu?->standar_mutu)->nama_standar_mutu ?? 'standar';
        $folder = 'bukti-dukung-dosen/upt-' . Str::slug($context['upt']->nama_upt)
            . '/periode-' . $context['periode_now']->tahun
            . '/dosen-' . Str::slug($context['dosen']->nama_lengkap)
            . '/' . Str::slug($namaStandar);

        foreach ($request->file('file_bukti') as $file) {
            $namaAsli = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $path = $folder . '/' . Str::slug($namaAsli) . '-' . Str::random(5) . '.' . $extension;

            Storage::disk('google')->put($path, file_get_contents($file->getRealPath()));

            JawabanAMI::create([
                'jawaban_id' => Str::uuid()->toString(),
                'upt_item_sub_standar_id' => $item->upt_item_sub_standar_id,
                'penugasan_id' => $context['penugasan']->penugasan_id,
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'keterangan' => $validated['keterangan'] ?? null,
                'sumber' => 'dosen',
                'uploaded_by_user_id' => Auth::id(),
                'dosen_id' => $context['dosen']->dosen_id,
                'status_validasi' => 'menunggu',
            ]);
        }

        return redirect()
            ->to(url()->previous() . '#item-' . $item->upt_item_sub_standar_id)
            ->with([
                'success' => 'Dokumen berhasil diupload dan menunggu validasi auditee.',
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function hapusDokumen(Request $request, $id)
    {
        $context = $this->getDosenAuditContext();

        $dokumen = JawabanAMI::where('jawaban_id', $id)
            ->where('dosen_id', $context['dosen']->dosen_id)
            ->firstOrFail();

        if ($dokumen->status_validasi === 'diterima') {
            return back()->with('error', 'Dokumen yang sudah diterima auditee tidak dapat dihapus oleh dosen.');
        }

        $itemId = $dokumen->upt_item_sub_standar_id;

        if ($dokumen->file_path && Storage::disk('google')->exists($dokumen->file_path)) {
            Storage::disk('google')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return redirect()
            ->to(url()->previous() . '#item-' . $itemId)
            ->with([
                'success' => 'Dokumen berhasil dihapus.',
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function previewDokumen($id)
    {
        $dokumen = $this->findDokumenDosen($id);

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $file = Storage::disk('google')->get($dokumen->file_path);
        $mimeType = Storage::disk('google')->mimeType($dokumen->file_path) ?? 'application/octet-stream';
        $namaFile = str_replace('"', '', $dokumen->nama_file);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    public function downloadDokumen($id)
    {
        $dokumen = $this->findDokumenDosen($id);

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan di Google Drive.');
        }

        return response(Storage::disk('google')->get($dokumen->file_path), 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    private function getDosenAuditContext(): array
    {
        $dosen = Dosen::with('prodi')->where('user_id', Auth::id())->firstOrFail();
        $prodi = $dosen->prodi;
        $periodeNow = Periode::where('status', '1')->first();
        $upt = $prodi ? UPT::where('nama_upt', $prodi->nama_prodi)->first() : null;
        $auditee = $upt ? Auditee::with('upt')->where('upt_id', $upt->upt_id)->first() : null;
        $penugasan = ($upt && $periodeNow)
            ? Penugasan::where('upt_id', $upt->upt_id)
                ->where('periode_id', $periodeNow->id)
                ->first()
            : null;

        return [
            'dosen' => $dosen,
            'prodi' => $prodi,
            'upt' => $upt,
            'auditee' => $auditee,
            'periode_now' => $periodeNow,
            'penugasan' => $penugasan,
            'nama_unit' => $auditee?->upt?->nama_upt ?? $upt?->nama_upt ?? '-',
        ];
    }

    private function findDokumenDosen(string $id): JawabanAMI
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        return JawabanAMI::where('jawaban_id', $id)
            ->where('dosen_id', $dosen->dosen_id)
            ->firstOrFail();
    }
}
