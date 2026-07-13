<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\TindakanKoreksi;
use App\Models\TindakanKoreksiDokumenAuditee;
use App\Models\TindakanKoreksiDokumenDosen;
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
use App\Http\Controllers\Concerns\PeriodeFilterSupport;

class TindakanKoreksiController extends Controller
{
    use PeriodeFilterSupport;

    // Menampilkan daftar tindakan koreksi dari penugasan auditor.
    public function index(Request $request): View
    {
        $auditor = $this->getAuditor();
        $periodeFilter = $this->getPeriodeFilterContext($request);
        $selectedPeriodeId = $periodeFilter['selectedPeriodeId'];

        $penugasan = Penugasan::with(['periode', 'upt', 'rka'])
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->when($selectedPeriodeId, fn ($query) => $query->where('periode_id', $selectedPeriodeId))
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $temuan = $this->getTemuan($penugasan);
                $tk = TindakanKoreksi::where('penugasan_id', $penugasan->penugasan_id)->get();

                $penugasan->setAttribute('jumlah_temuan', $temuan->count());
                $penugasan->setAttribute('tk_menunggu', $tk->where('status', 'diajukan')->count());
                $penugasan->setAttribute('tk_selesai', $tk->where('status', 'selesai')->count());
                $penugasan->setAttribute('rka_ditandatangani', $this->rkaSudahDitandatangani($penugasan));

                return $penugasan;
            });

        return view('auditor.tindakan-koreksi.index', array_merge(compact('penugasan'), $periodeFilter));
    }

    // Menampilkan detail temuan dan form tindakan koreksi per penugasan.
    public function show(string $penugasanId): View
    {
        $auditor = $this->getAuditor();
        $penugasan = $this->getPenugasanAuditor($penugasanId);
        $isKetuaAuditor = $penugasan->auditor_id_1 === $auditor->auditor_id;
        $rkaDitandatangani = $this->rkaSudahDitandatangani($penugasan);
        $periodeAktif = $this->isPeriodeAktif($penugasan);
        $temuan = $this->getTemuan($penugasan);
        $carryForward = $this->getTemuanBelumSelesaiSiklusSebelumnya($penugasan);

        return view('auditor.tindakan-koreksi.show', compact('penugasan', 'temuan', 'carryForward', 'isKetuaAuditor', 'rkaDitandatangani', 'periodeAktif'));
    }

    // Menyimpan hasil penilaian ulang auditor terhadap bukti tindakan koreksi auditee.
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

        if ($this->isTindakanKoreksiVerified($tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Penilaian auditor tidak dapat diubah lagi.');
        }

        $penugasan = $this->getPenugasanKetuaAuditor($tindakanKoreksi->penugasan_id);

        if (!$this->isPeriodeAktif($penugasan)) {
            return back()->with('error', 'Tindakan koreksi periode yang tidak aktif tidak dapat diubah.');
        }

        if (!$this->rkaSudahDitandatangani($penugasan)) {
            return back()->with('error', 'Tindakan koreksi baru bisa diverifikasi setelah RKA ditandatangani Kepala P4MP.');
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

    // Mengunduh bukti tindakan koreksi yang dikirim oleh auditee.
    public function downloadBukti(string $tindakanKoreksiId)
    {
        $dokumen = $this->findDokumenAuditeeAtauLegacy($tindakanKoreksiId);

        return Storage::disk('local')->download($dokumen['file_path'], $dokumen['nama_file']);
    }

    // Menampilkan bukti auditee langsung di browser tanpa download paksa.
    public function previewBukti(string $tindakanKoreksiId)
    {
        $dokumen = $this->findDokumenAuditeeAtauLegacy($tindakanKoreksiId);
        $namaFile = str_replace('"', '', $dokumen['nama_file']);

        return response(Storage::disk('local')->get($dokumen['file_path']), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($dokumen['file_path']) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    // Menampilkan dokumen dosen yang sudah diterima oleh auditee.
    public function previewDokumenDosen(string $dokumenId)
    {
        $dokumen = TindakanKoreksiDokumenDosen::with('tindakanKoreksi')
            ->where('dokumen_tk_dosen_id', $dokumenId)
            ->where('status_validasi', 'diterima')
            ->firstOrFail();

        $this->getPenugasanAuditor($dokumen->tindakanKoreksi->penugasan_id);

        abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

        $namaFile = str_replace('"', '', $dokumen->nama_file);

        return response(Storage::disk('local')->get($dokumen->file_path), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($dokumen->file_path) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    // Mengunduh dokumen dosen yang sudah diterima oleh auditee.
    public function downloadDokumenDosen(string $dokumenId)
    {
        $dokumen = TindakanKoreksiDokumenDosen::with('tindakanKoreksi')
            ->where('dokumen_tk_dosen_id', $dokumenId)
            ->where('status_validasi', 'diterima')
            ->firstOrFail();

        $this->getPenugasanAuditor($dokumen->tindakanKoreksi->penugasan_id);

        abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

        return Storage::disk('local')->download($dokumen->file_path, $dokumen->nama_file);
    }

    // Membuat QR code tanda tangan digital untuk export PDF tindakan koreksi.
    public function generateQrCode($prefix, $registrasi)
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);
        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }

    // Membuat file PDF tindakan koreksi sesuai format formulir AMI.
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
        $anggotaQR = $this->generateQrCode('tk_anggota||', $penugasan->penugasan_id);
        $auditeeQR = $this->generateQrCode('tk_auditee||', $penugasan->penugasan_id);
        $wadirQR = $this->generateQrCode('tk_wadir||', $penugasan->penugasan_id);

        return Pdf::loadView('auditor.export.pdf.tindakan-koreksi', compact('penugasan', 'temuan', 'upt', 'periode','kepalaQR', 'ketuaQR', 'anggotaQR', 'auditeeQR', 'wadirQR'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    // Ketua auditor merumuskan analisis dan usulan tindakan koreksi untuk tiap temuan.
    public function rumuskan(Request $request, string $penugasanId, string $jawabanAuditId): RedirectResponse
    {
        $validated = $request->validate([
            'analisis_ketidaksesuaian' => 'required|string|max:5000',
            'rencana_koreksi' => 'required|string|max:5000',
        ]);

        $penugasan = $this->getPenugasanKetuaAuditor($penugasanId);

        if (!$this->isPeriodeAktif($penugasan)) {
            return back()->with('error', 'Tindakan koreksi periode yang tidak aktif tidak dapat diubah.');
        }

        if (!$this->rkaSudahDitandatangani($penugasan)) {
            return back()->with('error', 'Tindakan koreksi baru bisa disusun setelah RKA ditandatangani Kepala P4MP.');
        }

        $jawabanAudit = $this->getJawabanTemuan($penugasan, $jawabanAuditId);

        $tindakanKoreksi = TindakanKoreksi::firstOrNew([
            'penugasan_id' => $penugasan->penugasan_id,
            'jawaban_audit_id' => $jawabanAudit->id,
        ]);

        if ($this->isTindakanKoreksiVerified($tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Analisis dan usulan tidak dapat diubah lagi.');
        }

        if (!$tindakanKoreksi->exists) {
            $tindakanKoreksi->tindakan_koreksi_id = Str::uuid()->toString();
        }

        $tindakanKoreksi->fill([
            'analisis_ketidaksesuaian' => $validated['analisis_ketidaksesuaian'],
            'akar_penyebab' => null,
            'rencana_koreksi' => $validated['rencana_koreksi'],
            'penanggung_jawab' => null,
            'target_selesai' => null,
            'status' => $tindakanKoreksi->exists ? $tindakanKoreksi->status : 'diajukan',
            'created_by_user_id' => Auth::id(),
        ]);

        if (!$tindakanKoreksi->exists) {
            $tindakanKoreksi->fill([
                'catatan_auditor' => null,
                'p4mp_status' => null,
                'p4mp_catatan' => null,
                'p4mp_verified_by_user_id' => null,
                'p4mp_verified_at' => null,
                'verified_by_user_id' => Auth::id(),
                'verified_at' => now(),
            ]);
        }

        $tindakanKoreksi->save();

        $this->kirimNotifikasiTkDirumuskan($penugasan);

        return back()->with('success', 'Tindakan koreksi berhasil dirumuskan dan dikirim ke auditee.');
    }

    // Mengambil data auditor berdasarkan user yang sedang login.
    private function getAuditor(): Auditor
    {
        return Auditor::where('user_id', Auth::id())->firstOrFail();
    }

    // Memastikan penugasan memang dapat diakses oleh auditor yang sedang login.
    private function getPenugasanAuditor(string $penugasanId): Penugasan
    {
        $auditor = $this->getAuditor();

        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'auditee.user', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();
    }

    // Memastikan hanya ketua auditor yang dapat menyusun dan menilai tindakan koreksi.
    private function getPenugasanKetuaAuditor(string $penugasanId): Penugasan
    {
        $auditor = $this->getAuditor();

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'auditee.user', 'rka'])
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

    // Mengambil semua temuan audit yang menjadi dasar tindakan koreksi.
    private function getTemuan(Penugasan $penugasan)
    {
        $itemIds = UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');

        return JawabanAudit::with([
            'itemSubStandar.parent.parent.parent',
            'itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'tindakanKoreksi' => function ($query) use ($penugasan) {
                $query->where('penugasan_id', $penugasan->penugasan_id)
                    ->with([
                        'p4mpVerifiedBy',
                        'dokumenAuditee.uploadedBy',
                        'dokumenDosen' => function ($query) {
                            $query->with('dosen')
                                ->where('status_validasi', 'diterima')
                                ->latest();
                        },
                    ]);
            },
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

    // Mengecek apakah RKA sudah final dan ditandatangani Kepala P4MP.
    private function rkaSudahDitandatangani(Penugasan $penugasan): bool
    {
        $rka = $penugasan->relationLoaded('rka')
            ? $penugasan->rka
            : $penugasan->rka()->first();

        return $rka
            && $rka->status === 'final'
            && (string) $rka->acc_p4mp === '1';
    }

    // Mengecek apakah tindakan koreksi sudah diverifikasi P4MP sehingga tidak boleh diubah.
    private function isTindakanKoreksiVerified(?TindakanKoreksi $tindakanKoreksi): bool
    {
        return $tindakanKoreksi
            && ($tindakanKoreksi->p4mp_status === 'terverifikasi' || filled($tindakanKoreksi->p4mp_verified_at));
    }

    // Mengecek periode aktif agar data periode lama hanya bisa dilihat, bukan diubah.
    private function isPeriodeAktif(Penugasan $penugasan): bool
    {
        $periode = $penugasan->relationLoaded('periode')
            ? $penugasan->periode
            : $penugasan->periode()->first();

        return (string) ($periode?->status) === '1';
    }

    // Mengambil parent item agar temuan sub item tetap punya konteks pertanyaannya.
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

    // Memastikan item yang dipilih benar-benar temuan pada penugasan tersebut.
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

    // Mengambil temuan tahun sebelumnya yang belum selesai untuk monitoring lintas siklus.
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

    // Mengambil dokumen auditee dari tabel baru atau kolom lama agar data lama tetap terbaca.
    private function findDokumenAuditeeAtauLegacy(string $id): array
    {
        $dokumen = TindakanKoreksiDokumenAuditee::with('tindakanKoreksi')
            ->where('dokumen_tk_auditee_id', $id)
            ->first();

        if ($dokumen) {
            $this->getPenugasanAuditor($dokumen->tindakanKoreksi->penugasan_id);

            abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

            return [
                'nama_file' => $dokumen->nama_file,
                'file_path' => $dokumen->file_path,
            ];
        }

        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $id)
            ->firstOrFail();

        $this->getPenugasanAuditor($tindakanKoreksi->penugasan_id);

        abort_unless($tindakanKoreksi->bukti_file_path && Storage::disk('local')->exists($tindakanKoreksi->bukti_file_path), 404);

        return [
            'nama_file' => $tindakanKoreksi->bukti_nama_file,
            'file_path' => $tindakanKoreksi->bukti_file_path,
        ];
    }

    // Mengirim notifikasi ke auditee setelah tindakan koreksi dirumuskan auditor.
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

    // Mengirim notifikasi ke Kepala P4MP saat tindakan koreksi siap diverifikasi.
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
