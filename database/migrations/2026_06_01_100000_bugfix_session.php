<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === Bug 3: Map jenis_ps_config to sub-akun codes ===
        $mapping = [
            'PS Bulanan' => 'I(APn).01.02a',
            'PS Perpuluhan' => 'I(APn).01.02b',
            'PS HUT Kelahiran' => 'I(APn).01.02c',
            'PS HUT Pernikahan' => 'I(APn).01.02d',
            'PS Natal' => 'I(APn).01.02e',
            'PS Tahun Baru' => 'I(APn).01.02f',
            'PS Paskah' => 'I(APn).01.02g',
            'PS Pentakosta' => 'I(APn).01.02h',
            'PS HUT Gereja' => 'I(APn).01.02i',
            'PS Khusus / Lainnya' => 'I(APn).01.02j',
            'Iuran GELIBU / SUMBU' => 'I(APn).01.02k',
            'Biaya Adm. Warga Jemaat' => 'I(APn).01.02l',
            'Penerimaan Pembangunan P4A' => 'I(APn).01.02m',
            'Penerimaan PHBG' => 'I(APn).01.02n',
            'Penerimaan Diakonia Sosial' => 'I(APn).01.02o',
            'Penghimpunan Dana Cadangan' => 'I(APn).01.02p',
            'KK Online' => 'I(APn).01.02q',
            'PS Kel/Pribadi' => 'I(APn).01.02r',
            'KK SHM Online' => 'I(APn).01.02s',
        ];

        foreach ($mapping as $nama => $kode) {
            DB::table('jenis_ps_config')->where('nama', $nama)->update(['kode_akun' => $kode]);
        }

        // Fix sumber_lapangan for sub-akun
        DB::table('kode_akun')
            ->where('parent_kode', 'I(APn).01.01')
            ->update(['sumber_lapangan' => 'kolekte']);

        DB::table('kode_akun')
            ->where('parent_kode', 'I(APn).01.02')
            ->update(['sumber_lapangan' => 'ps']);

        // === Bug 6: Drop unused tables ===
        Schema::dropIfExists('arsip_dokumen_otomatis');
        Schema::dropIfExists('rapp_realisasi');
        Schema::dropIfExists('rapp_target');
        Schema::dropIfExists('transaksi_detail');

        // === Bug 5: Setup default page_permissions ===
        DB::table('page_permissions')->delete();

        $allPages = ['dashboard','jadwal','jemaat','keuangan','transaksi','pendaftaran','persidangan','pedoman','files','laporan'];

        // Super Admin (1): Full access
        foreach ($allPages as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 1, 'can_view' => true, 'can_edit' => true, 'can_approve' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Admin (2): View+edit, no approve
        foreach ($allPages as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 2, 'can_view' => true, 'can_edit' => true, 'can_approve' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Bendahara (3)
        foreach (['dashboard','keuangan','transaksi','laporan','pedoman'] as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 3,
                'can_view' => true, 'can_edit' => in_array($slug, ['keuangan','transaksi']), 'can_approve' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Majelis (4)
        foreach (['dashboard','jadwal','jemaat','keuangan','transaksi','pendaftaran','persidangan','pedoman','laporan'] as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 4,
                'can_view' => true, 'can_edit' => false,
                'can_approve' => in_array($slug, ['jadwal','keuangan','transaksi','persidangan','pedoman']),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Staf (5)
        foreach (['dashboard','jadwal','jemaat','keuangan','transaksi','pendaftaran','laporan'] as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 5,
                'can_view' => true, 'can_edit' => in_array($slug, ['jadwal','jemaat','keuangan','transaksi','pendaftaran']),
                'can_approve' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Viewer (6)
        foreach (['dashboard','jadwal','jemaat','keuangan','transaksi','laporan'] as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 6,
                'can_view' => true, 'can_edit' => false, 'can_approve' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Ketua MPH (7)
        foreach ($allPages as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 7,
                'can_view' => true, 'can_edit' => true, 'can_approve' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Sekretaris (19)
        foreach (['dashboard','jemaat','pendaftaran','persidangan','files','laporan'] as $slug) {
            DB::table('page_permissions')->insert([
                'page_slug' => $slug, 'role_id' => 19,
                'can_view' => true, 'can_edit' => in_array($slug, ['jemaat','pendaftaran','persidangan','files']),
                'can_approve' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Not reversible — manual restoration needed
    }
};
