<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTindakLanjutKonsultasiTable extends Migration
{
    public function up()
    {
        Schema::create('tindak_lanjut_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi')->onDelete('cascade');
            $table->text('catatan_tindaklanjut');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tindak_lanjut_konsultasi');
    }
}

