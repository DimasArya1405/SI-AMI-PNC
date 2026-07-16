<?php

namespace App\Http\Controllers\Auditee;

use App\Http\Controllers\Concerns\PeriodeFilterSupport;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\PengajuanJadwalAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\User;
use App\Notifications\PenugasanAuditNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PenugasanController extends Controller
{
    use PeriodeFilterSupport;

    public function index(Request $request)
    {
        $id_user = Auth::id();

        $auditee_login = Auditee::where('user_id', $id_user)->first();

        if (!$auditee_login) {
            return redirect()->back()->with('error', 'Data auditee tidak ditemukan.');
        }

        $upt_id = $auditee_login->upt_id;

        $periodeFilter = $this->getPeriodeFilterContext($request);
        $selectedPeriode = $periodeFilter['selectedPeriode'];

        if (!$selectedPeriode) {
            return redirect()->back()->with('error', 'Data periode tidak ditemukan.');
        }

        $periode_id = $selectedPeriode?->id;

        // Penugasan berdasarkan UPT auditee login
        $penugasanProdi = Penugasan::where('periode_id', $periode_id)
            ->where('upt_id', $upt_id)
            ->whereIn('status_penugasan', ['aktif', 'selesai'])
            ->with([
                'upt',
                'auditor1',
                'auditor2',
                'auditee',
                'pengajuan_jadwal_audit'
            ])
            ->get();

        $penugasan = Penugasan::where('periode_id', $periode_id)
            ->where('upt_id', $upt_id)
            ->get();

        $auditor = Auditor::where('status_aktif', '1')->get();

        $upts = Upt::withCount(['penugasan' => function ($query) use ($periode_id) {
            $query->where('periode_id', $periode_id);
        }])
            ->where('upt_id', $upt_id)
            ->get();

        $penugasan_sekarang = Penugasan::where('periode_id', $periode_id)
            ->where('upt_id', $upt_id)
            ->get();

        return view('auditee.penugasan', array_merge(compact(
            'penugasan',
            'penugasanProdi',
            'penugasan_sekarang',
            'periode_id',
            'auditor',
            'upts',
            'auditee_login',
            'upt_id'
        ), $periodeFilter));
    }

    public function ajukan(Request $request)
    {
        $request->validate([
            'penugasan_id' => ['required'],
            'tanggal' => ['required', 'date', function ($attribute, $value, $fail) {
                $this->validasiTanggalAudit($value, $fail);
            }],
            'jam' => ['required', function ($attribute, $value, $fail) {
                $this->validasiJamAudit($value, $fail);
            }],
            'alasan' => ['required', 'string'],
        ], [
            'tanggal.required' => 'Tanggal audit wajib diisi.',
            'tanggal.date' => 'Format tanggal audit tidak valid.',
            'jam.required' => 'Jam audit wajib diisi.',
            'alasan.required' => 'Alasan perubahan jadwal wajib diisi.',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::where('penugasan_id', $request->penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        if ($penugasan->status_penugasan !== 'aktif') {
            return back()->with('error', 'Jadwal pada periode yang sudah selesai hanya dapat dilihat.');
        }

        $cekPengajuan = PengajuanJadwalAudit::where('penugasan_id', $penugasan->penugasan_id)->first();

        if ($cekPengajuan) {
            return back()->with('error', 'Pengajuan jadwal untuk penugasan ini sudah ada.');
        }

        $pengajuan_jadwal = new PengajuanJadwalAudit();
        $pengajuan_jadwal->id = Str::uuid();
        $pengajuan_jadwal->penugasan_id = $penugasan->penugasan_id;
        $pengajuan_jadwal->tanggal_audit = $request->tanggal;
        $pengajuan_jadwal->jam = $request->jam;
        $pengajuan_jadwal->id_pengaju = $auditee->auditee_id;

        // Karena pengaju adalah auditee / UPT
        $pengajuan_jadwal->upt = 1;
        $pengajuan_jadwal->ketua_auditor = 0;
        $pengajuan_jadwal->anggota_auditor = 0;

        $pengajuan_jadwal->alasan = $request->alasan;
        $pengajuan_jadwal->save();

        $this->kirimNotifikasiAdminPengajuanDibuat($penugasan, $auditee->nama_lengkap);
        $this->kirimNotifikasiPihakTerkaitPengajuanDibuat($penugasan, $auditee->nama_lengkap, Auth::id());

        return back()->with('success', 'Pengajuan perubahan jadwal berhasil dikirim.');
    }

    private function validasiTanggalAudit($value, $fail): void
    {
        try {
            $tanggal = Carbon::parse($value)->startOfDay();
        } catch (\Exception $exception) {
            return;
        }

        if ($tanggal->lt(Carbon::today())) {
            $fail('Tanggal audit tidak boleh sebelum hari ini.');
        }

        if ($tanggal->isWeekend()) {
            $fail('Tanggal audit tidak boleh hari Sabtu atau Minggu.');
        }
    }

    private function validasiJamAudit($value, $fail): void
    {
        $jam = substr((string) $value, 0, 5);

        if (!preg_match('/^\d{2}:\d{2}$/', $jam)) {
            $fail('Format jam audit tidak valid.');
            return;
        }

        if ($jam < '08:00' || $jam > '16:00') {
            $fail('Jam audit hanya boleh antara 08.00 sampai 16.00.');
        }
    }

    public function setuju(Request $request)
    {
        $request->validate([
            'penugasan_id' => 'required',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::where('penugasan_id', $request->penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        if ($penugasan->status_penugasan !== 'aktif') {
            return back()->with('error', 'Jadwal pada periode yang sudah selesai hanya dapat dilihat.');
        }

        $pengajuan_jadwal = PengajuanJadwalAudit::where('penugasan_id', $penugasan->penugasan_id)->firstOrFail();
        $sudahLengkapSebelumnya = $this->pengajuanSudahLengkap($pengajuan_jadwal);

        // Auditee hanya mengisi konfirmasi UPT
        $pengajuan_jadwal->upt = 1;
        $pengajuan_jadwal->save();

        // Jika semua pihak sudah setuju, update jadwal penugasan
        if (
            $pengajuan_jadwal->upt == 1 &&
            $pengajuan_jadwal->ketua_auditor == 1 &&
            $pengajuan_jadwal->anggota_auditor == 1
        ) {
            $penugasan->tanggal_audit = $pengajuan_jadwal->tanggal_audit;
            $penugasan->jam = $pengajuan_jadwal->jam;
            $penugasan->save();

            if (!$sudahLengkapSebelumnya) {
                $this->kirimNotifikasiAdminPengajuanDisetujui($penugasan);
            }
        }

        return back()->with('success', 'Pengajuan jadwal berhasil disetujui.');
    }

    public function tolak(Request $request)
    {
        $request->validate([
            'penugasan_id' => 'required',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $penugasan = Penugasan::where('penugasan_id', $request->penugasan_id)
            ->where('upt_id', $auditee->upt_id)
            ->firstOrFail();

        if ($penugasan->status_penugasan !== 'aktif') {
            return back()->with('error', 'Jadwal pada periode yang sudah selesai hanya dapat dilihat.');
        }

        $pengajuan_jadwal = PengajuanJadwalAudit::where('penugasan_id', $penugasan->penugasan_id)->firstOrFail();

        $pengajuan_jadwal->delete();

        return back()->with('success', 'Pengajuan jadwal berhasil ditolak.');
    }

    private function pengajuanSudahLengkap(PengajuanJadwalAudit $pengajuan): bool
    {
        return $pengajuan->upt == 1 &&
            $pengajuan->ketua_auditor == 1 &&
            $pengajuan->anggota_auditor == 1;
    }

    private function kirimNotifikasiAdminPengajuanDibuat(Penugasan $penugasan, string $namaPengaju): void
    {
        $penugasan->load('upt');

        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "{$namaPengaju} mengajukan perubahan jadwal audit untuk {$namaUpt}.";

        $this->kirimNotifikasiAdmin($penugasan, 'Pengajuan Jadwal Audit', $pesan);
    }

    private function kirimNotifikasiAdminPengajuanDisetujui(Penugasan $penugasan): void
    {
        $penugasan->load('upt');

        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $tanggal = $penugasan->tanggal_audit ? Carbon::parse($penugasan->tanggal_audit)->locale('id')->translatedFormat('d F Y') : '-';
        $jam = $penugasan->jam ? date('H:i', strtotime($penugasan->jam)) : '-';
        $pesan = "Pengajuan jadwal audit untuk {$namaUpt} sudah disetujui semua pihak. Jadwal baru: {$tanggal} pukul {$jam}.";

        $this->kirimNotifikasiAdmin($penugasan, 'Pengajuan Jadwal Disetujui', $pesan);
    }

    private function kirimNotifikasiAdmin(Penugasan $penugasan, string $judul, string $pesan): void
    {
        $url = route('admin.ami.penugasan.detail', $penugasan->periode_id);

        User::where('role', 'admin')
            ->get()
            ->each(fn ($admin) => $admin->notify(new PenugasanAuditNotification($penugasan, $judul, $pesan, $url)));
    }

    private function kirimNotifikasiPihakTerkaitPengajuanDibuat(Penugasan $penugasan, string $namaPengaju, string $userPengajuId): void
    {
        $penugasan->load(['upt', 'auditor1.user', 'auditor2.user']);

        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "{$namaPengaju} mengajukan perubahan jadwal audit untuk {$namaUpt}. Silakan cek dan berikan persetujuan.";
        $url = route('auditor.penugasan');

        collect([
            $penugasan->auditor1?->user,
            $penugasan->auditor2?->user,
        ])
            ->filter()
            ->reject(fn ($user) => $user->id === $userPengajuId)
            ->unique('id')
            ->each(fn ($user) => $user->notify(new PenugasanAuditNotification($penugasan, 'Pengajuan Jadwal Audit', $pesan, $url)));
    }
}
