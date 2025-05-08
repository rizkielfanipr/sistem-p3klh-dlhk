<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriLayanan extends Model
{
    protected $table = 'kategori_layanan';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['nama_kategori'];

    public function layanan()
    {
        return $this->hasOne(Layanan::class, 'kategori_id');
    }
}