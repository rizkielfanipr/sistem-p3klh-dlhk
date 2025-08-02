<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgresDokumenTable extends Migration
{
    public function up()
    {
        Schema::create('progres_dokumen', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dokumen_id');
            $table->unsignedBigInteger('status_id');
            $table->text('catatan')->nullable();
            $table->integer('lampiran_id')->nullable(); // ← perbaikan tipe data
            $table->date('tanggal');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('dokumen_id')->references('id')->on('dokumen_persetujuan')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status_dokumen')->onDelete('restrict');
            $table->foreign('lampiran_id')->references('id')->on('lampiran')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('progres_dokumen');
    }
}
