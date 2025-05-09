<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalasanDiskusi extends Model
{
    use HasFactory;

    protected $table = 'balasan_diskusi';

    protected $fillable = [
        'balasan_diskusi',
        'forum_diskusi_id',
        'user_id',
    ];

    // Relasi ke forum diskusi
    public function forumDiskusi()
    {
        return $this->belongsTo(ForumDiskusi::class, 'forum_diskusi_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
