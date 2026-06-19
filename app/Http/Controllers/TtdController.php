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

        $penugasan = Penugasan::with(['periode','auditor1','auditor2','upt'])
            ->where('penugasan_id', $uuid)
            ->first();
        $kepala_p4mp = User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->first()
            ?: User::where('role', 'kepala_p4mp')->first();

        if ($prefix == 'rka_ketua') {
            // $redaful = RegistrasiDataFakultas::where('meta_key', 'dekan')->first();
            $nama = $penugasan->auditor1->nama_lengkap;
            // $registrasi_code = substr($decode, 19);
            $judul = "Ringkasan Kondisi Audit";
            $jabatan = "Ketua Auditor";
        } elseif ($prefix == 'rka_anggota') {
            // $redaful = RegistrasiDataFakultas::where('meta_key', 'ketua')->first();
            $nama = $penugasan->auditor2->nama_lengkap;
            // $registrasi_code = substr($decode, 19);
            $judul = "Ringkasan Kondisi Audit";
            $jabatan = "Anggota Auditor";
        } elseif ($prefix == 'rka_kepala') {
            // $redaful = RegistrasiDataFakultas::where('meta_key', 'dekan')->first();
            $nama = $kepala_p4mp?->name ?? 'Kepala P4MP';
            // $registrasi_code = substr($decode, 19);
            $judul = "Ringkasan Kondisi Audit";
            $jabatan = "Kepala P4MP";
        } elseif ($prefix == 'surat_penelitian_wirausaha') {
            // $redaful = RegistrasiDataFakultas::where('meta_key', 'dekan')->first();
            // $nama = $redaful->meta_value;
            // $registrasi_code = substr($decode, 19);
            $judul = "Ringkasan Kondisi Audit";
            // $mv = 'Dekan';
        } else {
        }

        $tahun = $penugasan->periode->tahun;
        return view('ttd.index', compact(
            'jabatan', 
            'nama', 
            'judul',
            'tahun',
            'penugasan'
            // 'mv'
        ));
    }
}
