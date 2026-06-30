<?php

namespace App\Http\Controllers\KepalaP4mp;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RingkasanKondisiAudit;
use App\Notifications\PenugasanAuditNotification;

class RkaController extends Controller
{
    public function acc($id){
        $rka = RingkasanKondisiAudit::with([
            'penugasan.upt',
            'penugasan.auditor1.user',
            'penugasan.auditor2.user',
            'penugasan.auditee.user',
        ])->findOrFail($id);

        if ($rka->acc_p4mp === '1') {
            return back()->with('info', 'RKA sudah pernah ditandatangani Kepala P4MP.');
        }

        if ($rka->status !== 'final') {
            return back()->with('error', 'RKA belum final sehingga belum bisa ditandatangani Kepala P4MP.');
        }

        $rka->acc_p4mp = '1';
        $rka->acc_p4mp_at = now();
        $rka->save();

        $this->kirimNotifikasiRkaDitandatangani($rka);

        return back()->with('success', 'RKA berhasil ditandatangani. Auditor sudah bisa menyusun tindakan koreksi.');
    }

    private function kirimNotifikasiRkaDitandatangani(RingkasanKondisiAudit $rka): void
    {
        $penugasan = $rka->penugasan;
        $namaUpt = $penugasan?->upt?->nama_upt ?? 'UPT';
        $pesan = "RKA {$namaUpt} telah ditandatangani oleh Kepala P4MP. Tindakan koreksi sudah dapat disusun oleh auditor.";
        $jenis = 'rka-ditandatangani-p4mp';

        $penerima = collect();

        $penerima = $penerima->merge(
            User::where('role', 'admin')->get()
        );

        $penerima = $penerima->push($penugasan?->auditor1?->user);
        $penerima = $penerima->push($penugasan?->auditor2?->user);
        $penerima = $penerima->push($penugasan?->auditee?->user);

        $penerima
            ->filter()
            ->unique('id')
            ->each(function (User $user) use ($penugasan, $pesan, $jenis) {
                $routeName = match ($user->role) {
                    'admin' => 'admin.rka.show',
                    'auditee' => 'auditee.rka.show',
                    'auditor' => 'auditor.tindakan_koreksi.show',
                    default => 'dashboard',
                };

                $url = $routeName === 'dashboard'
                    ? route('dashboard')
                    : route($routeName, $penugasan->penugasan_id);

                $sudahDikirim = $user->notifications()
                    ->where('type', PenugasanAuditNotification::class)
                    ->get()
                    ->contains(fn ($notifikasi) => ($notifikasi->data['jenis'] ?? null) === $jenis
                        && ($notifikasi->data['penugasan_id'] ?? null) === $penugasan->penugasan_id);

                if ($sudahDikirim) {
                    return;
                }

                $user->notify(new PenugasanAuditNotification(
                    $penugasan,
                    'RKA Ditandatangani Kepala P4MP',
                    $pesan,
                    $url,
                    $jenis
                ));
            });
    }
}
