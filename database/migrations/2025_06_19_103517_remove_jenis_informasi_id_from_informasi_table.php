<?php

// database/migrations/xxxx_xx_xx_remove_jenis_informasi_id_from_informasi_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('informasi', function (Blueprint $table) {
            $table->dropForeign(['jenis_informasi_id']);
            $table->dropColumn('jenis_informasi_id');
        });
    }

    public function down(): void
    {
        Schema::table('informasi', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_informasi_id')->nullable();
            $table->foreign('jenis_informasi_id')->references('id')->on('jenis_informasi');
        });
    }
};
