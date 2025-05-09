<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopikKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'topik_konsultasi';

    protected $fillable = [
        'nama_topik',
    ];

    // Relasi ke forum diskusi
    public function forumDiskusi()
    {
        return $this->hasMany(ForumDiskusi::class, 'topik_id');
    }
}
