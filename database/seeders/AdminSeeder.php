<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'AdminP3KLH',
            'no_telp' => '08123456789',
            'email' => 'admin@p3klh.com',
            'password' => Hash::make('Admin123DLHK'),
            'role_id' => Role::where('nama_role', 'Admin')->value('id'),
        ]);

        User::create([
            'nama' => 'Front Office',
            'no_telp' => '082233445566',
            'email' => 'front@p3klh.com',
            'password' => Hash::make('FO123DLHK'),
            'role_id' => Role::where('nama_role', 'Front Office')->value('id'),
        ]);
    }
}

