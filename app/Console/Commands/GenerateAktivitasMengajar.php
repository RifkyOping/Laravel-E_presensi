<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalMengajar;
use App\Models\AbsensiMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateAktivitasMengajar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:generate-aktivitas-mengajar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat jurnal aktivitas mengajar otomatis berdasarkan jadwal guru pada jam yang sesuai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $hariInggris = $now->format('l'); // 'Monday', 'Tuesday', dll
        
        $mapHari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];

        $hariIni = $mapHari[$hariInggris] ?? '';

        if ($hariIni === 'Sabtu' || $hariIni === 'Minggu' || $hariIni === '') {
            return; // Tidak ada jadwal hari Sabtu & Minggu
        }

        // Jam dan menit saat ini, misal '07:15:00'
        $jamSekarang = $now->format('H:i') . ':00';

        $setting = \App\Models\SchoolSetting::get();
        $blokAktif = $setting->blok_jadwal_aktif ?? 'A';
        
        if ($blokAktif === 'TEFA') {
            return; // Tidak ada jadwal mengajar pada saat TEFA
        }

        // Cari semua jadwal untuk hari ini dan blok aktif (tanpa menunggu jam mulai)
        $jadwalCocok = JadwalMengajar::where('hari', $hariIni)
            ->whereIn('tipe_blok', ['Semua', $blokAktif])
            ->get();

        $jumlahDibuat = 0;
        $tanggalHariIni = $now->format('Y-m-d');

        foreach ($jadwalCocok as $jadwal) {
            // Cek agar tidak ganda jika command ter-trigger 2x dalam semenit
            $sudahAda = AbsensiMengajar::where('user_id', $jadwal->user_id)
                ->where('tanggal', $tanggalHariIni)
                ->where('jam_ke', $jadwal->jam_ke)
                ->whereTime('jam_mulai', $jadwal->jam_mulai)
                ->exists();

            if (!$sudahAda) {
                AbsensiMengajar::create([
                    'user_id' => $jadwal->user_id,
                    'tanggal' => $tanggalHariIni,
                    'mata_pelajaran' => $jadwal->mata_pelajaran,
                    'kelas' => $jadwal->kelas,
                    'jam_ke' => $jadwal->jam_ke,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                ]);
                $jumlahDibuat++;
            }
        }

        if ($jumlahDibuat > 0) {
            Log::info("GenerateAktivitasMengajar: Berhasil membuat $jumlahDibuat jurnal otomatis untuk jam $jamSekarang.");
        }
    }
}
