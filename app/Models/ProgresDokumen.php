<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresDokumen extends Model
{
    protected $table = 'progres_dokumen';

    protected $fillable = [
        'dokumen_id',
        'status_id',
        'catatan',
        'lampiran_id',
        'tanggal',
    ];

    public function dokumen()
    {
        return $this->belongsTo(DokumenPersetujuan::class, 'dokumen_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusDokumen::class, 'status_id');
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class, 'lampiran_id');
    }

     public function statusDokumen() // <--- Pastikan nama metode ini adalah 'statusDokumen'
    {
        return $this->belongsTo(StatusDokumen::class, 'status_id');
    }
}
