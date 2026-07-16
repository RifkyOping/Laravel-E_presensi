<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorLiterasi extends Model
{
    protected $fillable = [
        'pertanyaan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
