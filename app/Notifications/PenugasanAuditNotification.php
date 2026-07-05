<?php

namespace App\Notifications;

use App\Models\Penugasan;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;

class PenugasanAuditNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Penugasan $penugasan,
        protected string $judul,
        protected string $pesan,
        protected string $url,
        protected ?string $jenis = null
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'url' => $this->url,
            'jenis' => $this->jenis,
            'penugasan_id' => $this->penugasan->penugasan_id,
            'periode_id' => $this->penugasan->periode_id,
            'upt_id' => $this->penugasan->upt_id,
            'nama_upt' => $this->penugasan->upt?->nama_upt,
            'tanggal_audit' => $this->penugasan->tanggal_audit
                ? Carbon::parse($this->penugasan->tanggal_audit)->locale('id')->translatedFormat('d F Y')
                : null,
            'jam' => $this->penugasan->jam,
        ];
    }
}
