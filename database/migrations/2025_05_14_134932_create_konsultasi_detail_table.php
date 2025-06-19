<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKonsultasiDetailTable extends Migration
{
    public function up()
    {
        Schema::create('konsultasi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi')->onDelete('cascade');
            $table->foreignId('topik_id')->constrained('topik_konsultasi')->onDelete('cascade');
            $table->date('tanggal_konsultasi');
            $table->foreignId('sesi_konsultasi_id')->nullable()->constrained('sesi_konsultasi')->onDelete('set null');
            $table->text('catatan_konsultasi');
            $table->foreignId('status_id')->constrained('status_konsultasi')->onDelete('cascade');
            $table->foreignId('lampiran_id')->nullable()->constrained('lampiran')->onDelete('set null');
            $table->string('kode_konsultasi', 255)->nullable(); // hanya untuk luring
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('konsultasi_detail');
    }
}

