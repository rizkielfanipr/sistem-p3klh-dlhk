<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSesiKonsultasiTable extends Migration
{
    public function up()
    {
        Schema::create('sesi_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi', 50); // Sesi 1, 2, 3
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sesi_konsultasi');
    }
}

