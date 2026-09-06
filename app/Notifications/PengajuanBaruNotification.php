<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PengajuanBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $namaSiswa;
    protected $jenis; // 'izin', 'sakit'

    /**
     * Create a new notification instance.
     */
    public function __construct($namaSiswa, $jenis)
    {
        $this->namaSiswa = $namaSiswa;
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
        $jenisIndo = ucfirst($this->jenis);
        
        $title = 'Pengajuan ' . $jenisIndo . ' Baru 📩';
        $body = "Murid {$this->namaSiswa} telah mengajukan {$jenisIndo}. Silakan cek untuk menyetujui.";

        return (new WebPushMessage)
            ->title($title)
            ->icon('/images/logo.png')
            ->body($body)
            ->action('Cek Pengajuan', '/guru/persetujuan-absensi')
            ->options(['TTL' => 1000]);
    }
}
