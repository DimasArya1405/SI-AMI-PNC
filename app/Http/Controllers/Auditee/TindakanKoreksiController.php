<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\TindakanKoreksiDosen;
use App\Models\TindakanKoreksiDokumenAuditee;
use App\Models\TindakanKoreksiDokumenDosen;
use App\Models\TindakanKoreksi;
use App\Models\UptItemSubStandarMutu;
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

    public function index(Request $request): View
    {
        $auditee = $this->getAuditee();
        $periodeFilter = $this->getPeriodeFilterContext($request);
        $selectedPeriodeId = $periodeFilter['selectedPeriodeId'];

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1.user', 'auditor2.user', 'rka'])
            ->where('upt_id', $auditee->upt_id)
            ->when($selectedPeriodeId, fn ($query) => $query->where('periode_id', $selectedPeriodeId))
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $temuan = $this->getTemuan($penugasan);
                $tk = TindakanKoreksi::where('penugasan_id', $penugasan->penugasan_id)->get();

                $penugasan->setAttribute('jumlah_temuan', $temuan->count());
                $penugasan->setAttribute('tk_diajukan', $tk->whereIn('status', ['diajukan', 'disetujui', 'selesai'])->count());
                $penugasan->setAttribute('tk_selesai', $tk->where('status', 'selesai')->count());
                $penugasan->setAttribute('rka_ditandatangani', $this->rkaSudahDitandatangani($penugasan));

                return $penugasan;
            });

        return view('auditee.tindakan-koreksi.index', array_merge(compact('penugasan'), $periodeFilter));
    }

    public function show(string $penugasanId): View
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);
        $rkaDitandatangani = $this->rkaSudahDitandatangani($penugasan);
        $temuan = $this->getTemuan($penugasan);

        return view('auditee.tindakan-koreksi.show', compact('penugasan', 'temuan', 'rkaDitandatangani'));
    }

    public function uploadBukti(Request $request, string $tindakanKoreksiId): RedirectResponse
    {
        $validated = $request->validate([
            'pelaksanaan_deskripsi' => 'nullable|string|max:5000',
            'bukti_koreksi' => 'required|array',
            'bukti_koreksi.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
        ], [
            'bukti_koreksi.required' => 'File bukti wajib diupload.',
            'bukti_koreksi.array' => 'File bukti tidak valid.',
            'bukti_koreksi.*.uploaded' => 'Ukuran salah satu file terlalu besar atau gagal diupload. Maksimal 5 MB per file.',
            'bukti_koreksi.*.file' => 'File bukti tidak valid.',
            'bukti_koreksi.*.mimes' => 'Tipe file tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.',
            'bukti_koreksi.*.max' => 'Ukuran salah satu file terlalu besar. Maksimal 5 MB per file.',
        ]);

        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        $penugasan = $this->getPenugasanAuditee($tindakanKoreksi->penugasan_id);

        if (!$this->rkaSudahDitandatangani($penugasan)) {
            return back()->with('error', 'Tindakan koreksi belum dapat diunggah karena RKA belum ditandatangani Kepala P4MP.');
        }

        if ($this->isTindakanKoreksiVerified($tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Upload bukti tidak dapat dilakukan lagi.');
        }

        if ($tindakanKoreksi->bukti_file_path && $tindakanKoreksi->dokumenAuditee()
            ->where('file_path', $tindakanKoreksi->bukti_file_path)
            ->doesntExist()) {
            TindakanKoreksiDokumenAuditee::create([
                'dokumen_tk_auditee_id' => Str::uuid()->toString(),
                'tindakan_koreksi_id' => $tindakanKoreksi->tindakan_koreksi_id,
                'uploaded_by_user_id' => $tindakanKoreksi->bukti_uploaded_by_user_id,
                'nama_file' => $tindakanKoreksi->bukti_nama_file,
                'file_path' => $tindakanKoreksi->bukti_file_path,
                'keterangan' => $tindakanKoreksi->pelaksanaan_deskripsi,
            ]);
        }

        $uploadedFiles = collect($request->file('bukti_koreksi'));
        $dokumenPertama = null;

        foreach ($uploadedFiles as $file) {
            $path = $file->store('bukti-tindakan-koreksi', 'local');

            $dokumen = TindakanKoreksiDokumenAuditee::create([
                'dokumen_tk_auditee_id' => Str::uuid()->toString(),
                'tindakan_koreksi_id' => $tindakanKoreksi->tindakan_koreksi_id,
                'uploaded_by_user_id' => Auth::id(),
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'keterangan' => $validated['pelaksanaan_deskripsi'] ?? null,
            ]);

            $dokumenPertama ??= $dokumen;
        }

        $tindakanKoreksi->update([
            'bukti_nama_file' => $dokumenPertama?->nama_file ?? $tindakanKoreksi->bukti_nama_file,
            'bukti_file_path' => $dokumenPertama?->file_path ?? $tindakanKoreksi->bukti_file_path,
            'bukti_uploaded_by_user_id' => Auth::id(),
            'bukti_uploaded_at' => now(),
            'pelaksanaan_deskripsi' => $validated['pelaksanaan_deskripsi'] ?? null,
            'status' => 'diajukan',
            'p4mp_status' => null,
            'p4mp_verified_by_user_id' => null,
            'p4mp_verified_at' => null,
        ]);

        return back()->with('success', 'Bukti tindakan koreksi berhasil diunggah dan siap diverifikasi auditor.');
    }

    public function aturKebutuhanDokumenDosen(Request $request, string $tindakanKoreksiId): RedirectResponse
    {
        $validated = $request->validate([
            'butuh_dokumen_dosen' => 'nullable|boolean',
        ]);

        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->firstOrFail();

        $penugasan = $this->getPenugasanAuditee($tindakanKoreksi->penugasan_id);

        if (!$this->rkaSudahDitandatangani($penugasan)) {
            return back()->with('error', 'Dokumen dosen belum dapat diatur karena RKA belum ditandatangani Kepala P4MP.');
        }

        if ($this->isTindakanKoreksiVerified($tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Kebutuhan dokumen dosen tidak dapat diubah lagi.');
        }

        if ((bool) ($validated['butuh_dokumen_dosen'] ?? false)) {
            TindakanKoreksiDosen::firstOrCreate(
                ['tindakan_koreksi_id' => $tindakanKoreksi->tindakan_koreksi_id],
                [
                    'tindakan_koreksi_dosen_id' => Str::uuid()->toString(),
                    'penugasan_id' => $tindakanKoreksi->penugasan_id,
                    'assigned_by_user_id' => Auth::id(),
                    'assigned_at' => now(),
                ]
            );

            return back()->with('success', 'Tindakan koreksi dibuka untuk upload dokumen dosen.');
        }

        TindakanKoreksiDosen::where('tindakan_koreksi_id', $tindakanKoreksi->tindakan_koreksi_id)->delete();

        return back()->with('success', 'Tindakan koreksi tidak lagi ditampilkan ke dosen.');
    }

    public function validasiDokumenDosen(Request $request, string $dokumenId): RedirectResponse
    {
        $validated = $request->validate([
            'status_validasi' => 'required|in:diterima,ditolak',
            'catatan_validasi' => 'nullable|string|max:1000',
        ]);

        $dokumen = TindakanKoreksiDokumenDosen::with('tindakanKoreksi')
            ->where('dokumen_tk_dosen_id', $dokumenId)
            ->firstOrFail();

        $penugasan = $this->getPenugasanAuditee($dokumen->tindakanKoreksi->penugasan_id);

        if (!$this->rkaSudahDitandatangani($penugasan)) {
            return back()->with('error', 'Validasi dokumen dosen belum dapat dilakukan karena RKA belum ditandatangani Kepala P4MP.');
        }

        if ($this->isTindakanKoreksiVerified($dokumen->tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Validasi dokumen dosen tidak dapat diubah lagi.');
        }

        $dokumen->update([
            'status_validasi' => $validated['status_validasi'],
            'catatan_validasi' => $validated['catatan_validasi'] ?? null,
            'validated_by_user_id' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Status dokumen dosen berhasil diperbarui.');
    }

    public function tandaTangan(string $penugasanId): RedirectResponse
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);

        if (!$this->rkaSudahDitandatangani($penugasan)) {
            return back()->with('error', 'Tindakan koreksi belum dapat ditandatangani karena RKA belum ditandatangani Kepala P4MP.');
        }

        $temuan = $this->getTemuan($penugasan);
        $tindakanKoreksi = $temuan
            ->pluck('tindakanKoreksi')
            ->filter()
            ->values();

        if ($tindakanKoreksi->isEmpty()) {
            return back()->with('error', 'Belum ada tindakan koreksi yang bisa ditandatangani.');
        }

        $sudahPernahDitandatangani = $tindakanKoreksi->contains(fn (TindakanKoreksi $tk) => filled($tk->auditee_signed_at));

        if ($sudahPernahDitandatangani) {
            return back()->with('error', 'Tindakan koreksi sudah pernah ditandatangani auditee.');
        }

        $tindakanKoreksi->each(function (TindakanKoreksi $tk) {
            $tk->update([
                'auditee_signed_by_user_id' => Auth::id(),
                'auditee_signed_at' => now(),
            ]);
        });

        return back()->with('success', 'Tindakan koreksi berhasil ditandatangani oleh auditee.');
    }

    public function downloadBukti(string $tindakanKoreksiId)
    {
        $dokumen = $this->findDokumenAuditeeAtauLegacy($tindakanKoreksiId);

        return Storage::disk('local')->download($dokumen['file_path'], $dokumen['nama_file']);
    }

    public function previewBukti(string $tindakanKoreksiId)
    {
        $dokumen = $this->findDokumenAuditeeAtauLegacy($tindakanKoreksiId);
        $namaFile = str_replace('"', '', $dokumen['nama_file']);

        return response(Storage::disk('local')->get($dokumen['file_path']), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($dokumen['file_path']) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    public function previewDokumenDosen(string $dokumenId)
    {
        $dokumen = $this->findDokumenDosen($dokumenId);

        abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

        $namaFile = str_replace('"', '', $dokumen->nama_file);

        return response(Storage::disk('local')->get($dokumen->file_path), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($dokumen->file_path) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    public function downloadDokumenDosen(string $dokumenId)
    {
        $dokumen = $this->findDokumenDosen($dokumenId);

        abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

        return Storage::disk('local')->download($dokumen->file_path, $dokumen->nama_file);
    }

    public function export(string $penugasanId): Response
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);
        $temuan = $this->getTemuan($penugasan);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'Tindakan-Koreksi-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';
        $kepalaQR = $this->generateQrCode('tk_kepala||', $penugasan->penugasan_id);
        $ketuaQR = $this->generateQrCode('tk_ketua||', $penugasan->penugasan_id);
        $anggotaQR = $this->generateQrCode('tk_anggota||', $penugasan->penugasan_id);
        $auditeeQR = $this->generateQrCode('tk_auditee||', $penugasan->penugasan_id);
        $wadirQR = $this->generateQrCode('tk_wadir||', $penugasan->penugasan_id);

        return Pdf::loadView('auditor.export.pdf.tindakan-koreksi', compact('penugasan', 'temuan', 'upt', 'periode', 'kepalaQR', 'ketuaQR', 'anggotaQR', 'auditeeQR', 'wadirQR'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    private function generateQrCode(string $prefix, string $registrasi): string
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);

        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }

    private function getAuditee(): Auditee
    {
        return Auditee::with('upt')
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    private function getPenugasanAuditee(string $penugasanId): Penugasan
    {
        $auditee = $this->getAuditee();

        return Penugasan::with(['periode', 'upt', 'auditor1.user', 'auditor2.user', 'auditee.user', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();
    }

    private function getTemuan(Penugasan $penugasan)
    {
        $itemIds = $this->getItemIds($penugasan);

        return JawabanAudit::with([
            'itemSubStandar.parent.parent.parent',
            'itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'tindakanKoreksi.p4mpVerifiedBy',
            'tindakanKoreksi.dokumenAuditee.uploadedBy',
            'tindakanKoreksi.kebutuhanDokumenDosen',
            'tindakanKoreksi.dokumenDosen.dosen',
            'tindakanKoreksi.dokumenDosen.uploadedBy',
            'tindakanKoreksi.dokumenDosen.validatedBy',
            'rkaTemuan',
        ])
            ->whereIn('upt_item_sub_standar_id', $itemIds)
            ->where('jawaban', 0)
            ->get()
            ->sortBy(fn ($jawaban) => sprintf(
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

    private function rkaSudahDitandatangani(Penugasan $penugasan): bool
    {
        $rka = $penugasan->relationLoaded('rka')
            ? $penugasan->rka
            : $penugasan->rka()->first();

        return $rka
            && $rka->status === 'final'
            && (string) $rka->acc_p4mp === '1';
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

    private function getItemIds(Penugasan $penugasan)
    {
        return UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');
    }

    private function findDokumenDosen(string $dokumenId): TindakanKoreksiDokumenDosen
    {
        $dokumen = TindakanKoreksiDokumenDosen::with('tindakanKoreksi')
            ->where('dokumen_tk_dosen_id', $dokumenId)
            ->firstOrFail();

        $this->getPenugasanAuditee($dokumen->tindakanKoreksi->penugasan_id);

        return $dokumen;
    }

    private function findDokumenAuditeeAtauLegacy(string $id): array
    {
        $dokumen = TindakanKoreksiDokumenAuditee::with('tindakanKoreksi')
            ->where('dokumen_tk_auditee_id', $id)
            ->first();

        if ($dokumen) {
            $this->getPenugasanAuditee($dokumen->tindakanKoreksi->penugasan_id);

            abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

            return [
                'nama_file' => $dokumen->nama_file,
                'file_path' => $dokumen->file_path,
            ];
        }

        $tindakanKoreksi = TindakanKoreksi::with('penugasan')
            ->where('tindakan_koreksi_id', $id)
            ->firstOrFail();

        $this->getPenugasanAuditee($tindakanKoreksi->penugasan_id);

        abort_unless($tindakanKoreksi->bukti_file_path && Storage::disk('local')->exists($tindakanKoreksi->bukti_file_path), 404);

        return [
            'nama_file' => $tindakanKoreksi->bukti_nama_file,
            'file_path' => $tindakanKoreksi->bukti_file_path,
        ];
    }

    private function isTindakanKoreksiVerified(?TindakanKoreksi $tindakanKoreksi): bool
    {
        return $tindakanKoreksi
            && ($tindakanKoreksi->p4mp_status === 'terverifikasi' || filled($tindakanKoreksi->p4mp_verified_at));
    }

    private function hasBuktiPelaksanaan(TindakanKoreksi $tindakanKoreksi): bool
    {
        $adaDokumenDosenDiterima = $tindakanKoreksi->relationLoaded('dokumenDosen')
            && $tindakanKoreksi->dokumenDosen?->contains(fn ($dokumen) => $dokumen->status_validasi === 'diterima');

        $adaDokumenAuditee = $tindakanKoreksi->relationLoaded('dokumenAuditee')
            && $tindakanKoreksi->dokumenAuditee?->isNotEmpty();

        return filled($tindakanKoreksi->bukti_file_path)
            || $adaDokumenAuditee
            || filled($tindakanKoreksi->pelaksanaan_deskripsi)
            || $adaDokumenDosenDiterima;
    }
}
