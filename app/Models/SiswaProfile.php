<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'kelas',
        'jurusan',
        'rombel',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'skip_voice_verification',
    ];

    protected $casts = [
        'tanggal_lahir'             => 'date',
        'skip_voice_verification'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
