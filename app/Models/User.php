<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;

    protected $fillable = [
        'nama',
        'no_telp',
        'email',
        'email_verified_at',
        'verification_code',
        'password',
        'foto',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'verification_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class, 'user_id');
    }

    public function dokumenPersetujuan()
    {
        return $this->hasMany(DokumenPersetujuan::class, 'user_id');
    }
}
