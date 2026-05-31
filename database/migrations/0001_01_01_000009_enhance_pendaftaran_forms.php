<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Baptis: tambah saksi, biaya, scan form ─────
        Schema::table('pendaftaran_baptis', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftaran_baptis', 'saksi_1')) $table->string('saksi_1')->nullable()->after('nama_ibu');
            if (!Schema::hasColumn('pendaftaran_baptis', 'saksi_2')) $table->string('saksi_2')->nullable()->after('saksi_1');
            if (!Schema::hasColumn('pendaftaran_baptis', 'biaya_baptis')) $table->decimal('biaya_baptis', 12, 2)->nullable()->after('saksi_2');
            if (!Schema::hasColumn('pendaftaran_baptis', 'scan_formulir_path')) $table->string('scan_formulir_path')->nullable()->after('file_manager_id');
        });

        // ─── Nikah: tambah saksi, biaya, checklist ──────
        Schema::table('pendaftaran_nikah', function (Blueprint $table) {
            $table->string('saksi_pria_1')->nullable()->after('wanita_no_telepon');
            $table->string('saksi_pria_2')->nullable()->after('saksi_pria_1');
            $table->string('saksi_wanita_1')->nullable()->after('saksi_pria_2');
            $table->string('saksi_wanita_2')->nullable()->after('saksi_wanita_1');
            $table->string('scan_formulir_path')->nullable()->after('file_manager_id');
        });

        // ─── Keluarga: tambah no_kk_gereja ──────────────
        if (!Schema::hasColumn('keluarga', 'no_kk_gereja')) {
            Schema::table('keluarga', function (Blueprint $table) {
                $table->string('no_kk_gereja', 50)->nullable()->after('no_kk');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pendaftaran_baptis', function (Blueprint $table) {
            $cols = ['saksi_1', 'saksi_2', 'biaya_baptis', 'scan_formulir_path'];
            foreach ($cols as $c) if (Schema::hasColumn('pendaftaran_baptis', $c)) $table->dropColumn($c);
        });
        Schema::table('pendaftaran_nikah', function (Blueprint $table) {
            $cols = ['saksi_pria_1', 'saksi_pria_2', 'saksi_wanita_1', 'saksi_wanita_2', 'scan_formulir_path'];
            foreach ($cols as $c) if (Schema::hasColumn('pendaftaran_nikah', $c)) $table->dropColumn($c);
        });
        if (Schema::hasColumn('keluarga', 'no_kk_gereja')) {
            Schema::table('keluarga', function (Blueprint $table) { $table->dropColumn('no_kk_gereja'); });
        }
    }
};
