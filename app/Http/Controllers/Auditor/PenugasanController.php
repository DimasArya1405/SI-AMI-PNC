<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\PengajuanJadwalAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PenugasanController extends Controller
{
    public function index()
    {
        $id_user = Auth::user()->id;
        $auditor_login = Auditor::where('user_id', $id_user)->first();
        $auditor_id = $auditor_login->auditor_id;
        $periode_now = Periode::where('status', '1')->first();
        $periode_id = $periode_now->id;
        // Filter UPT Prodi yang ditugaskan ke auditor login
        $penugasanProdi = Penugasan::where('periode_id', $periode_id)
            ->where(function ($query) use ($auditor_id) {
                $query->where('auditor_id_1', $auditor_id)
                    ->orWhere('auditor_id_2', $auditor_id);
            })
            ->with(['upt', 'auditor1', 'auditor2','pengajuan_jadwal_audit'])
            ->get();        


        $penugasan = Penugasan::where('periode_id', $periode_id)->get();
        $auditor = Auditor::where('status_aktif', '1')->get();
        // Ambil semua UPT dan hitung jumlah penugasan mereka khusus untuk periode ini
        $upts = Upt::withCount(['penugasan' => function ($query) use ($periode_id) {
            $query->where('periode_id', $periode_id);
        }])->get();
        $penugasan_sekarang = Penugasan::where('periode_id', $periode_id)->get();
        return view('auditor.penugasan.index', compact(
            'penugasan',
            'penugasanProdi',
            'penugasan_sekarang',
            'periode_id',
            'auditor',
            'upts',
            'auditor_id'
        ));
    }
    public function ajukan(Request $request)
    {
        $penugasan = Penugasan::find($request->penugasan_id);

        
        $pengajuan_jadwal = new PengajuanJadwalAudit;
        $pengajuan_jadwal->id = Str::uuid();
        $pengajuan_jadwal->penugasan_id = $request->penugasan_id;
        $pengajuan_jadwal->tanggal_audit = $request->tanggal;
        $pengajuan_jadwal->jam = $request->jam;
        $pengajuan_jadwal->id_pengaju = $request->auditor_id;
        if($penugasan->auditor_id_1 == $request->auditor_id){
            $pengajuan_jadwal->ketua_auditor = 1;
        }else{
            $pengajuan_jadwal->anggota_auditor = 1;
        }
        $pengajuan_jadwal->alasan = $request->alasan;
        $pengajuan_jadwal->save();
        
        return back()->with('success', 'Penugasan berhasil disetujui');
    }

    public function setuju(Request $request)
    {
        $penugasan = Penugasan::find($request->penugasan_id);
        $pengajuan_jadwal = PengajuanJadwalAudit::where('penugasan_id', $request->penugasan_id)->first();
        if($penugasan->auditor_id_1 == $request->auditor_id_detail){
            $pengajuan_jadwal->ketua_auditor = 1;
            $pengajuan_jadwal->save();
        }else{
            $pengajuan_jadwal->anggota_auditor = 1;
            $pengajuan_jadwal->save();
        }
        return back()->with('success', 'Penugasan berhasil disetujui');
    }

    public function tolak(Request $request)
    {
        $pengajuan_jadwal = PengajuanJadwalAudit::where('penugasan_id', $request->penugasan_id)->first();
        $pengajuan_jadwal->delete();
        return back()->with('success', 'Penugasan berhasil ditolak');
    }
}
