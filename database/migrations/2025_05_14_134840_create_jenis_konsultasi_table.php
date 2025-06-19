<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisKonsultasiTable extends Migration
{
    public function up()
    {
        Schema::create('jenis_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 50); // Daring, Luring
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jenis_konsultasi');
    }
}
