<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameInformasiToPengumumanTable extends Migration
{
    public function up()
    {
        Schema::rename('informasi', 'pengumuman');
    }

    public function down()
    {
        Schema::rename('pengumuman', 'informasi');
    }
}
