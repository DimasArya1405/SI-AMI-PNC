<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\User;
use App\Notifications\PenugasanAuditNotification;

class NotifikasiService
{
    // Mengirim notifikasi aplikasi dan email tanpa membuat fitur utama gagal saat SMTP bermasalah.
    public function kirimPenugasan(
        User $user,
        Penugasan $penugasan,
        string $judul,
        string $pesan,
        string $url,
        ?string $jenis = null,
        bool $kirimEmail = true
    ): void {
        // Simpan notifikasi ke database agar tetap muncul di dropdown notifikasi.
        $user->notify(new PenugasanAuditNotification($penugasan, $judul, $pesan, $url, $jenis));

        if (!$kirimEmail) {
            return;
        }

        // Kirim email secara terpisah. Jika gagal, cukup dicatat di log.
        try {
            $user->notify(new PenugasanAuditNotification($penugasan, $judul, $pesan, $url, $jenis, true, false));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
