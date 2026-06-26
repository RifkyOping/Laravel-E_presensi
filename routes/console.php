<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (Schema::hasTable('school_settings')) {
    $setting = SchoolSetting::first();
    if ($setting && $setting->status_absen === 'auto') {
        if ($setting->absen_datang_tutup) {
            $timeDatang = \Carbon\Carbon::parse($setting->absen_datang_tutup)->format('H:i');
            Schedule::command('presensi:cek-alpha')->dailyAt($timeDatang);
        }
        
        if ($setting->absen_pulang_tutup) {
            $timePulang = \Carbon\Carbon::parse($setting->absen_pulang_tutup)->format('H:i');
            Schedule::command('presensi:cek-alpha')->dailyAt($timePulang); // Sebagai pengaman (safety net)
            Schedule::command('presensi:cek-lupa-pulang')->dailyAt($timePulang);
        }
    }
}

// Menjalankan cek aktivitas mengajar secara otomatis setiap menit
Schedule::command('presensi:generate-aktivitas-mengajar')->everyMinute();
