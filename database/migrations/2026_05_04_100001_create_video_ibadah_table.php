<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('video_ibadah')) {
            Schema::create('video_ibadah', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->string('youtube_url');
                $table->date('tanggal');
                $table->string('jenis_ibadah')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('video_ibadah');
    }
};
