<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'maks_konsultasi_daring_harian',
        'maks_konsultasi_luring_harian',
        'tanggal_tidak_tersedia_konsultasi_luring',
        'tanggal_tidak_tersedia_konsultasi_daring',
        'maks_perling_harian', // New: Maximum daily perling submissions
        'tanggal_tidak_tersedia_perling', // New: Unavailable dates for perling
    ];

    protected $casts = [
        'tanggal_tidak_tersedia_konsultasi_luring' => 'array',
        'tanggal_tidak_tersedia_konsultasi_daring' => 'array',
        'tanggal_tidak_tersedia_perling' => 'array', // New: Cast to array
    ];

    protected $table = 'settings';
}
