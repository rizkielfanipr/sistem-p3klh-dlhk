<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('informasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jenis_informasi_id')
                  ->constrained('jenis_informasi')
                  ->onDelete('cascade');

            $table->string('judul', 255);
            $table->text('konten');
            $table->date('tanggal');

            $table->foreignId('lampiran_id')
                  ->nullable()
                  ->constrained('lampiran')
                  ->onDelete('set null');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasi');
    }
};

