<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\Ami\PenugasanDataTable;
use App\DataTables\Admin\PeriodeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\Prodi;
use App\Models\UPT;
use Barryvdh\DomPDF\Facade\Pdf;
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
                // Load kedua auditor sekaligus
                $query->where('periode_id', $id)->with(['auditor1', 'auditor2']);
            }])
            ->get();

        $uptBagian = UPT::where('kategori_upt', 'Unit/Bagian')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with(['auditor1', 'auditor2']);
            }])
            ->get();
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
        // 1. Validasi awal: Auditor tidak boleh orang yang sama
        if ($request->auditor_1 == $request->auditor_2) {
            return redirect()->back()->with('error', 'Auditor 1 dan Auditor 2 Tidak Boleh Sama!');
        }

        $upt = UPT::find($request->upt_id);
        $auditor_1 = Auditor::find($request->auditor_1);
        $auditor_2 = Auditor::find($request->auditor_2);

        // 2. Validasi Independensi (Jika UPT adalah Prodi)
        if ($upt->kategori_upt == 'Prodi') {
            // Ambil data prodi auditor (asumsi relasi 'prodi' sudah ada di model Auditor)
            // Jika tidak ada relasi, gunakan cara manual: Prodi::find($auditor_1->prodi_id)
            $nama_prodi_auditor_1 = $auditor_1->prodi->nama_prodi ?? '';
            $nama_prodi_auditor_2 = $auditor_2->prodi->nama_prodi ?? '';

            if (Str::upper($nama_prodi_auditor_1) == Str::upper($upt->nama_upt)) {
                return redirect()->back()->with('error', 'Ketua Auditor tidak boleh berasal dari Prodi yang diaudit!');
            }

            if (Str::upper($nama_prodi_auditor_2) == Str::upper($upt->nama_upt)) {
                return redirect()->back()->with('error', 'Anggota Auditor tidak boleh berasal dari Prodi yang diaudit!');
            }
        }

        // 3. Update Data Penugasan (Cukup satu kali find/update)
        $penugasan = Penugasan::where('upt_id', $request->upt_id)
            ->where('periode_id', $request->periode_id)
            ->first();

        if (!$penugasan) {
            return redirect()->back()->with('error', 'Data penugasan tidak ditemukan!');
        }

        $penugasan->auditor_id_1 = $request->auditor_1; // Ketua
        $penugasan->auditor_id_2 = $request->auditor_2; // Anggota
        $penugasan->tanggal_audit = date('Y-m-d', strtotime($request->tanggal));
        $penugasan->jam = $request->jam; // Pastikan kolom 'jam' ada di DB

        // Jika ada kolom lain seperti status, pastikan tetap terjaga
        $penugasan->save();

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
        $penugasan->auditor_id_1 = $request->auditor_1;
        $penugasan->auditor_id_2 = $request->auditor_2;
        $penugasan->tanggal_audit = date('Y-m-d', strtotime($request->tanggal));
        $penugasan->jam = $request->jam;
        $penugasan->status_penugasan = 'pending';
        $penugasan->save();

        return redirect()->back()->with('success', 'Penugasan Berhasil Ditambahkan!');
    }
    public function aktifkan($id)
    {
        // 1. Ambil semua data penugasan pada periode tersebut
        $penugasan = Penugasan::where('periode_id', $id);

        // 2. Hitung jumlah UPT yang seharusnya memiliki penugasan
        // Jika semua UPT (Prodi & Bagian) wajib diaudit, gunakan UPT::count()
        $jumlahUpt = UPT::count();

        // Sekarang syaratnya adalah 1 UPT = 1 baris penugasan
        $syaratJumlahPenugasan = $jumlahUpt;

        // 3. Validasi: Apakah jumlah baris penugasan sudah sama dengan jumlah UPT?
        if ($penugasan->count() < $syaratJumlahPenugasan) {
            return redirect()->back()->with('error', 'Gagal aktifkan! Masih ada UPT yang belum memiliki data penugasan.');
        }

        // 4. Update status menjadi aktif
        $penugasan->update(['status_penugasan' => 'aktif']);

        return redirect()->back()->with('success', 'Semua penugasan berhasil diaktifkan. Auditor sekarang dapat memulai proses audit.');
    }
    public function exportPdf($id)
    {
        // 1. Ambil data berdasarkan ID periode yang dikirim
        $uptProdi = UPT::where('kategori_upt', 'Prodi')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with('auditor');
            }])
            ->get();

        $uptBagian = UPT::where('kategori_upt', 'Unit/Bagian')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with('auditor');
            }])
            ->get();
        $periode = Periode::where('id', $id)->first();
        $tahun = $periode->tahun;

        // 2. Load View PDF (Gunakan file blade khusus PDF yang sudah kita buat sebelumnya)
        $pdf = Pdf::loadView('admin.export.pdf.penugasan', compact('uptProdi', 'uptBagian', 'id', 'tahun'))
            ->setPaper('a4', 'portrait');

        // 3. Download atau Stream
        return $pdf->stream('Jadwal-AMI-PNC.pdf');
    }
}
