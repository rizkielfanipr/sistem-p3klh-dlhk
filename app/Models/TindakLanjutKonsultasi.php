<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjutKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjut_konsultasi';

    protected $fillable = [
        'konsultasi_id',
        'catatan_tindaklanjut',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }
}
