<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Penugasan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditeeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $auditee = Auditee::with('upt')
            ->where('user_id', $userId)
            ->first();

        $periodeNow = Periode::where('status', '1')->first();

        $penugasan = collect();

        if ($auditee && $periodeNow) {
            $penugasan = Penugasan::with([
                'auditor1',
                'auditor2',
                'upt',
                'periode'
            ])
                ->where('upt_id', $auditee->upt_id)
                ->where('periode_id', $periodeNow->id)
                ->get();
        }

        $penugasanTerbaru = $penugasan->sortByDesc('tanggal_audit')->first();
        $statusPenugasan = $penugasanTerbaru?->status_penugasan;

        $totalPenugasan = $penugasan->count();
        $penugasanAktif = $penugasan->where('status_penugasan', 'aktif')->count();
        $penugasanSelesai = $penugasan->where('status_penugasan', 'selesai')->count();
        $isAssigned = $penugasan->isNotEmpty();

        $namaKetuaAuditor = $penugasanTerbaru?->auditor1?->nama_lengkap ?? '-';
        $namaAnggotaAuditor = $penugasanTerbaru?->auditor2?->nama_lengkap ?? '-';

        $tanggalAudit = $penugasanTerbaru?->tanggal_audit
            ? Carbon::parse($penugasanTerbaru->tanggal_audit)->translatedFormat('d F Y')
            : '-';

        $jamAudit = $penugasanTerbaru?->jam ?? '-';

        $lokasiAudit = $penugasanTerbaru?->lokasi ?? '-';

        $currentStep = match ($statusPenugasan) {
            'pending' => 2,
            'aktif' => 3,
            'selesai' => 5,
            default => 1,
        };

        $currentStageLabel = match ($statusPenugasan) {
            'pending' => 'Menunggu Pelaksanaan Audit',
            'aktif' => 'Pelaksanaan Audit',
            'selesai' => 'Audit Selesai',
            default => 'Menunggu Penugasan',
        };

        return view('auditee.dashboard', [
            'auditee' => $auditee,
            'periode_now' => $periodeNow,
            'is_assigned' => $isAssigned,
            'nama_unit' => $auditee?->upt?->nama_upt ?? '-',
            'total_penugasan' => $totalPenugasan,
            'penugasan_aktif' => $penugasanAktif,
            'penugasan_selesai' => $penugasanSelesai,

            'nama_ketua_auditor' => $namaKetuaAuditor,
            'nama_anggota_auditor' => $namaAnggotaAuditor,

            'tanggal_audit' => $tanggalAudit,
            'jam_audit' => $jamAudit,
            'lokasi_audit' => $lokasiAudit,
            'currentStep' => $currentStep,
            'currentStageLabel' => $currentStageLabel,
        ]);
    }
}
