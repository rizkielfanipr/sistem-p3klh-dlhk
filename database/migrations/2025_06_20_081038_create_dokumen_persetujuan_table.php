<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('dokumen_persetujuan', function (Blueprint $table) {
    $table->id();
    $table->integer('user_id'); // karena users.id adalah signed int
    $table->string('nama_pemohon');
    $table->string('nama_usaha');
    $table->text('alamat_usaha');
    $table->unsignedBigInteger('jenis_perling_id'); // karena jenis_perling.id adalah bigint unsigned
    $table->integer('lampiran_id')->nullable(); // karena lampiran.id adalah signed int
    $table->date('tanggal')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('jenis_perling_id')->references('id')->on('jenis_perling')->onDelete('cascade');
    $table->foreign('lampiran_id')->references('id')->on('lampiran')->onDelete('set null');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_persetujuan');
    }
};

