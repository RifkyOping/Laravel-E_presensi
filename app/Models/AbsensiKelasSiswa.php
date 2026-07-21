<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiKelasSiswa extends Model
{
    protected $table = 'absensi_kelas_siswa';

    protected $fillable = [
        'jadwal_mengajar_id',
        'guru_id',
        'siswa_id',
        'tanggal',
        'status',
        'keterangan',
        'materi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwalMengajar(): BelongsTo
    {
        return $this->belongsTo(JadwalMengajar::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}
