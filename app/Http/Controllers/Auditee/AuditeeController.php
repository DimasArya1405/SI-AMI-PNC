<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Penugasan;
use App\Models\Periode;
use Illuminate\Http\Request;
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
            $penugasan = Penugasan::with(['auditor', 'upt', 'periode'])
                ->where('auditee_id', $auditee->auditee_id)
                ->where('periode_id', $periodeNow->id)
                ->get();
        }

        $penugasanTerbaru = $penugasan->sortByDesc('tanggal_audit')->first();
        $statusPenugasan = $penugasanTerbaru?->status_penugasan;

        $totalPenugasan = $penugasan->count();
        $penugasanAktif = $penugasan->where('status_penugasan', 'aktif')->count();
        $penugasanSelesai = $penugasan->where('status_penugasan', 'selesai')->count();
        $isAssigned = $penugasan->isNotEmpty();

        $namaAuditor = $penugasanTerbaru?->auditor?->nama_lengkap ?? '-';
        $tanggalAudit = $penugasanTerbaru?->tanggal_audit
            ? \Carbon\Carbon::parse($penugasanTerbaru->tanggal_audit)->translatedFormat('d F Y')
            : '-';
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
            'nama_auditor' => $namaAuditor,
            'tanggal_audit' => $tanggalAudit,
            'lokasi_audit' => $lokasiAudit,
            'currentStep' => $currentStep,
            'currentStageLabel' => $currentStageLabel,
        ]);
    }
}
