<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topik_id')->constrained('topik_konsultasi')->onDelete('cascade');
            $table->string('judul_diskusi', 50);
            $table->text('uraian_diskusi');
            $table->date('tanggal_diskusi');
            $table->foreignId('lampiran_id')->nullable()->constrained('lampiran')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_diskusi');
    }
};
