<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiGuru extends Model
{
    protected $table = 'absensi_guru';

    protected $fillable = [
        'user_id',
        'tanggal',
        'tanggal_selesai',
        'waktu_datang',
        'waktu_pulang',
        'status',
        'keterangan',
        'file_bukti',
        'status_pengajuan',
        'is_notified',
        'judul_pengajuan',
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
