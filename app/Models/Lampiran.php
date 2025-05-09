<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lampiran extends Model
{
    use HasFactory;

    protected $table = 'lampiran';

    protected $fillable = ['lampiran'];

    public function informasi()
    {
        return $this->hasMany(Informasi::class, 'lampiran_id');
    }
}

