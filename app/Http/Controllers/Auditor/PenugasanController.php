<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->with(['upt', 'auditor1', 'auditor2'])
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
}
