<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanIndikator extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_buku',
        'buku_id',
        'indikator_id',
        'jawaban',
        'nilai_guru',
        'catatan_guru',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function indikator()
    {
        return $this->belongsTo(IndikatorLiterasi::class, 'indikator_id');
    }
}
