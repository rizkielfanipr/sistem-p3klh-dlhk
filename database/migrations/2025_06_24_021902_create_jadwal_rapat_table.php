<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalRapatTable extends Migration
{
    public function up()
    {
        Schema::create('jadwal_rapat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_id')->constrained('dokumen_persetujuan')->onDelete('cascade');
            $table->date('tanggal_rapat');
            $table->time('waktu_rapat');
            $table->string('ruang_rapat', 100);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_rapat');
    }
}

