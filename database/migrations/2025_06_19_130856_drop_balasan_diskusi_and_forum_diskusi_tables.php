<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBalasanDiskusiAndForumDiskusiTables extends Migration
{
    /**
     * Run the migrations (DROP tables).
     */
    public function up(): void
    {
        Schema::dropIfExists('balasan_diskusi');
        Schema::dropIfExists('forum_diskusi');
    }

    /**
     * Reverse the migrations (RE-CREATE tables if needed).
     */
    public function down(): void
    {
        Schema::create('forum_diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topik_id')->constrained('topik_konsultasi')->onDelete('cascade');
            $table->string('judul_diskusi');
            $table->text('uraian_diskusi')->nullable();
            $table->date('tanggal_diskusi');
            $table->foreignId('lampiran_id')->nullable()->constrained('lampiran')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('balasan_diskusi', function (Blueprint $table) {
            $table->id();
            $table->text('balasan_diskusi');
            $table->foreignId('forum_diskusi_id')->constrained('forum_diskusi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
}
