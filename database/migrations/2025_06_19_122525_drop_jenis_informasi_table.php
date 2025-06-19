<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropJenisInformasiTable extends Migration
{
    public function up()
    {
        // Hapus foreign key dari kolom jenis_informasi_id di tabel pengumuman
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropForeign('pengumuman_ibfk_1');
            $table->dropColumn('jenis_informasi_id');
        });

        // Hapus tabel jenis_informasi
        Schema::dropIfExists('jenis_informasi');
    }

    public function down()
    {
        // Kembalikan tabel jenis_informasi
        Schema::create('jenis_informasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis');
            $table->timestamps();
        });

        // Tambahkan kembali kolom dan relasi ke pengumuman
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_informasi_id')->nullable()->after('id');
            $table->foreign('jenis_informasi_id')->references('id')->on('jenis_informasi')->onDelete('set null');
        });
    }
}
