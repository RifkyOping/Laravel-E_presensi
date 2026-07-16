<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EBook extends Model
{
    protected $table = 'e_books';

    protected $fillable = [
        'level',
        'judul',
        'deskripsi',
        'konten_teks',
        'file_pdf',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function progres()
    {
        return $this->hasMany(ProgresEbook::class, 'e_book_id');
    }

    public function questions()
    {
        return $this->hasMany(EBookQuestion::class, 'e_book_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function catatan()
    {
        // EBook memiliki banyak catatan
        return $this->hasMany(CatatanMembaca::class, 'e_book_id');
    }
}
