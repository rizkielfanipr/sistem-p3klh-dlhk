<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumDiskusi extends Model
{
    use HasFactory;

    protected $table = 'forum_diskusi';

    protected $fillable = [
        'topik_id',
        'judul_diskusi',
        'uraian_diskusi',
        'tanggal_diskusi',
        'lampiran_id',
        'user_id',
    ];

    // Relasi ke topik konsultasi
    public function topik()
    {
        return $this->belongsTo(TopikKonsultasi::class, 'topik_id');
    }

    // Relasi ke lampiran
    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class, 'lampiran_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke balasan
    public function balasan()
    {
        return $this->hasMany(BalasanDiskusi::class, 'forum_diskusi_id');
    }
}
