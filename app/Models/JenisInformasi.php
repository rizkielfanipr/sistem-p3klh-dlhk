<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisInformasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_informasi';

    protected $fillable = ['nama_jenis'];

    public function informasi()
    {
        return $this->hasMany(Informasi::class, 'jenis_informasi_id');
    }
}

