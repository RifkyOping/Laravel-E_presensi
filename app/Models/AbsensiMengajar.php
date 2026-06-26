<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiMengajar extends Model
{
    protected $table = 'absensi_mengajar';

    protected $fillable = [
        'user_id',
        'tanggal',
        'mata_pelajaran',
        'kelas',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'foto_verifikasi',
        'catatan_kurikulum',
        'status_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
