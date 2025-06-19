<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten');
            $table->date('tanggal');

            // Relasi ke lampiran: gunakan unsignedInteger agar sesuai dengan lampiran_id di tabel lain
            $table->unsignedInteger('lampiran_id')->nullable();
            $table->foreign('lampiran_id')->references('id')->on('lampiran')->onDelete('set null');

            // Relasi ke user: tetap gunakan foreignId karena users.id pakai BIGINT
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
