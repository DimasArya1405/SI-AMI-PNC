<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\RingkasanKondisiAudit;
use App\Models\UptItemSubStandarMutu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RkaController extends Controller
{
    public function index(): View
    {
        $auditee = $this->getAuditee();

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where('upt_id', $auditee->upt_id)
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $progress = $this->getAuditProgress($penugasan);
                $rkaFinal = $penugasan->rka?->status === 'final';

                $penugasan->setAttribute('total_item', $progress['total_item']);
                $penugasan->setAttribute('item_terjawab', $progress['item_terjawab']);
                $penugasan->setAttribute('persentase', $progress['persentase']);
                $penugasan->setAttribute('penilaian_selesai', $progress['selesai']);
                $penugasan->setAttribute('rka_tersedia', $rkaFinal);

                return $penugasan;
            });

        return view('auditee.rka.index', compact('auditee', 'penugasan'));
    }

    public function show(string $penugasanId): View
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);
        $rka = $this->getRkaFinal($penugasan);
        $progress = $this->getAuditProgress($penugasan);
        $rka->load([
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'finalizedBy',
        ]);

        $ringkasanStandar = $rka->temuan
            ->groupBy(fn ($temuan) => $temuan->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->nama_standar_mutu ?? '-')
            ->map(fn ($temuan, $namaStandar) => [
                'nama_standar' => $namaStandar,
                'temuan' => $temuan,
            ])
            ->values();

        $ringkasan = $this->getRingkasan($progress['item_ids'], $rka);

        return view('auditee.rka.show', compact('penugasan', 'rka', 'ringkasanStandar', 'ringkasan'));
    }

    public function export(string $penugasanId): Response
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);
        $rka = $this->getRkaFinal($penugasan);
        $rka->load([
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'finalizedBy',
        ]);

        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'RKA-' . Str::slug($upt->nama_upt) . '-' . $periode->tahun . '.pdf';

        return Pdf::loadView('auditor.export.pdf.rka', compact('rka', 'upt', 'periode', 'penugasan'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
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

        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();
    }

    private function getRkaFinal(Penugasan $penugasan): RingkasanKondisiAudit
    {
        return RingkasanKondisiAudit::where('penugasan_id', $penugasan->penugasan_id)
            ->where('status', 'final')
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
}
