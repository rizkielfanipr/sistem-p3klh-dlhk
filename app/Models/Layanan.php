<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['kategori_id', 'konten_layanan', 'user_id'];

    public function kategoriLayanan()
    {
        return $this->belongsTo(KategoriLayanan::class, 'kategori_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}