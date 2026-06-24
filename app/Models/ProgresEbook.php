<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresEbook extends Model
{
    protected $table = 'progres_ebook';

    protected $fillable = [
        'user_id',
        'e_book_id',
        'selesai',
        'skor_suara',
        'lulus_suara',
        'lulus_kuis',
        'jawaban_kuis',
        'skor_kuis',
        'selesai_pada',
    ];

    protected $casts = [
        'selesai'      => 'boolean',
        'lulus_suara'  => 'boolean',
        'lulus_kuis'   => 'boolean',
        'jawaban_kuis' => 'array',
        'selesai_pada' => 'datetime',
    ];

    public function ebook()
    {
        return $this->belongsTo(EBook::class, 'e_book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
