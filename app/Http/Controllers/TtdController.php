<?php

namespace App\Http\Controllers;

use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\RingkasanKondisiAudit;
use App\Models\UptItemSubStandarMutu;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Symfony\Component\HttpFoundation\Response;

class TtdController extends Controller
{
    public function ttdShow(Request $request)
    {
        [$prefix, $uuid, $decodedCode] = $this->decodeTtdCode($request);
        $penugasan = $this->getPenugasan($uuid);

        $signature = $this->getSignatureData($prefix, $penugasan);
        $tgl = $signature['tgl'];
        $tanggalTandaTangan = $tgl
            ? Carbon::parse($tgl)->locale('id')->translatedFormat('l, d F Y')
            : '-';
        $jamTandaTangan = $tgl
            ? Carbon::parse($tgl)->locale('id')->translatedFormat('H:i')
            : '-';
        $downloadUrl = route('ttdcode.download', ['ttdcode' => $request->query('ttdcode')]);
        $nilaiHash = sha1($decodedCode);

        return view('ttd.index', array_merge($signature, compact(
            'penugasan',
            'tanggalTandaTangan',
            'jamTandaTangan',
            'downloadUrl',
            'nilaiHash'
        )));
    }

    public function download(Request $request): Response
    {
        [$prefix, $uuid] = $this->decodeTtdCode($request);
        $penugasan = $this->getPenugasan($uuid);

        if (str_starts_with($prefix, 'rka_')) {
            return $this->streamRka($penugasan);
        }

        if (str_starts_with($prefix, 'tk_')) {
            return $this->streamTindakanKoreksi($penugasan);
        }

        if ($prefix === 'penugasan_kepala') {
            return $this->streamPenugasan($penugasan);
        }

        abort(404);
    }

    private function decodeTtdCode(Request $request): array
    {
        $ttdcode = (string) $request->query('ttdcode', '');
        $decodedCode = base64_decode($ttdcode, true);

        abort_if($decodedCode === false || !str_contains($decodedCode, '||'), 404);

        [$prefix, $uuid] = explode('||', $decodedCode, 2);

        abort_if($prefix === '' || $uuid === '', 404);

        return [$prefix, $uuid, $decodedCode];
    }

    private function getPenugasan(string $uuid): Penugasan
    {
        return Penugasan::with([
            'periode',
            'auditor1',
            'auditor2',
            'auditee.user',
            'upt',
            'rka',
            'tindakanKoreksi',
            'verifikasiTindakanKoreksi',
        ])
            ->where('penugasan_id', $uuid)
            ->firstOrFail();
    }

    private function getSignatureData(string $prefix, Penugasan $penugasan): array
    {
        $tindakanKoreksi = $penugasan->tindakanKoreksi?->first();
        $verifikasiTk = $penugasan->verifikasiTindakanKoreksi;
        $kepalaP4mp = $this->getKepalaP4mp();
        $tahun = $penugasan->periode?->tahun ?? '-';
        $judul = match (true) {
            str_starts_with($prefix, 'rka_') => 'Ringkasan Kondisi Audit',
            str_starts_with($prefix, 'tk_') => 'Tindakan Koreksi',
            $prefix === 'penugasan_kepala' => 'Jadwal Audit Mutu Internal',
            default => abort(404),
        };
        $lingkup = $prefix === 'penugasan_kepala'
            ? 'Seluruh Jadwal AMI Periode ' . $tahun
            : $this->getLingkupPenugasan($penugasan);

        if ($prefix === 'rka_ketua') {
            $nama = $penugasan->auditor1?->nama_lengkap ?? '-';
            $jabatan = 'Ketua Auditor';
            $tgl = $penugasan->rka?->finalized_at;
        } elseif ($prefix === 'rka_anggota') {
            $nama = $penugasan->auditor2?->nama_lengkap ?? '-';
            $jabatan = 'Anggota Auditor';
            $tgl = $penugasan->rka?->finalized_at;
        } elseif ($prefix === 'rka_kepala') {
            $nama = $kepalaP4mp?->name ?? 'Kepala P4MP';
            $jabatan = 'Kepala P4MP';
            $tgl = $penugasan->rka?->acc_p4mp_at;
        } elseif ($prefix === 'tk_kepala') {
            $nama = $kepalaP4mp?->name ?? 'Kepala P4MP';
            $jabatan = 'Kepala P4MP';
            $tgl = $verifikasiTk?->finalized_at ?? $tindakanKoreksi?->p4mp_verified_at;
        } elseif ($prefix === 'tk_wadir') {
            $nama = trim((string) ($verifikasiTk?->wadir1_nama ?: $tindakanKoreksi?->wadir1_nama));
            abort_if($nama === '', 404);

            $jabatan = 'Wadir I';
            $tgl = $verifikasiTk?->finalized_at ?: $tindakanKoreksi?->p4mp_verified_at;
        } elseif ($prefix === 'tk_ketua') {
            $nama = $penugasan->auditor1?->nama_lengkap ?? '-';
            $jabatan = 'Ketua Auditor';
            $tgl = $tindakanKoreksi?->verified_at;
        } elseif ($prefix === 'tk_anggota') {
            $nama = $penugasan->auditor2?->nama_lengkap ?? '-';
            $jabatan = 'Anggota Auditor';
            $tgl = $tindakanKoreksi?->verified_at;
        } elseif ($prefix === 'tk_auditee') {
            $signedTk = $penugasan->tindakanKoreksi?->first(fn ($tk) => filled($tk->auditee_signed_at));
            $nama = $penugasan->auditee?->nama_lengkap
                ?? $penugasan->auditee?->user?->name
                ?? $penugasan->upt?->nama_upt
                ?? 'Auditee';
            $jabatan = 'Auditee / Kepala Unit Kerja';
            $tgl = $signedTk?->auditee_signed_at;
        } elseif ($prefix === 'penugasan_kepala') {
            $nama = $kepalaP4mp?->name ?? 'Kepala P4MP';
            $jabatan = 'Kepala P4MP';
            $tgl = $penugasan->acc_kepala_p4mp_at;
        } else {
            abort(404);
        }

        return [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'judul' => $judul,
            'tahun' => $tahun,
            'tgl' => $tgl,
            'lingkup' => $lingkup,
            'unitKerja' => $penugasan->upt?->nama_upt ?? '-',
            'instansi' => 'Politeknik Negeri Cilacap',
        ];
    }

    private function streamRka(Penugasan $penugasan): Response
    {
        $rka = RingkasanKondisiAudit::with([
            'penugasan.upt',
            'penugasan.periode',
            'penugasan.auditor1',
            'penugasan.auditor2',
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
        ])
            ->where('penugasan_id', $penugasan->penugasan_id)
            ->firstOrFail();

        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'RKA-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';
        $ketuaAuditorQR = $this->generateQrCode('rka_ketua||', $penugasan->penugasan_id);
        $anggotaAuditorQR = $this->generateQrCode('rka_anggota||', $penugasan->penugasan_id);
        $kepalaQR = $this->generateQrCode('rka_kepala||', $penugasan->penugasan_id);

        return Pdf::loadView('auditor.export.pdf.rka', compact(
            'rka',
            'upt',
            'periode',
            'penugasan',
            'ketuaAuditorQR',
            'anggotaAuditorQR',
            'kepalaQR'
        ))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    private function streamTindakanKoreksi(Penugasan $penugasan): Response
    {
        $penugasan->load(['periode', 'upt', 'auditor1', 'auditor2', 'verifikasiTindakanKoreksi.finalizedBy', 'tindakanKoreksi']);

        $temuan = $this->getTemuan($penugasan);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'Tindakan-Koreksi-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';
        $kepalaQR = $this->generateQrCode('tk_kepala||', $penugasan->penugasan_id);
        $ketuaQR = $this->generateQrCode('tk_ketua||', $penugasan->penugasan_id);
        $anggotaQR = $this->generateQrCode('tk_anggota||', $penugasan->penugasan_id);
        $auditeeQR = $this->generateQrCode('tk_auditee||', $penugasan->penugasan_id);
        $wadirQR = $this->generateQrCode('tk_wadir||', $penugasan->penugasan_id);

        return Pdf::loadView('auditor.export.pdf.tindakan-koreksi', compact(
            'penugasan',
            'temuan',
            'upt',
            'periode',
            'kepalaQR',
            'ketuaQR',
            'anggotaQR',
            'auditeeQR',
            'wadirQR'
        ))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    private function streamPenugasan(Penugasan $penugasan): Response
    {
        $periode = $penugasan->periode;
        $penugasanPeriode = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2'])
            ->where('periode_id', $periode->id)
            ->orderBy('tanggal_audit')
            ->orderBy('jam')
            ->get();

        $uptProdi = $this->kelompokkanPenugasanPerUpt($penugasanPeriode, 'Prodi');
        $uptBagian = $this->kelompokkanPenugasanPerUpt($penugasanPeriode, 'Unit/Bagian');
        $tahun = $periode->tahun;
        $sudahDitandatangani = $penugasanPeriode->isNotEmpty()
            && $penugasanPeriode->every(fn (Penugasan $item) => $item->acc_kepala_p4mp === '1');
        $tanggalTtd = $penugasanPeriode->firstWhere('acc_kepala_p4mp', '1')
            ?->acc_kepala_p4mp_at
            ?->locale('id')
            ->translatedFormat('d F Y');
        $kepalaP4mpName = $this->getKepalaP4mp()?->name ?? 'Kepala P4MP';
        $penugasanQR = $sudahDitandatangani
            ? $this->generateQrCode('penugasan_kepala||', $penugasan->penugasan_id)
            : null;

        return Pdf::loadView('admin.export.pdf.penugasan', compact(
            'uptProdi',
            'uptBagian',
            'tahun',
            'kepalaP4mpName',
            'sudahDitandatangani',
            'tanggalTtd',
            'penugasanQR'
        ))
            ->setPaper('a4', 'portrait')
            ->stream('Jadwal-AMI-PNC-' . $tahun . '.pdf');
    }

    private function getTemuan(Penugasan $penugasan): Collection
    {
        return JawabanAudit::with([
            'itemSubStandar.parent.parent.parent',
            'itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'tindakanKoreksi.buktiUploadedBy',
            'tindakanKoreksi.verifiedBy',
            'tindakanKoreksi.p4mpVerifiedBy',
            'tindakanKoreksi.dokumenAuditee.uploadedBy',
            'tindakanKoreksi.dokumenDosen' => fn ($query) => $query
                ->where('status_validasi', 'diterima')
                ->with(['dosen', 'uploadedBy', 'validatedBy']),
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

    private function getItemIds(Penugasan $penugasan): Collection
    {
        return UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');
    }

    private function kelompokkanPenugasanPerUpt(Collection $penugasan, string $kategori): Collection
    {
        return $penugasan
            ->filter(fn (Penugasan $item) => $item->upt?->kategori_upt === $kategori)
            ->map(function (Penugasan $item) {
                $upt = $item->upt;
                $upt->setRelation('penugasan', collect([$item]));

                return $upt;
            })
            ->values();
    }

    private function generateQrCode(string $prefix, string $registrasi): string
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);

        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }

    private function getKepalaP4mp(): ?User
    {
        return User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->first()
            ?: User::where('role', 'kepala_p4mp')->first();
    }

    private function getLingkupPenugasan(Penugasan $penugasan): string
    {
        $kategori = $penugasan->upt?->kategori_upt === 'Prodi' ? 'Prodi' : 'Unit/Bagian';

        return $kategori . ' ' . ($penugasan->upt?->nama_upt ?? '-');
    }
}
