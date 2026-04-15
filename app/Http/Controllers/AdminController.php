<?php

namespace App\Http\Controllers;

use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $auditor = Auditor::count();
        $upt = UPT::count();
        $periode = Periode::count();
        $periode_now = Periode::where('status', '1')->first();
        
        $currentStep = 1;

    if ($periode_now) {
        $totalUPT = UPT::count();
        $penugasanAktif = Penugasan::where('periode_id', $periode_now->id)
                                   ->where('status_penugasan', 'aktif')
                                   ->count();

        if ($penugasanAktif >= ($totalUPT * 2)) {
            $currentStep = 3; 
        }
    }
        return view('admin.dashboard', compact(
            'auditor', 
            'upt', 
            'periode', 
            'periode_now',
            'currentStep'
        ));
    }
}
