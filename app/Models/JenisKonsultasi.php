<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_konsultasi';

    protected $fillable = [
        'nama_jenis',
    ];
}

