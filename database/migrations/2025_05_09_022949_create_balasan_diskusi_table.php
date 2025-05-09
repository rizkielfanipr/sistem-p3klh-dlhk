<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balasan_diskusi', function (Blueprint $table) {
            $table->id();
            $table->text('balasan_diskusi');
            $table->foreignId('forum_diskusi_id')->constrained('forum_diskusi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balasan_diskusi');
    }
};
