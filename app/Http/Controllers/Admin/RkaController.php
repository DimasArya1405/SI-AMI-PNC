<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\UptStandarMutu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RkaController extends Controller
{
    public function index()
    {
        $periode = Periode::where('status', '1')->first();

        // Jika tidak ada periode yang aktif, set data UPT menjadi collection kosong
        if (!$periode) {
            $uptProdi = collect();
            $uptBagian = collect();
            return view('admin.ami.rka', compact('uptProdi', 'uptBagian'))->with('no_periode', true);
        }

        // Eager load penugasan yang sesuai dengan periode aktif
        $uptProdi = UPT::where('kategori_upt', 'Prodi')->with(['penugasan' => function ($query) use ($periode) {
            $query->where('periode_id', $periode->id);
        }])->get();

        $uptBagian = UPT::where('kategori_upt', 'Unit/Bagian')->with(['penugasan' => function ($query) use ($periode) {
            $query->where('periode_id', $periode->id);
        }])->get();

        return view('admin.ami.rka', compact('uptProdi', 'uptBagian'));
    }
    public function exportRka($id)
    {
        $periode = Periode::where('status', '1')->first();
        $periode_id = $periode->id;

        // Ambil data penugasan untuk cek status
        $penugasan = Penugasan::where('upt_id', $id)
            ->where('periode_id', $periode_id)
            ->with('auditor1', 'auditor2')
            ->firstOrFail();

        // Proteksi: Jika status_penugasan BUKAN 'selesai', gagalkan proses export
        if ($penugasan->status_penugasan !== 'selesai') {
            return redirect()->back()->with('error', 'Audit belum selesai. Dokumen RKA belum dapat diunduh.');
        }

        $upt = UPT::findOrFail($id);

        // Ambil Standar Mutu yang memiliki relasi ke bawah hingga ke jawaban = 0
        $standarMutu = UptStandarMutu::with(['standar_mutu', 'subStandarUpt.items.jawaban_audit' => function ($query) {
            $query->where('jawaban', 0);
        }])
            ->where('upt_id', $id)
            ->where('periode_id', $periode_id)
            ->get();

        $pdf = Pdf::loadView('admin.export.pdf.rka', compact('standarMutu', 'upt', 'periode', 'penugasan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('RKA.pdf');
    }
}
