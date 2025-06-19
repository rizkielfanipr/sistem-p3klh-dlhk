<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'status_konsultasi';

    protected $fillable = [
        'nama_status',
    ];
}

