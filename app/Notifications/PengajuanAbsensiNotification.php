<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PengajuanAbsensiNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $status; // 'approved' or 'rejected'
    protected $jenis; // 'izin', 'sakit', 'cuti', 'tugas'

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $jenis)
    {
        $this->status = $status;
        $this->jenis = $jenis;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $statusIndo = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        $jenisIndo = ucfirst($this->jenis);
        
        $title = $this->status === 'approved' ? 'Pengajuan Disetujui! ✅' : 'Pengajuan Ditolak ❌';
        $body = "Pengajuan {$jenisIndo} Anda telah {$statusIndo}.";

        return (new WebPushMessage)
            ->title($title)
            ->icon('/images/logo.png')
            ->body($body)
            ->action('Buka Aplikasi', '/')
            ->options(['TTL' => 1000]);
    }
}
