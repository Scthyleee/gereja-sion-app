<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->unsignedBigInteger('kode_akun_insentif_id')->nullable()->after('status');
            $table->foreign('kode_akun_insentif_id')->references('id')->on('kode_akun')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->dropForeign(['kode_akun_insentif_id']);
            $table->dropColumn('kode_akun_insentif_id');
        });
    }
};
