<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add ayat_khotbah to jadwal_ibadah
        if (!Schema::hasColumn('jadwal_ibadah', 'ayat_khotbah')) {
            Schema::table('jadwal_ibadah', function (Blueprint $table) {
                $table->string('ayat_khotbah', 200)->nullable()->after('tema_ibadah');
            });
        }

        // Seed new CMS settings for sumbangan & ibadah minggu
        $newSettings = [
            ['key' => 'section_ibadah_minggu', 'value' => '1'],
            ['key' => 'section_sumbangan', 'value' => '1'],
            ['key' => 'rekening_bank', 'value' => 'BRI'],
            ['key' => 'rekening_nomor', 'value' => ''],
            ['key' => 'rekening_atas_nama', 'value' => ''],
            ['key' => 'whatsapp_sumbangan', 'value' => ''],
        ];

        foreach ($newSettings as $s) {
            DB::table('cms_settings')->updateOrInsert(
                ['key' => $s['key']],
                ['value' => $s['value'], 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->dropColumn('ayat_khotbah');
        });

        DB::table('cms_settings')->whereIn('key', [
            'section_ibadah_minggu', 'section_sumbangan',
            'rekening_bank', 'rekening_nomor', 'rekening_atas_nama', 'whatsapp_sumbangan',
        ])->delete();
    }
};
