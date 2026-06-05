<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\TindakanKoreksi;
use App\Models\UptItemSubStandarMutu;
use App\Models\VerifikasiTindakanKoreksi;
use App\Notifications\PenugasanAuditNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MonitoringTindakanKoreksiController extends Controller
{
    public function index(): View
    {
        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2'])
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $itemIds = UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
                    $query->where('upt_id', $penugasan->upt_id)
                        ->where('periode_id', $penugasan->periode_id);
                })->pluck('upt_item_sub_standar_id');

                $temuanIds = JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)
                    ->where('jawaban', 0)
                    ->pluck('id');

                $tk = TindakanKoreksi::where('penugasan_id', $penugasan->penugasan_id)->get();
                $carryForward = $this->getTemuanBelumSelesaiSiklusSebelumnya($penugasan);

                $penugasan->setAttribute('jumlah_temuan', $temuanIds->count());
                $penugasan->setAttribute('tk_draft', $tk->where('status', 'draft')->count());
                $penugasan->setAttribute('tk_diajukan', $tk->where('status', 'diajukan')->count());
                $penugasan->setAttribute('tk_ditolak', $tk->where('status', 'ditolak')->count());
                $penugasan->setAttribute('tk_disetujui', $tk->where('status', 'disetujui')->count());
                $penugasan->setAttribute('tk_selesai', $tk->where('status', 'selesai')->count());
                $penugasan->setAttribute('tk_menunggu_p4mp', $tk
                    ->where('status', 'selesai')
                    ->filter(fn ($item) => !$item->p4mp_status || $item->p4mp_status === 'menunggu_verifikasi')
                    ->count());
                $penugasan->setAttribute('tk_belum_dibuat', max($temuanIds->count() - $tk->count(), 0));
                $penugasan->setAttribute('tk_terlambat', $tk
                    ->whereIn('status', ['draft', 'diajukan', 'ditolak', 'disetujui'])
                    ->filter(fn ($item) => $item->target_selesai && $item->target_selesai->isBefore(today()))
                    ->count());
                $penugasan->setAttribute('temuan_lintas_siklus', $carryForward->count());
                $penugasan->setAttribute('ob_naik_kts', $carryForward
                    ->filter(fn ($item) => strtoupper((string) $item->jawabanAudit?->kategori_temuan) === 'OB')
                    ->count());

                return $penugasan;
            });

        $ringkasan = [
            'total_temuan' => $penugasan->sum('jumlah_temuan'),
            'total_tk_selesai' => $penugasan->sum('tk_selesai'),
            'total_tk_menunggu' => $penugasan->sum('tk_diajukan'),
            'total_tk_terlambat' => $penugasan->sum('tk_terlambat'),
            'total_menunggu_p4mp' => $penugasan->sum('tk_menunggu_p4mp'),
            'total_lintas_siklus' => $penugasan->sum('temuan_lintas_siklus'),
            'total_ob_naik_kts' => $penugasan->sum('ob_naik_kts'),
        ];

        return view('admin.monitoring-tindakan-koreksi.index', compact('penugasan', 'ringkasan'));
    }

    public function show(string $penugasanId): View
    {
        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'auditee.user', 'verifikasiTindakanKoreksi.finalizedBy'])
            ->where('penugasan_id', $penugasanId)
            ->firstOrFail();

        $temuan = $this->getTemuan($penugasan);
        $verifikasiTk = $penugasan->verifikasiTindakanKoreksi;

        return view('admin.monitoring-tindakan-koreksi.show', compact('penugasan', 'temuan', 'verifikasiTk'));
    }

    public function verifikasi(Request $request, string $tindakanKoreksiId): RedirectResponse
    {
        $validated = $request->validate([
            'p4mp_status' => 'required|in:terverifikasi,perlu_perbaikan',
            'p4mp_catatan' => 'nullable|required_if:p4mp_status,perlu_perbaikan|string|max:5000',
            'wadir1_nama' => 'nullable|string|max:255',
        ]);

        $tindakanKoreksi = TindakanKoreksi::where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        abort_unless($tindakanKoreksi->status === 'selesai', 403, 'Verifikasi P4MP hanya dapat dilakukan setelah auditor menandai tindakan koreksi selesai.');

        $tindakanKoreksi->update([
            'p4mp_status' => $validated['p4mp_status'],
            'p4mp_catatan' => $validated['p4mp_catatan'] ?? null,
            'wadir1_nama' => $validated['wadir1_nama'] ?? null,
            'p4mp_verified_by_user_id' => Auth::id(),
            'p4mp_verified_at' => now(),
        ]);

        $this->kirimNotifikasiVerifikasiP4mp($tindakanKoreksi->fresh([
            'penugasan.upt',
            'penugasan.auditor1.user',
            'penugasan.auditor2.user',
        ]));

        return back()->with('success', 'Verifikasi tindakan koreksi berhasil disimpan.');
    }

    public function finalisasi(Request $request, string $penugasanId): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'kepala_p4mp', 403, 'Verifikasi tindakan koreksi hanya dapat difinalisasi oleh Kepala P4MP.');

        $validated = $request->validate([
            'catatan_umum' => 'nullable|string|max:10000',
            'catatan_item' => 'nullable|array',
            'catatan_item.*' => 'nullable|string|max:5000',
            'wadir1_nama' => 'nullable|string|max:255',
        ]);

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1.user', 'auditor2.user'])
            ->where('penugasan_id', $penugasanId)
            ->firstOrFail();

        $temuan = $this->getTemuan($penugasan);
        $tindakanKoreksi = $temuan
            ->pluck('tindakanKoreksi')
            ->filter()
            ->values();

        if ($temuan->isEmpty()) {
            return back()->with('error', 'Belum ada temuan yang perlu diverifikasi P4MP.');
        }

        if ($tindakanKoreksi->count() !== $temuan->count() || $tindakanKoreksi->contains(fn ($tk) => $tk->status !== 'selesai')) {
            return back()->with('error', 'Finalisasi belum bisa dilakukan karena masih ada tindakan koreksi yang belum selesai dinilai auditor.');
        }

        $catatanItem = collect($validated['catatan_item'] ?? []);

        $tindakanKoreksi->each(function (TindakanKoreksi $tk) use ($catatanItem, $validated) {
            $tk->update([
                'p4mp_status' => 'terverifikasi',
                'p4mp_catatan' => $catatanItem->get($tk->tindakan_koreksi_id),
                'wadir1_nama' => $validated['wadir1_nama'] ?? null,
                'p4mp_verified_by_user_id' => Auth::id(),
                'p4mp_verified_at' => now(),
            ]);
        });

        $verifikasiTk = VerifikasiTindakanKoreksi::firstOrNew([
            'penugasan_id' => $penugasan->penugasan_id,
        ]);

        if (!$verifikasiTk->exists) {
            $verifikasiTk->verifikasi_tk_id = (string) Str::uuid();
        }

        $verifikasiTk->fill([
            'catatan_umum' => $validated['catatan_umum'] ?? null,
            'wadir1_nama' => $validated['wadir1_nama'] ?? null,
            'finalized_by_user_id' => Auth::id(),
            'finalized_at' => now(),
        ])->save();

        $this->kirimNotifikasiFinalisasiP4mp($penugasan);

        return back()->with('success', 'Tindakan koreksi berhasil difinalisasi dan ditandai sudah diverifikasi P4MP.');
    }

    public function downloadBukti(string $tindakanKoreksiId)
    {
        $tindakanKoreksi = TindakanKoreksi::where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        abort_unless($tindakanKoreksi->bukti_file_path && Storage::disk('local')->exists($tindakanKoreksi->bukti_file_path), 404);

        return Storage::disk('local')->download($tindakanKoreksi->bukti_file_path, $tindakanKoreksi->bukti_nama_file);
    }

    public function previewBukti(string $tindakanKoreksiId)
    {
        $tindakanKoreksi = TindakanKoreksi::where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        abort_unless($tindakanKoreksi->bukti_file_path && Storage::disk('local')->exists($tindakanKoreksi->bukti_file_path), 404);

        $namaFile = str_replace('"', '', $tindakanKoreksi->bukti_nama_file);

        return response(Storage::disk('local')->get($tindakanKoreksi->bukti_file_path), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($tindakanKoreksi->bukti_file_path) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    public function export(string $penugasanId): Response
    {
        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'verifikasiTindakanKoreksi.finalizedBy'])
            ->where('penugasan_id', $penugasanId)
            ->firstOrFail();

        $temuan = $this->getTemuan($penugasan);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'Tindakan-Koreksi-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';

        return Pdf::loadView('auditor.export.pdf.tindakan-koreksi', compact('penugasan', 'temuan', 'upt', 'periode'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    private function getTemuan(Penugasan $penugasan)
    {
        return JawabanAudit::with([
            'itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'tindakanKoreksi.buktiUploadedBy',
            'tindakanKoreksi.verifiedBy',
            'tindakanKoreksi.p4mpVerifiedBy',
            'rkaTemuan',
        ])
            ->whereIn('upt_item_sub_standar_id', $this->getItemIds($penugasan))
            ->where('jawaban', 0)
            ->get()
            ->sortBy(fn ($jawaban) => sprintf(
                '%05d-%05d-%05d',
                $jawaban->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->urutan ?? 0,
                $jawaban->itemSubStandar?->uptSubStandar?->urutan ?? 0,
                $jawaban->itemSubStandar?->urutan ?? 0
            ))
            ->values();
    }

    private function getItemIds(Penugasan $penugasan)
    {
        return UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');
    }

    private function getTemuanBelumSelesaiSiklusSebelumnya(Penugasan $penugasan)
    {
        $tahun = $penugasan->periode?->tahun;

        return TindakanKoreksi::with(['penugasan.periode', 'jawabanAudit'])
            ->where('penugasan_id', '!=', $penugasan->penugasan_id)
            ->where('status', '!=', 'selesai')
            ->whereHas('penugasan', function ($query) use ($penugasan, $tahun) {
                $query->where('upt_id', $penugasan->upt_id);

                if ($tahun) {
                    $query->whereHas('periode', fn ($periodeQuery) => $periodeQuery->where('tahun', '<', $tahun));
                }
            })
            ->get();
    }

    private function kirimNotifikasiVerifikasiP4mp(TindakanKoreksi $tindakanKoreksi): void
    {
        $penugasan = $tindakanKoreksi->penugasan;
        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $status = $tindakanKoreksi->p4mp_status === 'terverifikasi' ? 'terverifikasi' : 'perlu perbaikan';
        $pesan = "Verifikasi P4MP untuk tindakan koreksi {$namaUpt} telah {$status}.";

        $auditeeUsers = Auditee::with('user')
            ->where('upt_id', $penugasan->upt_id)
            ->get()
            ->pluck('user');

        $auditorUsers = collect([
            $penugasan->auditor1?->user,
            $penugasan->auditor2?->user,
        ]);

        $auditeeUsers
            ->merge($auditorUsers)
            ->filter()
            ->unique('id')
            ->each(fn ($user) => $user->notify(new PenugasanAuditNotification(
                $penugasan,
                'Verifikasi TK P4MP',
                $pesan,
                $user->role === 'auditee'
                    ? route('auditee.tindakan_koreksi.show', $penugasan->penugasan_id)
                    : route('auditor.tindakan_koreksi.show', $penugasan->penugasan_id),
                'tk-p4mp-' . $tindakanKoreksi->p4mp_status
            )));
    }

    private function kirimNotifikasiFinalisasiP4mp(Penugasan $penugasan): void
    {
        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "Tindakan koreksi {$namaUpt} telah difinalisasi dan diverifikasi oleh P4MP.";

        $auditeeUsers = Auditee::with('user')
            ->where('upt_id', $penugasan->upt_id)
            ->get()
            ->pluck('user');

        $auditorUsers = collect([
            $penugasan->auditor1?->user,
            $penugasan->auditor2?->user,
        ]);

        $auditeeUsers
            ->merge($auditorUsers)
            ->filter()
            ->unique('id')
            ->each(fn ($user) => $user->notify(new PenugasanAuditNotification(
                $penugasan,
                'TK Diverifikasi P4MP',
                $pesan,
                $user->role === 'auditee'
                    ? route('auditee.tindakan_koreksi.show', $penugasan->penugasan_id)
                    : route('auditor.tindakan_koreksi.show', $penugasan->penugasan_id),
                'tk-p4mp-final'
            )));
    }
}
