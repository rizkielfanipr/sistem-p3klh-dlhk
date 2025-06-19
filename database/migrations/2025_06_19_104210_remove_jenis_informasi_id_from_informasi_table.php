<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('informasi', function (Blueprint $table) {
            // Cek apakah kolom ada sebelum dihapus
            if (Schema::hasColumn('informasi', 'jenis_informasi_id')) {
                // Cek apakah foreign key ada
                $foreignKeyExists = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_NAME = 'informasi'
                    AND COLUMN_NAME = 'jenis_informasi_id'
                    AND CONSTRAINT_SCHEMA = DATABASE()
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                // Jika foreign key ada, hapus dulu
                if (count($foreignKeyExists)) {
                    $table->dropForeign(['jenis_informasi_id']);
                }

                // Lalu hapus kolomnya
                $table->dropColumn('jenis_informasi_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informasi', function (Blueprint $table) {
            if (!Schema::hasColumn('informasi', 'jenis_informasi_id')) {
                $table->unsignedBigInteger('jenis_informasi_id')->nullable();
                $table->foreign('jenis_informasi_id')->references('id')->on('jenis_informasi')->onDelete('set null');
            }
        });
    }
};
