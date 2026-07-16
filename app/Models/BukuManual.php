<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuManual extends Model
{
    protected $fillable = [
        'user_id',
        'level',
        'judul',
        'penulis',
        'penerbit',
        'kota_terbit',
        'tahun_terbit',
        'jumlah_halaman',
        'foto_sampul',
        'status_selesai',
    ];

    protected $casts = [
        'status_selesai' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
