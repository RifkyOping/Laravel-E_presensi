<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AbsensiSiswa;
use App\Models\AbsensiGuru;
use App\Models\SchoolSetting;
use Carbon\Carbon;

class CekAlphaAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:cek-alpha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek user yang belum absen setelah jam tutup dan menandainya sebagai alpa atau sakit lanjutan';

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
            $this->info('Absensi sedang dibuka secara manual. Cek alpa tidak akan berjalan sampai ditutup atau dikembalikan ke otomatis.');
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
            $tutup = Carbon::createFromTimeString($jadwal->absen_datang_tutup);
            // Jika saat ini belum melewati jam tutup, jangan jalankan
            if (now()->lessThan($tutup)) {
                $this->info("Belum melewati jam tutup absensi ($jadwal->absen_datang_tutup).");
                return;
            }
        }

        $today = Carbon::today()->toDateString();

        // 1. Proses Siswa / Murid
        $siswaList = User::whereIn('role', ['murid', 'siswa'])->get();
        foreach ($siswaList as $siswa) {
            $absenHariIni = AbsensiSiswa::where('user_id', $siswa->id)->whereDate('tanggal', $today)->first();
            if (!$absenHariIni) {
                // 1. Cek apakah ada pengajuan izin yang sudah disetujui dan masih berlaku hari ini (berdasarkan rentang tanggal)
                $activeIzin = AbsensiSiswa::where('user_id', $siswa->id)
                    ->where('status', 'izin')
                    ->where('status_pengajuan', 'approved')
                    ->whereDate('tanggal', '<=', $today)
                    ->whereDate('tanggal_selesai', '>=', $today)
                    ->first();

                if ($activeIzin) {
                    $status = 'izin';
                    $status_pengajuan = 'approved';
                } else {
                    // 2. Cek apakah sakit berlanjut (jika absen terakhir adalah sakit yang disetujui)
                    $lastAbsen = AbsensiSiswa::where('user_id', $siswa->id)->whereDate('tanggal', '<', $today)->orderByDesc('tanggal')->first();
                    $status = ($lastAbsen && $lastAbsen->status === 'sakit' && $lastAbsen->status_pengajuan === 'approved') ? 'sakit' : 'alpa';
                    $status_pengajuan = ($status === 'sakit' && $lastAbsen && $lastAbsen->status_pengajuan === 'approved') ? 'approved' : null;
                }
                
                AbsensiSiswa::create([
                    'user_id' => $siswa->id,
                    'tanggal' => $today,
                    'status' => $status,
                    'status_pengajuan' => $status_pengajuan,
                    'kategori' => ($status === 'alpa') ? 'alpa' : null,
                ]);
            } else {
                // Jika pengajuan ditolak dan siswa belum/tidak melakukan absen datang setelah jam tutup
                if ($absenHariIni->status_pengajuan === 'rejected' && !$absenHariIni->waktu_datang && $absenHariIni->status !== 'hadir') {
                    $absenHariIni->update([
                        'status'   => 'alpa',
                        'kategori' => 'alpa',
                    ]);
                }
            }
        }

        // 2. Proses Guru
        $guruList = User::where('role', 'guru')->get();
        foreach ($guruList as $guru) {
            $absenHariIni = AbsensiGuru::where('user_id', $guru->id)->whereDate('tanggal', $today)->first();
            if (!$absenHariIni) {
                // 1. Cek apakah ada pengajuan izin yang sudah disetujui dan masih berlaku hari ini (berdasarkan rentang tanggal)
                $activeIzin = AbsensiGuru::where('user_id', $guru->id)
                    ->where('status', 'izin')
                    ->where('status_pengajuan', 'approved')
                    ->whereDate('tanggal', '<=', $today)
                    ->whereDate('tanggal_selesai', '>=', $today)
                    ->first();

                if ($activeIzin) {
                    $status = 'izin';
                    $status_pengajuan = 'approved';
                } else {
                    // 2. Cek apakah sakit berlanjut (jika absen terakhir adalah sakit yang disetujui)
                    $lastAbsen = AbsensiGuru::where('user_id', $guru->id)->whereDate('tanggal', '<', $today)->orderByDesc('tanggal')->first();
                    $status = ($lastAbsen && $lastAbsen->status === 'sakit' && $lastAbsen->status_pengajuan === 'approved') ? 'sakit' : 'alpa';
                    $status_pengajuan = ($status === 'sakit' && $lastAbsen && $lastAbsen->status_pengajuan === 'approved') ? 'approved' : null;
                }
                
                AbsensiGuru::create([
                    'user_id' => $guru->id,
                    'tanggal' => $today,
                    'status' => $status,
                    'status_pengajuan' => $status_pengajuan,
                    'kategori' => ($status === 'alpa') ? 'alpa' : null,
                ]);
            } else {
                // Jika pengajuan cuti/tugas ditolak dan guru belum/tidak melakukan absen datang setelah jam tutup
                if ($absenHariIni->status_pengajuan === 'rejected' && !$absenHariIni->waktu_datang && $absenHariIni->status !== 'hadir') {
                    $absenHariIni->update([
                        'status'   => 'alpa',
                        'kategori' => 'alpa',
                    ]);
                }
            }
        }

        $this->info("Proses cek alpa berhasil untuk tanggal $today.");
    }
}
