<?php

namespace App\Http\Controllers\KepalaP4mp;

use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use App\Models\TindakanKoreksi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPenugasan = Penugasan::count();
        $menungguVerifikasi = TindakanKoreksi::where('status', 'selesai')
            ->where(function ($query) {
                $query->whereNull('p4mp_status')
                    ->orWhere('p4mp_status', 'menunggu_verifikasi');
            })
            ->count();
        $terverifikasi = TindakanKoreksi::where('p4mp_status', 'terverifikasi')->count();

        return view('kepala-p4mp.dashboard', compact('totalPenugasan', 'menungguVerifikasi', 'terverifikasi'));
    }
}
