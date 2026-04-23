<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditorController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $auditor = Auditor::where('user_id', $userId)->first();
        $periode = Periode::where('status', '1')->first();
        $is_selected = Penugasan::where('periode_id', $periode->id)
            ->where('status_penugasan', 'aktif')
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->get();
        $jumlah_upt = $is_selected->count();
        return view('auditor.dashboard', compact(
            'auditor',
            'is_selected',
            'jumlah_upt'
        ));
    }
}
