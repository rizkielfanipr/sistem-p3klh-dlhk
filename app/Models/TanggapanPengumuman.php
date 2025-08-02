<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanggapanPengumuman extends Model
{
    use HasFactory;

    protected $table = 'tanggapan_pengumuman';

    protected $fillable = [
        'pengumuman_id',
        'nama',
        'nomor_hp',
        'email',
        'jenis_kelamin',
        'isi_tanggapan',
        'tanggal_tanggapan',
    ];

    public $timestamps = false;

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class);
    }
}
