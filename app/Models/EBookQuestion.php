<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EBookQuestion extends Model
{
    protected $fillable = [
        'e_book_id',
        'pertanyaan',
        'opsi_jawaban',
        'kunci_jawaban'
    ];

    protected $casts = [
        'opsi_jawaban' => 'array',
    ];

    public function eBook()
    {
        return $this->belongsTo(EBook::class);
    }
}
