<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiSiswa extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'user_id',
        'tanggal',
        'tanggal_selesai',
        'waktu_datang',
        'waktu_pulang',
        'status',
        'keterangan',
        'latitude',
        'longitude',
        'file_bukti',
        'status_pengajuan',
        'is_notified',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
