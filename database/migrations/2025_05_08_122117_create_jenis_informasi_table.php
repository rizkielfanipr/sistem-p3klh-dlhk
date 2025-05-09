<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jenis_informasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 50); // contoh: Publikasi, Pengumuman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_informasi');
    }
};

