<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPerling extends Model
{
    // Tambahkan ini agar Laravel tahu nama tabel yang digunakan
    protected $table = 'jenis_perling';

    // Field yang boleh diisi
    protected $fillable = ['nama_perling'];

    // Relasi ke DokumenPersetujuan
    public function dokumen()
    {
        return $this->hasMany(DokumenPersetujuan::class);
    }
}
