<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    use HasFactory;

    protected $table = 'informasi';

    protected $fillable = [
        'jenis_informasi_id',
        'judul',
        'konten',
        'tanggal',
        'lampiran_id',
        'user_id',
    ];

    public function jenisInformasi()
    {
        return $this->belongsTo(JenisInformasi::class, 'jenis_informasi_id');
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class, 'lampiran_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

