<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model
{
    protected $fillable = [
        'user_id',
        'hari',
        'tipe_blok',
        'mata_pelajaran',
        'kelas',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function absensiKelas()
    {
        return $this->hasMany(AbsensiKelasSiswa::class);
    }
}
