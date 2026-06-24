<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanLiterasiQuran extends Model
{
    protected $table = 'catatan_literasi_quran';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
