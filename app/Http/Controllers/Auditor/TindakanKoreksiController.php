<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\TindakanKoreksi;
use App\Models\UptItemSubStandarMutu;
use App\Models\User;
use App\Notifications\PenugasanAuditNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Milon\Barcode\Facades\DNS2DFacade;
use Symfony\Component\HttpFoundation\Response;

class TindakanKoreksiController extends Controller
{
    public function index(): View
    {
        $auditor = $this->getAuditor();

        $penugasan = Penugasan::with(['periode', 'upt'])
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $temuan = $this->getTemuan($penugasan);
                $tk = TindakanKoreksi::where('penugasan_id', $penugasan->penugasan_id)->get();

                $penugasan->setAttribute('jumlah_temuan', $temuan->count());
                $penugasan->setAttribute('tk_menunggu', $tk->where('status', 'diajukan')->count());
                $penugasan->setAttribute('tk_selesai', $tk->where('status', 'selesai')->count());

                return $penugasan;
            });

        return view('auditor.tindakan-koreksi.index', compact('penugasan'));
    }

    public function show(string $penugasanId): View
    {
        $auditor = $this->getAuditor();
        $penugasan = $this->getPenugasanAuditor($penugasanId);
        $isKetuaAuditor = $penugasan->auditor_id_1 === $auditor->auditor_id;
        $temuan = $this->getTemuan($penugasan);
        $carryForward = $this->getTemuanBelumSelesaiSiklusSebelumnya($penugasan);

        return view('auditor.tindakan-koreksi.show', compact('penugasan', 'temuan', 'carryForward', 'isKetuaAuditor'));
    }

    public function verifikasi(Request $request, string $tindakanKoreksiId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak,selesai',
            'catatan_auditor' => 'nullable|required_if:status,ditolak|string|max:5000',
            'hasil_penilaian_auditor' => 'nullable|string|max:5000',
        ]);

        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        $this->getPenugasanKetuaAuditor($tindakanKoreksi->penugasan_id);

        if ($validated['status'] === 'selesai' && !$tindakanKoreksi->bukti_file_path) {
            return back()
                ->withErrors(['status' => 'Tindakan koreksi baru bisa ditandai selesai setelah auditee mengunggah bukti.'])
                ->withInput();
        }

        if ($validated['status'] === 'selesai' && empty($validated['hasil_penilaian_auditor'])) {
            return back()
                ->withErrors(['hasil_penilaian_auditor' => 'Hasil penilaian ulang auditor wajib diisi.'])
                ->withInput();
        }

        $tindakanKoreksi->update([
            'status' => $validated['status'],
            'catatan_auditor' => $validated['catatan_auditor'] ?? null,
            'hasil_penilaian_auditor' => $validated['hasil_penilaian_auditor'] ?? $tindakanKoreksi->hasil_penilaian_auditor,
            'tanggal_penilaian_ulang' => $validated['status'] === 'selesai' ? now()->toDateString() : $tindakanKoreksi->tanggal_penilaian_ulang,
            'p4mp_status' => $validated['status'] === 'selesai'
                ? ($tindakanKoreksi->p4mp_status ?: 'menunggu_verifikasi')
                : $tindakanKoreksi->p4mp_status,
            'verified_by_user_id' => Auth::id(),
            'verified_at' => now(),
            'tanggal_selesai' => $validated['status'] === 'selesai' ? now()->toDateString() : $tindakanKoreksi->tanggal_selesai,
        ]);

        if ($validated['status'] === 'selesai') {
            $this->kirimNotifikasiTkMenungguP4mp($tindakanKoreksi->fresh('penugasan.upt')->penugasan);
        }

        return back()->with('success', 'Status tindakan koreksi berhasil diperbarui.');
    }

    public function downloadBukti(string $tindakanKoreksiId)
    {
        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        $this->getPenugasanAuditor($tindakanKoreksi->penugasan_id);

        abort_unless($tindakanKoreksi->bukti_file_path && Storage::disk('local')->exists($tindakanKoreksi->bukti_file_path), 404);

        return Storage::disk('local')->download($tindakanKoreksi->bukti_file_path, $tindakanKoreksi->bukti_nama_file);
    }

    public function previewBukti(string $tindakanKoreksiId)
    {
        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        $this->getPenugasanAuditor($tindakanKoreksi->penugasan_id);

        abort_unless($tindakanKoreksi->bukti_file_path && Storage::disk('local')->exists($tindakanKoreksi->bukti_file_path), 404);

        $namaFile = str_replace('"', '', $tindakanKoreksi->bukti_nama_file);

        return response(Storage::disk('local')->get($tindakanKoreksi->bukti_file_path), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($tindakanKoreksi->bukti_file_path) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }
    public function generateQrCode($prefix, $registrasi)
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        // dd($encodedCode);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);
        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }

    public function export(string $penugasanId): Response
    {
        $penugasan = $this->getPenugasanAuditor($penugasanId);
        $temuan = $this->getTemuan($penugasan);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'Tindakan-Koreksi-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';
        $penugasan = Penugasan::with([
            'tindakanKoreksi',
            'auditor1',
            'auditor2',
            'periode',
        ])
            ->where('penugasan_id', $penugasanId)
            ->firstOrFail();
        $kepalaQR = $this->generateQrCode('tk_kepala||', $penugasan->penugasan_id);
        $ketuaQR = $this->generateQrCode('tk_ketua||', $penugasan->penugasan_id);
        $anggotaQR = $this->generateQrCode('tk_ketua||', $penugasan->penugasan_id);

        return Pdf::loadView('auditor.export.pdf.tindakan-koreksi', compact('penugasan', 'temuan', 'upt', 'periode','kepalaQR', 'ketuaQR', 'anggotaQR'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    public function rumuskan(Request $request, string $penugasanId, string $jawabanAuditId): RedirectResponse
    {
        $validated = $request->validate([
            'analisis_ketidaksesuaian' => 'required|string|max:5000',
            'rencana_koreksi' => 'required|string|max:5000',
        ]);

        $penugasan = $this->getPenugasanKetuaAuditor($penugasanId);
        $jawabanAudit = $this->getJawabanTemuan($penugasan, $jawabanAuditId);

        $tindakanKoreksi = TindakanKoreksi::firstOrNew([
            'penugasan_id' => $penugasan->penugasan_id,
            'jawaban_audit_id' => $jawabanAudit->id,
        ]);

        if (!$tindakanKoreksi->exists) {
            $tindakanKoreksi->tindakan_koreksi_id = Str::uuid()->toString();
        }

        $tindakanKoreksi->fill([
            'analisis_ketidaksesuaian' => $validated['analisis_ketidaksesuaian'],
            'akar_penyebab' => null,
            'rencana_koreksi' => $validated['rencana_koreksi'],
            'penanggung_jawab' => null,
            'target_selesai' => null,
            'status' => 'diajukan',
            'catatan_auditor' => null,
            'p4mp_status' => null,
            'p4mp_catatan' => null,
            'p4mp_verified_by_user_id' => null,
            'p4mp_verified_at' => null,
            'created_by_user_id' => Auth::id(),
            'verified_by_user_id' => Auth::id(),
            'verified_at' => now(),
        ]);
        $tindakanKoreksi->save();

        $this->kirimNotifikasiTkDirumuskan($penugasan);

        return back()->with('success', 'Tindakan koreksi berhasil dirumuskan dan dikirim ke auditee.');
    }

    private function getAuditor(): Auditor
    {
        return Auditor::where('user_id', Auth::id())->firstOrFail();
    }

    private function getPenugasanAuditor(string $penugasanId): Penugasan
    {
        $auditor = $this->getAuditor();

        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'auditee.user'])
            ->where('penugasan_id', $penugasanId)
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();
    }

    private function getPenugasanKetuaAuditor(string $penugasanId): Penugasan
    {
        $auditor = $this->getAuditor();

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'auditee.user'])
            ->where('penugasan_id', $penugasanId)
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();

        abort_unless(
            $penugasan->auditor_id_1 === $auditor->auditor_id,
            403,
            'Tindakan koreksi hanya dapat disusun dan diverifikasi oleh ketua auditor.'
        );

        return $penugasan;
    }

    private function getTemuan(Penugasan $penugasan)
    {
        $itemIds = UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');

        return JawabanAudit::with([
            'itemSubStandar.parent.parent.parent',
            'itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'tindakanKoreksi.p4mpVerifiedBy',
            'rkaTemuan',
        ])
            ->whereIn('upt_item_sub_standar_id', $itemIds)
            ->where('jawaban', 0)
            ->get()
            ->sortBy(fn($jawaban) => sprintf(
                '%05d-%05d-%05d',
                $jawaban->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->urutan ?? 0,
                $jawaban->itemSubStandar?->uptSubStandar?->urutan ?? 0,
                $jawaban->itemSubStandar?->urutan ?? 0
            ))
            ->map(function (JawabanAudit $jawaban) {
                $jawaban->setAttribute('item_path', $this->getItemPath($jawaban->itemSubStandar));

                return $jawaban;
            })
            ->values();
    }

    private function getItemPath(?UptItemSubStandarMutu $item): Collection
    {
        if (!$item) {
            return collect();
        }

        $path = collect();
        $current = $item;
        $guard = 0;

        while ($current && $guard < 10) {
            $path->prepend($current);
            $current = $current->parent;
            $guard++;
        }

        return $path->values();
    }

    private function getJawabanTemuan(Penugasan $penugasan, string $jawabanAuditId): JawabanAudit
    {
        $itemIds = UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');

        return JawabanAudit::where('id', $jawabanAuditId)
            ->whereIn('upt_item_sub_standar_id', $itemIds)
            ->where('jawaban', 0)
            ->firstOrFail();
    }

    private function getTemuanBelumSelesaiSiklusSebelumnya(Penugasan $penugasan)
    {
        $tahun = $penugasan->periode?->tahun;

        return TindakanKoreksi::with([
            'penugasan.periode',
            'jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
        ])
            ->where('penugasan_id', '!=', $penugasan->penugasan_id)
            ->where('status', '!=', 'selesai')
            ->whereHas('penugasan', function ($query) use ($penugasan, $tahun) {
                $query->where('upt_id', $penugasan->upt_id);

                if ($tahun) {
                    $query->whereHas('periode', fn($periodeQuery) => $periodeQuery->where('tahun', '<', $tahun));
                }
            })
            ->get();
    }

    private function kirimNotifikasiTkDirumuskan(Penugasan $penugasan): void
    {
        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "Auditor telah merumuskan tindakan koreksi untuk temuan {$namaUpt}. Silakan unggah bukti pelaksanaan setelah ditindaklanjuti.";
        $url = route('auditee.tindakan_koreksi.show', $penugasan->penugasan_id);

        $penugasan->loadMissing('upt');

        \App\Models\Auditee::with('user')
            ->where('upt_id', $penugasan->upt_id)
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->each(fn($user) => $user->notify(new PenugasanAuditNotification(
                $penugasan,
                'Tindakan Koreksi Dirumuskan',
                $pesan,
                $url,
                'tk-dirumuskan-' . $penugasan->penugasan_id
            )));
    }

    private function kirimNotifikasiTkMenungguP4mp(Penugasan $penugasan): void
    {
        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "Tindakan koreksi untuk {$namaUpt} sudah dinilai selesai oleh auditor dan menunggu verifikasi P4MP.";
        $url = route('kepala_p4mp.tindakan_koreksi.show', $penugasan->penugasan_id);

        User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->get()
            ->each(function (User $user) use ($penugasan, $pesan, $url) {
                $sudahDikirim = $user->notifications()
                    ->where('type', PenugasanAuditNotification::class)
                    ->get()
                    ->contains(fn($notifikasi) => ($notifikasi->data['jenis'] ?? null) === 'tk-menunggu-p4mp'
                        && ($notifikasi->data['penugasan_id'] ?? null) === $penugasan->penugasan_id);

                if ($sudahDikirim) {
                    return;
                }

                $user->notify(new PenugasanAuditNotification(
                    $penugasan,
                    'Verifikasi TK Menunggu P4MP',
                    $pesan,
                    $url,
                    'tk-menunggu-p4mp'
                ));
            });
    }
}
