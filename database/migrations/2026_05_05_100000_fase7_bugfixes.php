<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add jadwal_ibadah_id to transaksi to properly link finalized transactions
        if (!Schema::hasColumn('transaksi', 'jadwal_ibadah_id')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->unsignedBigInteger('jadwal_ibadah_id')->nullable()->after('jenis_ibadah');
            });
        }

        // 2. Add status_baptis / status_pernikahan fields are already in warga_jemaat
        // Just ensure they exist for completeness
        if (!Schema::hasColumn('warga_jemaat', 'status_baptis')) {
            Schema::table('warga_jemaat', function (Blueprint $table) {
                $table->string('status_baptis', 50)->nullable()->after('status_sidi');
            });
        }

        // 3. Add user-created jenis ibadah (just insert more entries into existing table)
        // The table already supports this, just need UI for adding custom entries
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('jadwal_ibadah_id');
        });
    }
};
