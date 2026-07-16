<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSholatSiswa extends Model
{
    protected $table = 'absensi_sholat_siswa';

    protected $fillable = [
        'user_id',
        'tanggal',
        'status',
        'pencatat_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'pencatat_id');
    }
}
