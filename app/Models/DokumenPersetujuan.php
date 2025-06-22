<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPersetujuan extends Model
{
    // Tambahkan ini agar Laravel tahu nama tabel yang digunakan
    protected $table = 'dokumen_persetujuan';
    
    protected $fillable = [
        'user_id', 'nama_pemohon', 'nama_usaha',
        'alamat_usaha', 'jenis_perling_id', 'lampiran_id', 'tanggal'
    ];

    public function jenisPerling()
    {
        return $this->belongsTo(JenisPerling::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class);
    }
}
