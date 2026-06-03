<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\JawabanAMI;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use App\Notifications\PenugasanAuditNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PelaksanaanAuditController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $auditor = Auditor::where('user_id', $user->id)->firstOrFail();
        $periode = Periode::where('status', '1')->first();
        $penugasan = Penugasan::where('periode_id', $periode?->id)
            ->where('status_penugasan', 'aktif')
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->with('upt')
            ->get();

        return view('auditor.pelaksanaan_audit.index', compact('penugasan'));
    }
    public function detail($id)
    {
        $user = Auth::user();

        $auditor = Auditor::where('user_id', $user->id)->firstOrFail();
        $periode = Periode::where('status', '1')->firstOrFail();
        $penugasan = Penugasan::where('periode_id', $periode?->id)
            ->where('upt_id', $id) // Cari berdasarkan ID UPT yang dipassing
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();
        $auditee = Auditee::where('upt_id', $id)->firstOrFail();

        // CEK AKETUA AUDITOR ATAU BUKAN
        if ($penugasan->auditor_id_1 == $auditor->auditor_id) {
            $ketua = 1;
        } else {
            $ketua = 0;
        }

        $upt = UPT::findOrFail($id);
        $periode_id = $periode->id;
        $status_periode = $periode->status == 0;

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->where('upt_standar_mutu.upt_id', $id)
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

        $penugasan = Penugasan::where('upt_id', $auditee->upt_id)
            ->where('periode_id', $periode_id)
            ->firstOrFail();

        $buktiDukung = JawabanAMI::where('penugasan_id', $penugasan->penugasan_id)
            ->where('status_validasi', 'diterima')
            ->with('dosen')
            ->get()
            ->groupBy('upt_item_sub_standar_id');

        $allItemIds = $uptItemSubStandar->flatten()->pluck('upt_item_sub_standar_id');
        // Ambil data jawaban berdasarkan ID item tersebut
        $jawabanAudit = JawabanAudit::whereIn('upt_item_sub_standar_id', $allItemIds)
            ->get()
            ->keyBy('upt_item_sub_standar_id');

        return view('auditor.pelaksanaan_audit.detail', compact(
            'upt',
            'periode',
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'buktiDukung',
            'status_periode',
            'auditee',
            'ketua',
            'jawabanAudit',
            // 'adaPeriode'
        ));
    }
    public function penilaian(Request $request, $id)
    {
        $validated = $request->validate([
            'jawaban' => 'required|in:Ya,Tidak',
            'kategori_temuan' => 'nullable|required_if:jawaban,Tidak|in:KTS,OB',
            'catatan' => 'nullable|string|max:5000',
        ]);

        $auditor = Auditor::where('user_id', Auth::id())->firstOrFail();
        $item = UptItemSubStandarMutu::with('uptSubStandar.uptStandarMutu')
            ->findOrFail($id);
        $uptStandarMutu = $item->uptSubStandar?->uptStandarMutu;

        abort_unless($uptStandarMutu, 404, 'Pemetaan item standar tidak ditemukan.');

        Penugasan::where('upt_id', $uptStandarMutu->upt_id)
            ->where('periode_id', $uptStandarMutu->periode_id)
            ->where('status_penugasan', 'aktif')
            ->where('auditor_id_1', $auditor->auditor_id)
            ->firstOrFail();

        $nilaiJawaban = $validated['jawaban'] === 'Ya';

        JawabanAudit::updateOrCreate(
            ['upt_item_sub_standar_id' => $item->upt_item_sub_standar_id],
            [
                'jawaban' => $nilaiJawaban,
                'kategori_temuan' => $nilaiJawaban ? null : $validated['kategori_temuan'],
                'catatan' => $validated['catatan'] ?? null,
            ]
        );

        $this->kirimNotifikasiRkaJikaTersedia($item->upt_item_sub_standar_id);

        return redirect()->back()->with([
            'success' => 'Penilaian berhasil disimpan.',
            'active_tab' => $request->active_tab,
            'open_accordion' => $request->open_accordion,
            'target_scroll' => $request->target_scroll,
        ]);
    }
    public function exportRka($id)
    {
        $upt = UPT::findOrFail($id);
        $periode = Periode::where('status', '1')->first();
        $periode_id = $periode->id;

        // Ambil Standar Mutu yang memiliki relasi ke bawah hingga ke jawaban = 0
        $standarMutu = UptStandarMutu::with(['standar_mutu', 'subStandarUpt.items.jawaban_audit' => function ($query) {
            $query->where('jawaban', 0); // Filter langsung di query agar tidak berat di view
        }])
            ->where('upt_id', $id)
            ->where('periode_id', $periode_id)
            ->get();
        $penugasan = Penugasan::where('upt_id', $id)
            ->where('periode_id', $periode_id)
            ->with('auditor1', 'auditor2')
            ->firstOrFail();

        // return view('auditor.export.pdf.rka', compact('standarMutu', 'upt', 'periode', 'penugasan'));
                // 2. Load View PDF (Gunakan file blade khusus PDF yang sudah kita buat sebelumnya)
        $pdf = Pdf::loadView('auditor.export.pdf.rka', compact('standarMutu', 'upt', 'periode', 'penugasan'))
            ->setPaper('a4', 'portrait');

        // 3. Download atau Stream
        return $pdf->stream('RKA.pdf');
    }
    public function previewBukti($id)
    {
        $dokumen = $this->findDokumenUntukAuditor($id);

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
    public function downloadBukti($id)
    {
        $dokumen = $this->findDokumenUntukAuditor($id);

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan di Google Drive.');
        }

        return response(Storage::disk('google')->get($dokumen->file_path), 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    private function findDokumenUntukAuditor(string $id): JawabanAMI
    {
        $auditor = Auditor::where('user_id', Auth::id())->firstOrFail();

        return JawabanAMI::where('jawaban_id', $id)
            ->where('status_validasi', 'diterima')
            ->whereHas('penugasan', function ($query) use ($auditor) {
                $query->where(function ($subQuery) use ($auditor) {
                    $subQuery->where('auditor_id_1', $auditor->auditor_id)
                        ->orWhere('auditor_id_2', $auditor->auditor_id);
                });
            })
            ->firstOrFail();
    }

    private function kirimNotifikasiRkaJikaTersedia(string $itemId): void
    {
        $item = UptItemSubStandarMutu::with('uptSubStandar.uptStandarMutu')
            ->find($itemId);

        $uptStandarMutu = $item?->uptSubStandar?->uptStandarMutu;

        if (!$uptStandarMutu) {
            return;
        }

        $penugasan = Penugasan::with('upt')
            ->where('upt_id', $uptStandarMutu->upt_id)
            ->where('periode_id', $uptStandarMutu->periode_id)
            ->first();

        if (!$penugasan) {
            return;
        }

        $itemIds = UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');

        if ($itemIds->isEmpty()) {
            return;
        }

        $jumlahJawaban = JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)
            ->distinct()
            ->count('upt_item_sub_standar_id');

        if ($jumlahJawaban < $itemIds->count()) {
            return;
        }

        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "Penilaian audit untuk {$namaUpt} telah lengkap. Ringkasan Kondisi Audit (RKA) sudah dapat dilihat.";
        $url = route('auditee.rka.show', $penugasan->penugasan_id);

        Auditee::with('user')
            ->where('upt_id', $penugasan->upt_id)
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->each(function ($user) use ($penugasan, $pesan, $url) {
                $sudahDikirim = $user->notifications()
                    ->where('type', PenugasanAuditNotification::class)
                    ->get()
                    ->contains(fn ($notifikasi) => ($notifikasi->data['jenis'] ?? null) === 'rka-tersedia'
                        && ($notifikasi->data['penugasan_id'] ?? null) === $penugasan->penugasan_id);

                if (!$sudahDikirim) {
                    $user->notify(new PenugasanAuditNotification(
                        $penugasan,
                        'RKA Tersedia',
                        $pesan,
                        $url,
                        'rka-tersedia'
                    ));
                }
            });
    }
}
