<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalRapat extends Model
{
    protected $table = 'jadwal_rapat';

    protected $fillable = [
        'dokumen_id', 'tanggal_rapat', 'waktu_rapat', 'ruang_rapat', 'catatan'
    ];

    public function dokumen()
    {
        return $this->belongsTo(DokumenPersetujuan::class, 'dokumen_id');
    }
}

