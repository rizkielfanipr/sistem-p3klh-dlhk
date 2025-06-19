<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'konten',
        'tanggal',
        'lampiran_id',
        'user_id',
    ];

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class, 'lampiran_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
