<?php

namespace App\Http\Controllers;

use App\Models\Auditor;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\TindakanKoreksi;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;

class AdminController extends Controller
{
    public function index()
    {
        $auditor = Auditor::count();
        $upt = UPT::count();
        $periode = Periode::count();
        $periode_now = Periode::where('status', '1')->first();

        $currentStep = 1;
        $currentStageLabel = 'Belum ada periode aktif';
        $progressSummary = [
            'total_penugasan' => 0,
            'jadwal_terisi' => 0,
            'item_dinilai' => 0,
            'rka_final' => 0,
            'tindakan_koreksi' => 0,
            'tindakan_koreksi_terverifikasi' => 0,
        ];

        if ($periode_now) {
            $penugasanPeriode = Penugasan::with(['rka', 'verifikasiTindakanKoreksi'])
                ->where('periode_id', $periode_now->id)
                ->get();

            $penugasanIds = $penugasanPeriode->pluck('penugasan_id');
            $itemIds = UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($periode_now) {
                $query->where('periode_id', $periode_now->id);
            })->pluck('upt_item_sub_standar_id');

            $jawabanAuditCount = $itemIds->isEmpty()
                ? 0
                : JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)->count();

            $tindakanKoreksi = $penugasanIds->isEmpty()
                ? collect()
                : TindakanKoreksi::whereIn('penugasan_id', $penugasanIds)->get();

            $progressSummary = [
                'total_penugasan' => $penugasanPeriode->count(),
                'jadwal_terisi' => $penugasanPeriode
                    ->filter(fn ($item) => filled($item->tanggal_audit) && filled($item->jam))
                    ->count(),
                'item_dinilai' => $jawabanAuditCount,
                'rka_final' => $penugasanPeriode
                    ->filter(fn ($item) => $item->rka && ($item->rka->status === 'final' || filled($item->rka->finalized_at)))
                    ->count(),
                'tindakan_koreksi' => $tindakanKoreksi->count(),
                'tindakan_koreksi_terverifikasi' => $tindakanKoreksi
                    ->filter(fn ($item) => $item->p4mp_status === 'terverifikasi' || filled($item->p4mp_verified_at))
                    ->count(),
            ];

            $semuaPenugasanSelesai = $penugasanPeriode->isNotEmpty()
                && $penugasanPeriode->every(fn ($item) => $item->status_penugasan === 'selesai');

            $semuaTkTerverifikasi = $tindakanKoreksi->isNotEmpty()
                && $tindakanKoreksi->every(fn ($item) => $item->p4mp_status === 'terverifikasi' || filled($item->p4mp_verified_at));

            if ($semuaPenugasanSelesai || $semuaTkTerverifikasi) {
                $currentStep = 5;
                $currentStageLabel = 'Audit Selesai';
            } elseif ($progressSummary['rka_final'] > 0 || $progressSummary['tindakan_koreksi'] > 0) {
                $currentStep = 4;
                $currentStageLabel = 'Monitoring dan Tindakan Koreksi';
            } elseif ($jawabanAuditCount > 0 || $penugasanPeriode->where('status_penugasan', 'aktif')->isNotEmpty()) {
                $currentStep = 3;
                $currentStageLabel = 'Pelaksanaan Audit';
            } elseif ($progressSummary['jadwal_terisi'] > 0 || $penugasanPeriode->isNotEmpty()) {
                $currentStep = 2;
                $currentStageLabel = 'Penjadwalan Audit';
            } else {
                $currentStep = 1;
                $currentStageLabel = 'Pembentukan Tim';
            }
        }

        return view('admin.dashboard', compact(
            'auditor',
            'upt',
            'periode',
            'periode_now',
            'currentStep',
            'currentStageLabel',
            'progressSummary'
        ));
    }
}
