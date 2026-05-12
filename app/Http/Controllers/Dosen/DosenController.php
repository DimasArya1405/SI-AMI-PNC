<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Dosen;
use App\Models\Periode;
use App\Models\Prodi;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $dosen = Dosen::where('user_id', $userId)->first();

        $periodeNow = Periode::where('status', '1')->first();

        $prodi = null;
        $upt = null;
        $auditee = null;

        if ($dosen) {
            $prodi = Prodi::where('prodi_id', $dosen->prodi_id)->first();

            if ($prodi) {
                $upt = UPT::where('nama_upt', $prodi->nama_prodi)->first();

                if ($upt) {
                    $auditee = Auditee::with('upt')
                        ->where('upt_id', $upt->upt_id)
                        ->first();
                }
            }
        }

        return view('dosen.dashboard', [
            'dosen' => $dosen,
            'prodi' => $prodi,
            'upt' => $upt,
            'auditee' => $auditee,
            'periode_now' => $periodeNow,
            'nama_unit' => $auditee?->upt?->nama_upt ?? '-',
        ]);
    }
}
