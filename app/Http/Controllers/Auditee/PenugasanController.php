<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
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
        $id_user = Auth::id();

        $auditee_login = Auditee::where('user_id', $id_user)->first();

        if (!$auditee_login) {
            return redirect()->back()->with('error', 'Data auditee tidak ditemukan.');
        }

        $upt_id = $auditee_login->upt_id;

        $periode_now = Periode::where('status', '1')->first();

        if (!$periode_now) {
            return redirect()->back()->with('error', 'Periode aktif tidak ditemukan.');
        }

        $periode_id = $periode_now->id;

        // Penugasan berdasarkan UPT auditee login
        $penugasanProdi = Penugasan::where('periode_id', $periode_id)
            ->where('upt_id', $upt_id)
            ->where('status_penugasan', 'aktif')
            ->with([
                'upt',
                'auditor1',
                'auditor2',
                'auditee',
                'pengajuan_jadwal_audit'
            ])
            ->get();

        $penugasan = Penugasan::where('periode_id', $periode_id)
            ->where('upt_id', $upt_id)
            ->get();

        $auditor = Auditor::where('status_aktif', '1')->get();

        $upts = Upt::withCount(['penugasan' => function ($query) use ($periode_id) {
            $query->where('periode_id', $periode_id);
        }])
            ->where('upt_id', $upt_id)
            ->get();

        $penugasan_sekarang = Penugasan::where('periode_id', $periode_id)
            ->where('upt_id', $upt_id)
            ->get();

        return view('auditee.penugasan', compact(
            'penugasan',
            'penugasanProdi',
            'penugasan_sekarang',
            'periode_id',
            'auditor',
            'upts',
            'auditee_login',
            'upt_id'
        ));
    }

    public function ajukan(Request $request)
    {
        $request->validate([
            'penugasan_id' => 'required',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'alasan' => 'required|string',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::where('penugasan_id', $request->penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        $cekPengajuan = PengajuanJadwalAudit::where('penugasan_id', $penugasan->penugasan_id)->first();

        if ($cekPengajuan) {
            return back()->with('error', 'Pengajuan jadwal untuk penugasan ini sudah ada.');
        }

        $pengajuan_jadwal = new PengajuanJadwalAudit();
        $pengajuan_jadwal->id = Str::uuid();
        $pengajuan_jadwal->penugasan_id = $penugasan->penugasan_id;
        $pengajuan_jadwal->tanggal_audit = $request->tanggal;
        $pengajuan_jadwal->jam = $request->jam;
        $pengajuan_jadwal->id_pengaju = $auditee->auditee_id;

        // Karena pengaju adalah auditee / UPT
        $pengajuan_jadwal->upt = 1;
        $pengajuan_jadwal->ketua_auditor = 0;
        $pengajuan_jadwal->anggota_auditor = 0;

        $pengajuan_jadwal->alasan = $request->alasan;
        $pengajuan_jadwal->save();

        return back()->with('success', 'Pengajuan perubahan jadwal berhasil dikirim.');
    }

    public function setuju(Request $request)
    {
        $request->validate([
            'penugasan_id' => 'required',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::where('penugasan_id', $request->penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        $pengajuan_jadwal = PengajuanJadwalAudit::where('penugasan_id', $penugasan->penugasan_id)->firstOrFail();

        // Auditee hanya mengisi konfirmasi UPT
        $pengajuan_jadwal->upt = 1;
        $pengajuan_jadwal->save();

        // Jika semua pihak sudah setuju, update jadwal penugasan
        if (
            $pengajuan_jadwal->upt == 1 &&
            $pengajuan_jadwal->ketua_auditor == 1 &&
            $pengajuan_jadwal->anggota_auditor == 1
        ) {
            $penugasan->tanggal_audit = $pengajuan_jadwal->tanggal_audit;
            $penugasan->jam = $pengajuan_jadwal->jam;
            $penugasan->save();
        }

        return back()->with('success', 'Pengajuan jadwal berhasil disetujui.');
    }

    public function tolak(Request $request)
    {
        $request->validate([
            'penugasan_id' => 'required',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::where('penugasan_id', $request->penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        $pengajuan_jadwal = PengajuanJadwalAudit::where('penugasan_id', $penugasan->penugasan_id)->firstOrFail();

        $pengajuan_jadwal->delete();

        return back()->with('success', 'Pengajuan jadwal berhasil ditolak.');
    }
}
