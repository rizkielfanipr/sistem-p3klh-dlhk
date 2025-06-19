<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusKonsultasiTable extends Migration
{
    public function up()
    {
        Schema::create('status_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 50); // Diajukan, Diproses, Selesai
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('status_konsultasi');
    }
}

