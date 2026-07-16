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
    protected $description = 'Mengecek user yang sudah absen datang tapi tidak absen pulang setelah jam pulang ditutup, lalu mengubah kategorinya menjadi lupa absen pulang';

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

        $hariIniStr = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ][now()->format('l')];
        $jadwal = \App\Models\JadwalAbsensi::where('hari', $hariIniStr)->first();

        // Kalau hari ini diset libur atau tidak ada jadwal (misal weekend belum diset), skip
        if (!$jadwal || $jadwal->is_libur) {
            $this->info("Hari Libur ($hariIniStr).");
            return;
        }

        if ($setting->status_absen === 'auto') {
            $tutup = Carbon::createFromTimeString($jadwal->absen_pulang_tutup);
            // Jika saat ini belum melewati jam tutup pulang, jangan jalankan
            if (now()->lessThan($tutup)) {
                $this->info("Belum melewati jam tutup absensi pulang ($jadwal->absen_pulang_tutup).");
                return;
            }
        }

        $today = Carbon::today()->toDateString();

        // 1. Proses Siswa
        $siswaAbsens = AbsensiSiswa::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNull('waktu_pulang')
            ->get();
            
        foreach ($siswaAbsens as $absen) {
            $newKategori = ($absen->kategori === 'terlambat') ? 'terlambat dan lupa absen pulang' : 'lupa absen pulang';
            $absen->update(['kategori' => $newKategori]);
        }

        // 2. Proses Guru
        $guruAbsens = AbsensiGuru::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNull('waktu_pulang')
            ->get();
            
        foreach ($guruAbsens as $absen) {
            $newKategori = ($absen->kategori === 'terlambat') ? 'terlambat dan lupa absen pulang' : 'lupa absen pulang';
            $absen->update(['kategori' => $newKategori]);
        }

        $this->info("Proses cek lupa pulang berhasil untuk tanggal $today. Total diupdate: " . ($siswaAbsens->count() + $guruAbsens->count()));
    }
}
