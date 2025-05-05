<?php

namespace Database\Seeders;

// database/seeders/RoleSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::insert([
            ['nama_role' => 'Admin'],
            ['nama_role' => 'Front Office'],
            ['nama_role' => 'Pengguna']
        ]);
    }
}

