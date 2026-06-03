<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2'])
            ->where('upt_id', $auditee->upt_id)
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) {
                $progress = $this->getAuditProgress($penugasan);

                $penugasan->setAttribute('total_item', $progress['total_item']);
                $penugasan->setAttribute('item_terjawab', $progress['item_terjawab']);
                $penugasan->setAttribute('persentase', $progress['persentase']);
                $penugasan->setAttribute('rka_tersedia', $progress['selesai']);

                return $penugasan;
            });

        return view('auditee.rka.index', compact('auditee', 'penugasan'));
    }

    public function show(string $penugasanId): View
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);
        $progress = $this->getAuditProgress($penugasan);

        abort_unless($progress['selesai'], 403, 'RKA belum tersedia karena penilaian auditor belum lengkap.');

        $standarMutu = $this->getStandarMutu($penugasan);
        $jawabanAudit = JawabanAudit::whereIn('upt_item_sub_standar_id', $progress['item_ids'])
            ->get()
            ->unique('upt_item_sub_standar_id');

        $ringkasanStandar = $standarMutu->map(function (UptStandarMutu $standar) {
            $temuan = $standar->subStandarUpt
                ->flatMap(fn ($subStandar) => $subStandar->items)
                ->filter(fn ($item) => $item->jawaban_audit && (int) $item->jawaban_audit->jawaban === 0)
                ->map(fn ($item) => [
                    'nama_item' => $item->nama_item,
                    'catatan' => $item->jawaban_audit->catatan ?: '-',
                    'kategori' => $item->jawaban_audit->kategori_temuan ?: '-',
                ])
                ->values();

            return [
                'nama_standar' => $standar->standar_mutu?->nama_standar_mutu ?? '-',
                'temuan' => $temuan,
            ];
        });

        $ringkasan = [
            'total_item' => $progress['total_item'],
            'sesuai' => $jawabanAudit->filter(fn ($jawaban) => (int) $jawaban->jawaban === 1)->count(),
            'temuan' => $jawabanAudit->filter(fn ($jawaban) => (int) $jawaban->jawaban === 0)->count(),
            'kts' => $jawabanAudit->where('kategori_temuan', 'KTS')->count(),
            'ob' => $jawabanAudit->where('kategori_temuan', 'OB')->count(),
        ];

        return view('auditee.rka.show', compact('penugasan', 'ringkasanStandar', 'ringkasan'));
    }

    public function export(string $penugasanId): Response
    {
        $penugasan = $this->getPenugasanAuditee($penugasanId);
        $progress = $this->getAuditProgress($penugasan);

        abort_unless($progress['selesai'], 403, 'RKA belum tersedia karena penilaian auditor belum lengkap.');

        $standarMutu = $this->getStandarMutu($penugasan, true);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'RKA-' . Str::slug($upt->nama_upt) . '-' . $periode->tahun . '.pdf';

        return Pdf::loadView('auditor.export.pdf.rka', compact('standarMutu', 'upt', 'periode', 'penugasan'))
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

        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2'])
            ->where('penugasan_id', $penugasanId)
            ->where('upt_id', $auditee->upt_id)
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

    private function getStandarMutu(Penugasan $penugasan, bool $hanyaTemuan = false): EloquentCollection
    {
        return UptStandarMutu::with([
            'standar_mutu',
            'subStandarUpt.items.jawaban_audit' => function ($query) use ($hanyaTemuan) {
                if ($hanyaTemuan) {
                    $query->where('jawaban', 0);
                }
            },
        ])
            ->where('upt_id', $penugasan->upt_id)
            ->where('periode_id', $penugasan->periode_id)
            ->get()
            ->sortBy(fn ($standar) => $standar->standar_mutu?->urutan ?? 0)
            ->values();
    }
}
