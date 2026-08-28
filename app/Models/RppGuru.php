<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RppGuru extends Model
{
    use HasFactory;

    protected $table = 'rpp_gurus';

    protected $fillable = [
        'user_id',
        'tingkat',
        'jurusan',
        'rpp_file',
        'rpp_status',
        'rpp_periode',
        'rpp_pesan',
    ];

    /**
     * Relasi ke User (guru pemilik RPP).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: RPP untuk periode bulan ini.
     */
    public function scopeAktif($query)
    {
        return $query->where('rpp_periode', date('Y-m'));
    }

    /**
     * Scope: RPP untuk tingkat dan jurusan tertentu.
     */
    public function scopeForKelas($query, $tingkat, $jurusan)
    {
        return $query->where('tingkat', $tingkat)->where('jurusan', $jurusan);
    }

    /**
     * Scope: RPP untuk periode tertentu.
     */
    public function scopeForPeriode($query, $periode)
    {
        return $query->where('rpp_periode', $periode);
    }
}
