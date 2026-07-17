<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_jadwal_set',
        'rpp_file',
        'rpp_status',
        'rpp_pesan',
        'is_piket_sholat',
        'is_piket_mengajar',
        'is_guru_bahasa',
    ];

    protected $casts = [
        'is_jadwal_set'     => 'boolean',
        'is_piket_sholat'   => 'boolean',
        'is_piket_mengajar' => 'boolean',
        'is_guru_bahasa'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
