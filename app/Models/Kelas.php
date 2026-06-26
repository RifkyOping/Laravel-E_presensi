<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'tingkat',
        'jurusan',
        'rombel',
        'status',
    ];

    /**
     * Dapatkan format nama kelas lengkap (contoh: X RPL 1)
     */
    public function getNamaLengkapAttribute()
    {
        return "{$this->tingkat} {$this->jurusan} {$this->rombel}";
    }
}
