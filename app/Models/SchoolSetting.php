<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $table = 'school_settings';

    protected $fillable = [
        'latitude',
        'longitude',
        'radius_meter',
        'nama_sekolah',
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
            'nama_sekolah' => 'SMKN 1 Majene',
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
}
