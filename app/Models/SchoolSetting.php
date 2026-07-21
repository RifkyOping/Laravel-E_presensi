<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SchoolSetting extends Model
{
    protected $table = 'school_settings';

    protected $fillable = [
        'latitude',
        'longitude',
        'radius_meter',
        'nama_sekolah',
        'tahun_ajaran',
        'absen_datang_buka',
        'absen_datang_tutup',
        'batas_waktu_terlambat',
        'absen_pulang_buka',
        'absen_pulang_tutup',
        'batas_pulang_cepat',
        'status_absen',
    ];

    protected $casts = [
        'latitude'     => 'float',
        'longitude'    => 'float',
        'radius_meter' => 'integer',
    ];

    /**
     * Ambil satu-satunya record setting (singleton).
     */
    public static function get(): self
    {
        return static::firstOrCreate([], [
            'latitude'     => -3.5432,
            'longitude'    => 118.9759,
            'radius_meter' => 200,
            'nama_sekolah' => 'UPTD SMKN 1 Majene',
            'tahun_ajaran' => date('Y') . ' / ' . (date('Y') + 1),
            'absen_datang_buka'  => '06:00:00',
            'absen_datang_tutup' => '08:00:00',
            'batas_waktu_terlambat' => '07:15:00',
            'absen_pulang_buka'  => '15:00:00',
            'absen_pulang_tutup' => '17:00:00',
            'batas_pulang_cepat' => '15:00:00',
            'status_absen' => 'auto',
        ]);
    }

    /**
     * Hitung jarak antara dua titik GPS menggunakan rumus Haversine (dalam meter).
     */
    public static function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Cek apakah titik koordinat berada dalam radius sekolah.
     */
    public function dalamRadius(float $lat, float $lon): bool
    {
        $jarak = self::hitungJarak($this->latitude, $this->longitude, $lat, $lon);
        return $jarak <= $this->radius_meter;
    }

    /**
     * Cek apakah saat ini waktu absen sedang terbuka.
     * $type: 'datang' atau 'pulang'
     * Mengembalikan [bool, string alasan]
     */
    public function isAbsensiTerbuka(string $type = 'datang', string $jenis = 'hadir'): array
    {
        if (in_array($jenis, ['sakit', 'izin'])) {
            return [true, 'Pengajuan selalu terbuka.'];
        }
        if ($this->status_absen === 'buka') {
            return [true, 'Absen dibuka secara manual.'];
        }

        if ($this->status_absen === 'tutup') {
            return [false, 'Absen sedang ditutup oleh admin.'];
        }

        // status_absen === 'auto'
        $now = Carbon::now();
        
        // Translasi hari Inggris ke Indonesia
        $hariMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        $hariIni = $hariMap[$now->format('l')];

        $jadwal = \App\Models\JadwalAbsensi::where('hari', $hariIni)->first();

        // Jika hari ini libur atau tidak ada di database jadwal (misal Sabtu/Minggu yang tidak dimasukkan)
        if (!$jadwal || $jadwal->is_libur) {
            return [false, "Hari ini ($hariIni) adalah hari libur, absen tutup."];
        }

        if ($type === 'datang') {
            $buka  = Carbon::parse($jadwal->absen_datang_buka);
            $tutup = Carbon::parse($jadwal->absen_datang_tutup);
        } else {
            $buka  = Carbon::parse($jadwal->absen_pulang_buka);
            $tutup = Carbon::parse($jadwal->absen_pulang_tutup);
        }

        $currentTime = $now->format('H:i:s');
        $bukaTime    = $buka->format('H:i:s');
        $tutupTime   = $tutup->format('H:i:s');

        if ($currentTime >= $bukaTime && $currentTime <= $tutupTime) {
            return [true, 'Absen terbuka.'];
        }

        $namaAksi = "Absen " . ucfirst($type);
        if ($jenis === 'sakit') {
            $namaAksi = "Pengajuan Sakit";
        } elseif ($jenis === 'izin') {
            $namaAksi = "Pengajuan Izin";
        }

        return [false, "{$namaAksi} hari ini ($hariIni) hanya bisa dilakukan antara pukul " . $buka->format('H:i') . " hingga " . $tutup->format('H:i') . " WITA."];
    }
}
