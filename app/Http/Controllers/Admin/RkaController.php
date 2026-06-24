<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\RingkasanKondisiAudit;
use App\Models\UptItemSubStandarMutu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Milon\Barcode\Facades\DNS2DFacade;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Concerns\PeriodeFilterSupport;

class RkaController extends Controller
{
    use PeriodeFilterSupport;

    public function index(Request $request): View
    {
        $periodeFilter = $this->getPeriodeFilterContext($request);
        $selectedPeriodeId = $periodeFilter['selectedPeriodeId'];
        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->when($selectedPeriodeId, fn ($query) => $query->where('periode_id', $selectedPeriodeId))
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $progress = $this->getAuditProgress($penugasan);
                $statusRka = $penugasan->rka?->status ?? 'belum_dibuat';

                $penugasan->setAttribute('total_item', $progress['total_item']);
                $penugasan->setAttribute('item_terjawab', $progress['item_terjawab']);
                $penugasan->setAttribute('persentase', $progress['persentase']);
                $penugasan->setAttribute('penilaian_selesai', $progress['selesai']);
                $penugasan->setAttribute('status_rka', $statusRka);

                return $penugasan;
            });

        $ringkasan = [
            'total_penugasan' => $penugasan->count(),
            'rka_final' => $penugasan->filter(fn ($item) => $item->status_rka === 'final')->count(),
            'rka_draft' => $penugasan->filter(fn ($item) => $item->status_rka === 'draft')->count(),
            'belum_rka' => $penugasan->filter(fn ($item) => $item->status_rka === 'belum_dibuat')->count(),
        ];

        return view('admin.rka.index', array_merge(compact('penugasan', 'ringkasan'), $periodeFilter));
    }

    public function show(string $penugasanId): View
    {
        $penugasan = $this->getPenugasan($penugasanId);
        $rka = $this->getRka($penugasan);
        $progress = $this->getAuditProgress($penugasan);

        $rka->load([
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'createdBy',
            'finalizedBy',
        ]);

        $ringkasan = $this->getRingkasan($progress['item_ids'], $rka);
        $temuanPerStandar = $this->getTemuanPerStandar($rka);

        return view('admin.rka.show', compact('penugasan', 'rka', 'ringkasan', 'temuanPerStandar'));
    }

    // public function export(string $penugasanId): Response
    // {
    //     $penugasan = $this->getPenugasan($penugasanId);
    //     $rka = $this->getRka($penugasan);

    //     $rka->load([
    //         'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
    //         'finalizedBy',
    //     ]);

    //     $upt = $penugasan->upt;
    //     $periode = $penugasan->periode;
    //     $namaFile = 'RKA-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';

    //     return Pdf::loadView('auditor.export.pdf.rka', compact('rka', 'upt', 'periode', 'penugasan'))
    //         ->setPaper('a4', 'portrait')
    //         ->stream($namaFile);
    // }
        private function getPenugasanAuditor(string $penugasanId): Penugasan
    {

        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->firstOrFail();
    }
        public function generateQrCode($prefix, $registrasi)
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        // dd($encodedCode);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);
        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }
        public function export(string $rkaId): Response
    {
        $rka = RingkasanKondisiAudit::with([
            'penugasan.upt',
            'penugasan.periode',
            'penugasan.auditor1',
            'penugasan.auditor2',
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
        ])
            ->where('rka_id', $rkaId)
            ->firstOrFail();

        $penugasan = $this->getPenugasanAuditor($rka->penugasan_id);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'RKA-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';
        $ketuaAuditorQR = $this->generateQrCode('rka_ketua||', $rka->penugasan_id);
        $anggotaAuditorQR = $this->generateQrCode('rka_anggota||', $rka->penugasan_id);
        $kepalaQR = $this->generateQrCode('rka_kepala||', $rka->penugasan_id);

        return Pdf::loadView('auditor.export.pdf.rka', compact('rka', 'upt', 'periode', 'penugasan', 'ketuaAuditorQR', 'anggotaAuditorQR', 'kepalaQR'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    private function getPenugasan(string $penugasanId): Penugasan
    {
        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->firstOrFail();
    }

    private function getRka(Penugasan $penugasan): RingkasanKondisiAudit
    {
        return RingkasanKondisiAudit::where('penugasan_id', $penugasan->penugasan_id)
            ->firstOrFail();
    }

    private function getAuditProgress(Penugasan $penugasan): array
    {
        $itemIds = $this->getItemIds($penugasan);
        $totalItem = $itemIds->count();
        $itemTerjawab = JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)
            ->distinct()
            ->count('upt_item_sub_standar_id');

        return [
            'item_ids' => $itemIds,
            'total_item' => $totalItem,
            'item_terjawab' => $itemTerjawab,
            'persentase' => $totalItem > 0 ? (int) round(($itemTerjawab / $totalItem) * 100) : 0,
            'selesai' => $totalItem > 0 && $itemTerjawab >= $totalItem,
        ];
    }

    private function getItemIds(Penugasan $penugasan): Collection
    {
        return UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');
    }

    private function getRingkasan(Collection $itemIds, RingkasanKondisiAudit $rka): array
    {
        $jawabanAudit = JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)
            ->get()
            ->unique('upt_item_sub_standar_id');

        return [
            'total_item' => $itemIds->count(),
            'sesuai' => $jawabanAudit->filter(fn ($jawaban) => (int) $jawaban->jawaban === 1)->count(),
            'temuan' => $rka->temuan->count(),
            'kts' => $rka->temuan->where('kategori_final', 'KTS')->count(),
            'ob' => $rka->temuan->where('kategori_final', 'OB')->count(),
        ];
    }

    private function getTemuanPerStandar(RingkasanKondisiAudit $rka): Collection
    {
        return $rka->temuan
            ->sortBy(function ($temuan) {
                $item = $temuan->jawabanAudit?->itemSubStandar;
                $standar = $item?->uptSubStandar?->uptStandarMutu?->standar_mutu;

                return sprintf(
                    '%05d-%05d-%05d',
                    $standar?->urutan ?? 0,
                    $item?->uptSubStandar?->urutan ?? 0,
                    $item?->urutan ?? 0
                );
            })
            ->groupBy(fn ($temuan) => $temuan->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->standar_mutu_id ?? 'tanpa-standar')
            ->map(function ($temuan) {
                $standar = $temuan->first()?->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu;

                return [
                    'nama_standar' => $standar?->nama_standar_mutu ?? '-',
                    'temuan' => $temuan->values(),
                ];
            })
            ->values();
    }
}
