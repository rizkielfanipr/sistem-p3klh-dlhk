<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayananTable extends Migration
{
    public function up()
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id');
            $table->text('konten_layanan');
            $table->unsignedBigInteger('user_id');
            // $table->timestamps(); // Jika Anda ingin menggunakan created_at dan updated_at

            $table->foreign('kategori_id')->references('id')->on('kategori_layanan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('layanan');
    }
}
