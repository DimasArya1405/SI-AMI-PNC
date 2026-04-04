<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\Ami\PenugasanDataTable;
use App\DataTables\Admin\PeriodeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Prodi;
use App\Models\UPT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PenugasanController extends Controller
{
    public function index(PeriodeDataTable $dataTable)
    {
        return $dataTable->render('admin.ami.penugasan');
    }
    public function detail($id, Request $request)
    {
        $periode_id = $id;
        $uptProdi = UPT::where('kategori_upt', 'Prodi')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with('auditor');
            }])
            ->get();
        $uptBagian = UPT::where('kategori_upt', 'Unit/Bagian')->get();
        $penugasan = Penugasan::where('periode_id', $id)->get();
        $auditor = Auditor::where('status_aktif', '1')->get();
        // Ambil semua UPT dan hitung jumlah penugasan mereka khusus untuk periode ini
        $upts = Upt::withCount(['penugasan' => function ($query) use ($periode_id) {
            $query->where('periode_id', $periode_id);
        }])->get();
        $penugasan_sekarang = Penugasan::where('periode_id', $periode_id)->get();
        return view('admin.ami.penugasan_detail', compact('penugasan', 'uptProdi', 'penugasan_sekarang', 'uptBagian', 'periode_id', 'auditor', 'upts'));
    }
    public function edit(Request $request)
    {
        if ($request->auditor_1 == $request->auditor_2) {
            return redirect()->back()->with('error', 'Auditor 1 dan Auditor 2 Tidak Boleh Sama!');
        }
        $upt = UPT::find($request->upt_id);
        $auditor_1 = Auditor::find($request->auditor_1);
        $auditor_2 = Auditor::find($request->auditor_2);
        $prodi_1 = Prodi::where('prodi_id', $auditor_1->prodi_id)->first();
        $prodi_2 = Prodi::where('prodi_id', $auditor_2->prodi_id)->first();
        if ($upt->kategori_upt == 'Prodi') {
            if (Str::upper($prodi_1->nama_prodi) == Str::upper($upt->nama_upt)) {
                return redirect()->back()->with('error', 'Auditor 1 dan UPT Tidak Boleh Sama!');
            } elseif (Str::upper($prodi_2->nama_prodi) == Str::upper($upt->nama_upt)) {
                return redirect()->back()->with('error', 'Auditor 2 dan UPT Tidak Boleh Sama!');
            }
        }
        $penugasan_1 = Penugasan::where('upt_id', $request->upt_id)->where('periode_id', $request->periode_id)->where('auditor_id', $request->auditor_1_old)->first();
        $penugasan_1->auditor_id = $request->auditor_1;
        $penugasan_1->tanggal_audit = date('Y-m-d', strtotime($request->tanggal));
        $penugasan_1->lokasi = $request->tempat;
        $penugasan_1->save();

        $penugasan_2 = Penugasan::where('upt_id', $request->upt_id)->where('periode_id', $request->periode_id)->where('auditor_id', $request->auditor_2_old)->first();
        $penugasan_2->auditor_id = $request->auditor_2;
        $penugasan_2->tanggal_audit = date('Y-m-d', strtotime($request->tanggal));
        $penugasan_2->lokasi = $request->tempat;
        $penugasan_2->save();

        return redirect()->back()->with('success', 'Penugasan berhasil diubah');
    }
    public function tambah(Request $request)
    {
        if ($request->auditor_1 == $request->auditor_2) {
            return redirect()->back()->with('error', 'Auditor 1 dan Auditor 2 Tidak Boleh Sama!');
        }
        $upt = UPT::find($request->upt_id);
        $auditor_1 = Auditor::find($request->auditor_1);
        $auditor_2 = Auditor::find($request->auditor_2);
        $prodi_1 = Prodi::where('prodi_id', $auditor_1->prodi_id)->first();
        $prodi_2 = Prodi::where('prodi_id', $auditor_2->prodi_id)->first();
        if ($upt->kategori_upt == 'Prodi') {
            if (Str::upper($prodi_1->nama_prodi) == Str::upper($upt->nama_upt)) {
                return redirect()->back()->with('error', 'Auditor 1 dan UPT Tidak Boleh Sama!');
            } elseif (Str::upper($prodi_2->nama_prodi) == Str::upper($upt->nama_upt)) {
                return redirect()->back()->with('error', 'Auditor 2 dan UPT Tidak Boleh Sama!');
            }
        }

        $penugasan = new Penugasan();
        $penugasan->periode_id = $request->periode_id;
        $penugasan->upt_id = $request->upt_id;
        $penugasan->auditor_id = $request->auditor_1;
        $penugasan->tanggal_audit = date('Y-m-d', strtotime($request->tanggal));
        $penugasan->lokasi = $request->tempat;
        $penugasan->status_penugasan = 'pending';
        $penugasan->save();

        $penugasan_2 = new Penugasan();
        $penugasan_2->periode_id = $request->periode_id;
        $penugasan_2->upt_id = $request->upt_id;
        $penugasan_2->auditor_id = $request->auditor_2;
        $penugasan_2->tanggal_audit = date('Y-m-d', strtotime($request->tanggal));
        $penugasan_2->lokasi = $request->tempat;
        $penugasan_2->status_penugasan = 'pending';
        $penugasan_2->save();

        return redirect()->back()->with('success', 'Penugasan Berhasil Ditambahkan!');
    }
    public function aktifkan($id)
    {
        $penugasan = Penugasan::where('periode_id', $id);
        $jumlahUpt = UPT::count();
        $syaratJumlahPenugasan = $jumlahUpt * 2;

        if ($penugasan->count() < $syaratJumlahPenugasan) {
            return redirect()->back()->with('error', 'Gagal aktifkan! Pastikan semua UPT sudah memiliki tepat 2 data penugasan (Auditor).');
        }

        $penugasan->update(['status_penugasan' => 'aktif']);

        return redirect()->back()->with('success', 'Semua penugasan berhasil diaktifkan.');
    }
}
