<?php

namespace App\Http\Controllers\auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\UptStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
        public function index()
    {
        $user = Auth::user();
        $auditor = Auditor::where('user_id', $user->id)->firstOrFail();
        $periode = Periode::where('status', '1')->first();
        $penugasan = Penugasan::where('periode_id', $periode?->id)
            ->where('status_penugasan', 'aktif')
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->with('upt')
            ->get();

        return view('auditor.monitoring.index', compact('penugasan'));
    }
    public function detail($id)
{
    // 1. Ambil periode aktif saat ini agar kita tahu mana yang "periode sekarang"
    $periodeSekarang = Periode::where('status', '1')->first();
    $upt = UPT::findOrFail($id);

    // 2. Tarik data standar mutu milik UPT ini
    $monitoringData = UptStandarMutu::with([
            'standar_mutu', 
            'subStandarUpt.items.jawaban_audit'
        ])
        ->where('upt_id', $id)
        // Jalankan filter: Hanya ambil periode SEBELUM periode aktif sekarang (Temuan Masa Lalu)
        ->where('periode_id', '!=', $periodeSekarang?->id) 
        ->whereHas('subStandarUpt.items.jawaban_audit', function ($query) {
            // Filter: Hanya ambil yang jawabannya = 0 (Tidak Terpenuhi / Temuan)
            $query->where('jawaban', 0); 
        })
        ->get()
        // 3. Kelompokkan data di tingkat PHP berdasarkan periode_id agar mudah di-looping
        ->groupBy('periode_id');

    // Ambil info nama-nama periode untuk mempermudah pemanggilan title di view
    $listPeriode = Periode::all()->keyBy('id');

    return view('auditor.monitoring.detail', compact('monitoringData', 'listPeriode', 'id', 'upt'));
}
}
