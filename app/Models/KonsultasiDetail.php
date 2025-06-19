<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonsultasiDetail extends Model
{
    use HasFactory;

    protected $table = 'konsultasi_detail';

    protected $fillable = [
        'konsultasi_id',
        'topik_id',
        'tanggal_konsultasi',
        'sesi_konsultasi_id',
        'catatan_konsultasi',
        'status_id',
        'lampiran_id',
        'kode_konsultasi',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }

    public function topik()
    {
        return $this->belongsTo(TopikKonsultasi::class, 'topik_id');
    }

    public function sesi()
    {
        return $this->belongsTo(SesiKonsultasi::class, 'sesi_konsultasi_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusKonsultasi::class, 'status_id');
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class, 'lampiran_id');
    }
}

