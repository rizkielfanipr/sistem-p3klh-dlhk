<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'sesi_konsultasi';

    protected $fillable = [
        'nama_sesi',
    ];
}

