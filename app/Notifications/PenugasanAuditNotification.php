<?php

namespace App\Notifications;

use App\Models\Penugasan;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PenugasanAuditNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Penugasan $penugasan,
        protected string $judul,
        protected string $pesan,
        protected string $url,
        protected ?string $jenis = null,
        protected bool $kirimEmail = false,
        protected bool $kirimDatabase = true
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($this->kirimDatabase) {
            $channels[] = 'database';
        }

        if ($this->kirimEmail) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tanggalAudit = $this->penugasan->tanggal_audit
            ? Carbon::parse($this->penugasan->tanggal_audit)->locale('id')->translatedFormat('d F Y')
            : '-';

        $jamAudit = $this->penugasan->jam
            ? Carbon::parse($this->penugasan->jam)->format('H:i')
            : '-';

        return (new MailMessage)
            ->subject($this->judul)
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna SIAMI'))
            ->line($this->pesan)
            ->line('Unit/Auditee: ' . ($this->penugasan->upt?->nama_upt ?? '-'))
            ->line('Tanggal audit: ' . $tanggalAudit)
            ->line('Jam audit: ' . $jamAudit)
            ->action('Buka SIAMI', $this->url)
            ->line('Email ini dikirim otomatis oleh SIAMI PNC.');
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
