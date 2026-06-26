<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AbsensiSiswa;
use App\Models\AbsensiGuru;
use App\Models\SchoolSetting;
use Carbon\Carbon;

class CekLupaPulangAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:cek-lupa-pulang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek user yang sudah absen datang tapi tidak absen pulang setelah jam pulang ditutup, lalu mengubah statusnya menjadi alpha';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $setting = SchoolSetting::first();
        if (!$setting) {
            $this->error('SchoolSetting tidak ditemukan.');
            return;
        }

        if ($setting->status_absen === 'buka') {
            $this->info('Absensi sedang dibuka secara manual. Cek lupa pulang tidak akan berjalan sampai ditutup atau dikembalikan ke otomatis.');
            return;
        }

        if ($setting->status_absen === 'auto') {
            $tutup = Carbon::createFromTimeString($setting->absen_pulang_tutup);
            // Jika saat ini belum melewati jam tutup pulang, jangan jalankan
            if (now()->lessThan($tutup)) {
                $this->info('Belum melewati jam tutup absensi pulang.');
                return;
            }
        }

        // Kalau hari minggu, skip
        if (now()->isSunday()) {
            $this->info('Hari Minggu libur.');
            return;
        }

        $today = Carbon::today()->toDateString();

        // 1. Proses Siswa
        $siswaAbsens = AbsensiSiswa::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNull('waktu_pulang')
            ->get();
            
        foreach ($siswaAbsens as $absen) {
            $absen->update(['status' => 'alpha']);
        }

        // 2. Proses Guru
        $guruAbsens = AbsensiGuru::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNull('waktu_pulang')
            ->get();
            
        foreach ($guruAbsens as $absen) {
            $absen->update(['status' => 'alpha']);
        }

        $this->info("Proses cek lupa pulang berhasil untuk tanggal $today. Total di-alpha-kan: " . ($siswaAbsens->count() + $guruAbsens->count()));
    }
}
