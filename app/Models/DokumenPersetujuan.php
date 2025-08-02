<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DokumenPersetujuan extends Model
{
    // Tambahkan ini agar Laravel tahu nama tabel yang digunakan
    protected $table = 'dokumen_persetujuan';

    protected $fillable = [
        'user_id',
        'nama_pemohon',
        'nama_usaha',
        'bidang_usaha', // New field
        'lokasi',       // Replaced 'alamat_usaha'
        'pemrakarsa',   // New field
        'penanggung_jawab', // New field
        'jenis_perling_id',
        'lampiran_id',
        'kode_perling',
        'tanggal'
    ];

    public function jenisPerling()
    {
        return $this->belongsTo(JenisPerling::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progresDokumen()
    {
        return $this->hasMany(ProgresDokumen::class, 'dokumen_id');
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class);
    }

    public function jadwalRapat(): HasOne
    {
        return $this->hasOne(JadwalRapat::class, 'dokumen_id');
    }

    public function pengumuman()
    {
        return $this->hasOne(Pengumuman::class, 'dokumen_id');
    }
}