<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\Ami\PenugasanDataTable;
use App\DataTables\Admin\PeriodeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\User;
use App\Notifications\PenugasanAuditNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;

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
                $query->where('periode_id', $id)->with(['auditor1', 'auditor2', 'pengajuan_jadwal_audit']);
            }])
            ->get();

        $uptBagian = UPT::where('kategori_upt', 'Unit/Bagian')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with(['auditor1', 'auditor2', 'pengajuan_jadwal_audit']);
            }])
            ->get();
        $penugasan = Penugasan::where('periode_id', $id)->get();
        $auditor = Auditor::where('status_aktif', '1')->get();
        // Ambil semua UPT dan hitung jumlah penugasan mereka khusus untuk periode ini
        $upts = Upt::withCount(['penugasan' => function ($query) use ($periode_id) {
            $query->where('periode_id', $periode_id);
        }])->get();
        $penugasan_sekarang = Penugasan::where('periode_id', $periode_id)->get();
        $rekapAuditor = Auditor::where('status_aktif', 1)
            ->get()
            ->map(function ($auditor) use ($periode_id) {

                $penugasanKetua = Penugasan::with('upt')
                    ->where('periode_id', $periode_id)
                    ->where('auditor_id_1', $auditor->auditor_id)
                    ->get();

                $penugasanAnggota = Penugasan::with('upt')
                    ->where('periode_id', $periode_id)
                    ->where('auditor_id_2', $auditor->auditor_id)
                    ->get();

                $jumlahKetua = $penugasanKetua->count();
                $jumlahAnggota = $penugasanAnggota->count();

                // gabungkan semua UPT
                $daftarUpt = $penugasanKetua
                    ->merge($penugasanAnggota)
                    ->pluck('upt.nama_upt')
                    ->unique()
                    ->values();

                $auditor->jumlah_ketua = $jumlahKetua;
                $auditor->jumlah_anggota = $jumlahAnggota;
                $auditor->jumlah_upt = $daftarUpt->count();
                $auditor->daftar_upt = $daftarUpt;

                return $auditor;
            });
        return view('admin.ami.penugasan_detail', compact('penugasan', 'rekapAuditor', 'uptProdi', 'penugasan_sekarang', 'uptBagian', 'periode_id', 'auditor', 'upts'));
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
        $validator = Validator::make($request->all(), [
            'periode_id' => ['required'],
            'upt_id' => ['required'],
            'auditor_1' => ['required'],
            'auditor_2' => ['required'],
            'tanggal' => ['required', 'date'],
            'jam' => ['required'],
        ], [
            'auditor_1.required' => 'Ketua auditor wajib dipilih.',
            'auditor_2.required' => 'Anggota auditor wajib dipilih.',
            'tanggal.required' => 'Tanggal audit wajib diisi.',
            'tanggal.date' => 'Format tanggal audit tidak valid.',
            'jam.required' => 'Jam audit wajib diisi.',
            'periode_id.required' => 'Periode penugasan tidak ditemukan.',
            'upt_id.required' => 'UPT penugasan tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return $this->penugasanFailedResponse($request, $validator->errors()->first());
        }

        if ($request->auditor_1 == $request->auditor_2) {
            return $this->penugasanFailedResponse($request, 'Auditor 1 dan Auditor 2 Tidak Boleh Sama!');
        }

        $upt = UPT::find($request->upt_id);
        $auditor_1 = Auditor::with('prodi')->find($request->auditor_1);
        $auditor_2 = Auditor::with('prodi')->find($request->auditor_2);

        if (!$upt) {
            return $this->penugasanFailedResponse($request, 'Data UPT tidak ditemukan!');
        }

        if (!$auditor_1 || !$auditor_2) {
            return $this->penugasanFailedResponse($request, 'Data auditor tidak ditemukan!');
        }

        $penugasanSudahAda = Penugasan::where('upt_id', $request->upt_id)
            ->where('periode_id', $request->periode_id)
            ->exists();

        if ($penugasanSudahAda) {
            return $this->penugasanFailedResponse($request, 'UPT ini sudah memiliki data penugasan pada periode tersebut.');
        }

        if ($upt->kategori_upt == 'Prodi') {
            $namaProdiAuditor1 = $auditor_1->prodi?->nama_prodi ?? '';
            $namaProdiAuditor2 = $auditor_2->prodi?->nama_prodi ?? '';

            if (Str::upper($namaProdiAuditor1) == Str::upper($upt->nama_upt)) {
                return $this->penugasanFailedResponse($request, 'Ketua Auditor tidak boleh berasal dari Prodi yang diaudit!');
            } elseif (Str::upper($namaProdiAuditor2) == Str::upper($upt->nama_upt)) {
                return $this->penugasanFailedResponse($request, 'Anggota Auditor tidak boleh berasal dari Prodi yang diaudit!');
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

        $this->kirimNotifikasiPenugasanDibuat($penugasan);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penugasan Berhasil Ditambahkan!',
            ]);
        }

        return redirect()->back()->with('success', 'Penugasan Berhasil Ditambahkan!');
    }

    private function penugasanFailedResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return redirect()->back()->withInput()->with('error', $message);
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

        $penugasanAktif = Penugasan::where('periode_id', $id)
            ->with(['upt', 'auditor1.user', 'auditor2.user'])
            ->get();

        $penugasanAktif->each(fn($item) => $this->kirimNotifikasiAmiDibuka($item));
        $this->kirimNotifikasiKepalaP4mpPenugasanAktif($penugasanAktif, $id);

        return redirect()->back()->with('success', 'Semua penugasan berhasil diaktifkan. Auditor sekarang dapat memulai proses audit.');
    }
    public function exportPdf($id)
    {
        // 1. Ambil data berdasarkan ID periode yang dikirim
        $penugasan = Penugasan::with(['upt', 'auditor1', 'auditor2'])
            ->where('periode_id', $id)
            ->orderBy('tanggal_audit')
            ->orderBy('jam')
            ->get();

        $uptProdi = UPT::where('kategori_upt', 'Prodi')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with(['auditor1', 'auditor2']);
            }])
            ->get();

        $uptBagian = UPT::where('kategori_upt', 'Unit/Bagian')
            ->with(['penugasan' => function ($query) use ($id) {
                $query->where('periode_id', $id)->with(['auditor1', 'auditor2']);
            }])
            ->get();
        $periode = Periode::where('id', $id)->first();
        $tahun = $periode->tahun;
        $sudahDitandatangani = $penugasan->isNotEmpty()
            && $penugasan->every(fn (Penugasan $item) => $item->acc_kepala_p4mp === '1');
        $tanggalTtd = $penugasan->firstWhere('acc_kepala_p4mp', '1')
            ?->acc_kepala_p4mp_at
            ?->locale('id')
            ->translatedFormat('d F Y');
        $kepalaP4mp = User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->first()
            ?: User::where('role', 'kepala_p4mp')->first();
        $kepalaP4mpName = $kepalaP4mp?->name ?? 'Kepala P4MP';
        $penugasanQR = $sudahDitandatangani && $penugasan->first()
            ? $this->generateQrCode('penugasan_kepala||', $penugasan->first()->penugasan_id)
            : null;

        // 2. Load View PDF (Gunakan file blade khusus PDF yang sudah kita buat sebelumnya)
        $pdf = Pdf::loadView('admin.export.pdf.penugasan', compact(
            'uptProdi',
            'uptBagian',
            'id',
            'tahun',
            'kepalaP4mpName',
            'sudahDitandatangani',
            'tanggalTtd',
            'penugasanQR'
        ))
            ->setPaper('a4', 'portrait');

        // 3. Download atau Stream
        return $pdf->stream('Jadwal-AMI-PNC.pdf');
    }

    private function generateQrCode(string $prefix, string $registrasi): string
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);

        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }

    private function kirimNotifikasiPenugasanDibuat(Penugasan $penugasan): void
    {
        $penugasan->load(['upt', 'auditor1.user', 'auditor2.user']);

        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "Penugasan audit untuk {$namaUpt} telah dibuat. Silakan cek jadwal dan konfirmasi penugasan.";

        $this->notifikasiAuditor($penugasan, 'Penugasan AMI Dibuat', $pesan, route('auditor.penugasan'));
        $this->notifikasiAuditee($penugasan, 'Penugasan AMI Dibuat', $pesan, route('auditee.penugasan'));
    }

    private function kirimNotifikasiAmiDibuka(Penugasan $penugasan): void
    {
        $penugasan->load(['upt', 'auditor1.user', 'auditor2.user']);

        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesanAuditor = "Penugasan audit untuk {$namaUpt} telah aktif. Pelaksanaan AMI sudah dapat diakses.";
        $pesanAuditee = "Penugasan audit untuk {$namaUpt} telah aktif. Formulir AMI sudah dapat diakses.";

        $this->notifikasiAuditor($penugasan, 'AMI Sudah Dapat Diakses', $pesanAuditor, route('auditor.pelaksanaan_audit.detail', $penugasan->upt_id));
        $this->notifikasiAuditee($penugasan, 'AMI Sudah Dapat Diakses', $pesanAuditee, route('auditee.ami.detail', [
            'upt_id' => $penugasan->upt_id,
            'periode_id' => $penugasan->periode_id,
        ]));
    }

    private function notifikasiAuditor(Penugasan $penugasan, string $judul, string $pesan, string $url): void
    {
        collect([
            $penugasan->auditor1?->user,
            $penugasan->auditor2?->user,
        ])
            ->filter()
            ->unique('id')
            ->each(fn($user) => $user->notify(new PenugasanAuditNotification($penugasan, $judul, $pesan, $url)));
    }

    private function notifikasiAuditee(Penugasan $penugasan, string $judul, string $pesan, string $url): void
    {
        Auditee::with('user')
            ->where('upt_id', $penugasan->upt_id)
            ->get()
            ->pluck('user')
            ->filter()
            ->each(fn($user) => $user->notify(new PenugasanAuditNotification($penugasan, $judul, $pesan, $url)));
    }

    private function kirimNotifikasiKepalaP4mpPenugasanAktif($penugasanAktif, string $periodeId): void
    {
        $penugasanSample = $penugasanAktif->first();

        if (!$penugasanSample) {
            return;
        }

        $periode = Periode::find($periodeId);
        $tahun = $periode?->tahun ?? '-';
        $jumlahPenugasan = $penugasanAktif->count();
        $judul = 'Penugasan AMI Diaktifkan';
        $pesan = "Penugasan AMI periode {$tahun} telah diaktifkan oleh admin. Total {$jumlahPenugasan} penugasan siap direview dan ditandatangani Kepala P4MP.";
        $url = route('kepala_p4mp.penugasan.index', ['periode_id' => $periodeId]);

        User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->get()
            ->each(fn($user) => $user->notify(new PenugasanAuditNotification(
                $penugasanSample,
                $judul,
                $pesan,
                $url,
                'penugasan_aktif'
            )));
    }
}
