<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class AbsensiBerhasilNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tipeAbsen; // datang atau pulang
    protected $waktu;

    /**
     * Create a new notification instance.
     */
    public function __construct($tipeAbsen, $waktu)
    {
        $this->tipeAbsen = $tipeAbsen;
        $this->waktu = $waktu;
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
        $tipeAbsenFormatted = ucfirst($this->tipeAbsen);
        return (new WebPushMessage)
            ->title('Absensi Berhasil')
            ->icon('/images/logo.png')
            ->body("Absen {$tipeAbsenFormatted} Anda tercatat pada pukul {$this->waktu} WITA.")
            ->action('Buka Aplikasi', '/')
            ->options(['TTL' => 1000]);
    }
}
