<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusDokumen extends Model
{
    protected $table = 'status_dokumen';

    protected $fillable = ['nama_status'];

    public function progres()
    {
        return $this->hasMany(ProgresDokumen::class, 'status_id');
    }
}
