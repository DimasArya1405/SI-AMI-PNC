<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Http\Request;

class TtdController extends Controller
{
    public function ttdShow(Request $request)
    {
        $ttdcode = $request->query('ttdcode');
        $decode = base64_decode($ttdcode);
        list($prefix, $uuid) = explode('||', $decode);

        $penugasan = Penugasan::with(['periode','auditor1','auditor2','upt','rka','tindakanKoreksi'])
            ->where('penugasan_id', $uuid)
            ->first();
        $kepala_p4mp = User::where('role', 'kepala_p4mp')->first();

        if ($prefix == 'rka_ketua') {
            $nama = $penugasan->auditor1->nama_lengkap;
            $judul = "Ringkasan Kondisi Audit";
            $jabatan = "Ketua Auditor";
            $tgl = $penugasan->rka->finalized_at;
        } elseif ($prefix == 'rka_anggota') {
            $nama = $penugasan->auditor2->nama_lengkap;
            $judul = "Ringkasan Kondisi Audit";
            $jabatan = "Anggota Auditor";
            $tgl = $penugasan->rka->finalized_at;
        } elseif ($prefix == 'rka_kepala') {
            $nama = $kepala_p4mp->name;
            $judul = "Ringkasan Kondisi Audit";
            $jabatan = "Kepala P4MP";
            $tgl = $penugasan->rka->acc_p4mp_at;
        } elseif ($prefix == 'tk_kepala') {
            $nama = $kepala_p4mp->name;
            $judul = "Tindakan Koreksi";
            $jabatan = "Kepala P4MP";
            $tgl = $penugasan->tindakanKoreksi->p4mp_verified_at;
        } elseif ($prefik = 'tk_ketua') {
            $nama = $penugasan->auditor1->nama_lengkap;
            $judul = "Tindakan Koreksi";
            $jabatan = "Ketua Auditor";
            $tgl = $penugasan->tindakanKoreksi->first()?->verified_at;
        } elseif ($prefik = 'tk_anggota') {
            $nama = $penugasan->auditor2->nama_lengkap;
            $judul = "Tindakan Koreksi";
            $jabatan = "Anggota Auditor";
            $tgl = $penugasan->tindakanKoreksi->first()?->verified_at;
        }

        $tahun = $penugasan->periode->tahun;
        return view('ttd.index', compact(
            'jabatan', 
            'nama', 
            'judul',
            'tahun',
            'penugasan',
            'tgl'
        ));
    }
}
