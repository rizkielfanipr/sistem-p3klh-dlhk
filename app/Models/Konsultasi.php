<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    use HasFactory;

    protected $table = 'konsultasi';

    protected $fillable = [
        'user_id',
        'jenis_konsultasi_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisKonsultasi()
    {
        return $this->belongsTo(JenisKonsultasi::class);
    }

    public function detail()
    {
        return $this->hasMany(KonsultasiDetail::class);
    }

    public function tindakLanjut()
    {
        return $this->hasOne(TindakLanjutKonsultasi::class);
    }
}

