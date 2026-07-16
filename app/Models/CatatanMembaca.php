<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanMembaca extends Model
{
    protected $table = 'catatan_membaca';

    protected $fillable = [
        'user_id',
        'jenis_buku',
        'buku_id',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function eBook()
    {
        return $this->belongsTo(EBook::class, 'e_book_id');
    }
}
