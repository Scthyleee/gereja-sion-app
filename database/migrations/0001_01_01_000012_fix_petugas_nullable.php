<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix: petugas_id harus nullable untuk petugas external & pelayanan
        Schema::table('jadwal_petugas', function (Blueprint $table) {
            $table->unsignedBigInteger('petugas_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_petugas', function (Blueprint $table) {
            $table->unsignedBigInteger('petugas_id')->nullable(false)->change();
        });
    }
};
