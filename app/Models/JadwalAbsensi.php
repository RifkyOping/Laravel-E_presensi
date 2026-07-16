<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalAbsensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari',
        'absen_datang_buka',
        'batas_waktu_terlambat',
        'absen_datang_tutup',
        'absen_pulang_buka',
        'batas_pulang_cepat',
        'absen_pulang_tutup',
        'is_libur',
    ];

    protected $casts = [
        'is_libur' => 'boolean',
    ];
}
