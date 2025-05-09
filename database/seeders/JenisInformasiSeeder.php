<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisInformasiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jenis_informasi')->insert([
            ['nama_jenis' => 'Publikasi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Pengumuman', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
