<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Make sure Carbon is imported

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'nama_usaha',
        'bidang_usaha',
        'skala_besaran',
        'lokasi',
        'pemrakarsa',
        'penanggung_jawab',
        'deskripsi',
        'dampak',
        'judul',
        'jenis_perling',
        'user_id',
        'lampiran_id',
        'dokumen_id',
        'image',
    ];


    protected $appends = ['is_active', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class);
    }

    public function tanggapan()
    {
        return $this->hasMany(TanggapanPengumuman::class);
    }

    public function dokumen()
    {
        return $this->belongsTo(DokumenPersetujuan::class, 'dokumen_id');
    }

    public function getIsActiveAttribute(): bool
    {
        return Carbon::parse($this->created_at)->addDays(3)->isFuture();
    }

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }
}