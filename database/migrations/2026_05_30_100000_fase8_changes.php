<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =============================================
        // CHANGE 2: Sub Master Akun — parent_kode & kategori_akun
        // =============================================
        Schema::table('kode_akun', function (Blueprint $table) {
            $table->string('parent_kode', 20)->nullable()->after('kode');
            $table->enum('kategori_akun', ['mph', 'kategorial'])->default('mph')->after('sumber_lapangan');
        });

        $now = now();

        // --- Sub-akun I(APn).01.01 — Kolekte (KK) ---
        $sub_01_01 = [
            ['kode' => 'I(APn).01.01a', 'nama' => 'KK Ibadah Minggu',                              'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.01b', 'nama' => 'KK Ibadah Perayaan (Natal, Paskah, dll)',         'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.01c', 'nama' => 'KK Ibadah Keluarga',                             'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.01d', 'nama' => 'KK Ibadah Pernikahan',                           'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.01e', 'nama' => 'KK Ibadah Duka/Penghiburan/Mengenang',           'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.01f', 'nama' => 'KK SPPer',                                       'kategori_akun' => 'kategorial'],
            ['kode' => 'I(APn).01.01g', 'nama' => 'KK SPA',                                         'kategori_akun' => 'kategorial'],
            ['kode' => 'I(APn).01.01h', 'nama' => 'KK Ibadah Lainnya',                              'kategori_akun' => 'mph'],
        ];

        foreach ($sub_01_01 as $a) {
            DB::table('kode_akun')->insert(array_merge($a, [
                'parent_kode' => 'I(APn).01.01',
                'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 1, 'aktif' => true,
                'sumber_lapangan' => 'Sub-akun dari Kolekte ibadah',
                'created_at' => $now, 'updated_at' => $now,
            ]));
        }

        // --- Sub-akun I(APn).01.02 — PS & Penerimaan Lainnya ---
        $sub_01_02 = [
            ['kode' => 'I(APn).01.02a', 'nama' => 'PS Bulanan',                     'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02b', 'nama' => 'PS Perpuluhan',                  'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02c', 'nama' => 'PS HUT Kelahiran',               'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02d', 'nama' => 'PS HUT Pernikahan',              'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02e', 'nama' => 'PS Natal',                       'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02f', 'nama' => 'PS Tahun Baru',                  'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02g', 'nama' => 'PS Paskah',                      'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02h', 'nama' => 'PS Pentakosta',                  'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02i', 'nama' => 'PS HUT Gereja',                  'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02j', 'nama' => 'PS Khusus / Lainnya',            'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02k', 'nama' => 'Iuran GELIBU / SUMBU',           'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02l', 'nama' => 'Biaya Adm. Warga Jemaat',        'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02m', 'nama' => 'Penerimaan Pembangunan P4A',     'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02n', 'nama' => 'Penerimaan PHBG',                'kategori_akun' => 'kategorial'],
            ['kode' => 'I(APn).01.02o', 'nama' => 'Penerimaan Diakonia Sosial',     'kategori_akun' => 'kategorial'],
            ['kode' => 'I(APn).01.02p', 'nama' => 'Penghimpunan Dana Cadangan',     'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02q', 'nama' => 'KK Online',                      'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02r', 'nama' => 'PS Kel/Pribadi',                 'kategori_akun' => 'mph'],
            ['kode' => 'I(APn).01.02s', 'nama' => 'KK SHM Online',                  'kategori_akun' => 'mph'],
        ];

        foreach ($sub_01_02 as $a) {
            DB::table('kode_akun')->insert(array_merge($a, [
                'parent_kode' => 'I(APn).01.02',
                'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 2, 'aktif' => true,
                'sumber_lapangan' => 'Sub-akun dari PS MPH',
                'created_at' => $now, 'updated_at' => $now,
            ]));
        }

        // =============================================
        // CHANGE 3: Restructure Petugas & Insentif
        // =============================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop columns from petugas
        if (Schema::hasColumn('petugas', 'kode_akun_id')) {
            DB::statement('ALTER TABLE petugas DROP COLUMN kode_akun_id');
        }
        if (Schema::hasColumn('petugas', 'insentif')) {
            DB::statement('ALTER TABLE petugas DROP COLUMN insentif');
        }
        if (Schema::hasColumn('petugas', 'jenis_tugas')) {
            DB::statement('ALTER TABLE petugas DROP COLUMN jenis_tugas');
        }

        // Drop columns from petugas_roles
        if (Schema::hasColumn('petugas_roles', 'kode_akun_id')) {
            DB::statement('ALTER TABLE petugas_roles DROP COLUMN kode_akun_id');
        }
        if (Schema::hasColumn('petugas_roles', 'insentif')) {
            DB::statement('ALTER TABLE petugas_roles DROP COLUMN insentif');
        }

        // Drop column from jadwal_ibadah
        if (Schema::hasColumn('jadwal_ibadah', 'kode_akun_insentif_id')) {
            DB::statement('ALTER TABLE jadwal_ibadah DROP COLUMN kode_akun_insentif_id');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // =============================================
        // CHANGE 4: Reduce Roles
        // =============================================
        $rolesToRemove = [
            'SPPer', 'SPA', 'SPRP', 'SPB', 'SPUI', 'Pembangunan',
            'PHBG', 'KPI/PKB', 'Ketua Lingkungan', 'Bendahara Lingkungan',
            'Sekretaris Lingkungan',
        ];

        // Get Staf role id
        $stafRole = DB::table('roles')->where('nama_role', 'Staf')->first();
        if ($stafRole) {
            // Move users with removed roles to Staf
            $roleIds = DB::table('roles')->whereIn('nama_role', $rolesToRemove)->pluck('id');
            DB::table('users')->whereIn('role_id', $roleIds)->update(['role_id' => $stafRole->id]);
        }

        // Delete the roles
        DB::table('roles')->whereIn('nama_role', $rolesToRemove)->delete();

        // Add Sekretaris role if not exists
        if (!DB::table('roles')->where('nama_role', 'Sekretaris')->exists()) {
            DB::table('roles')->insert([
                'nama_role' => 'Sekretaris',
                'deskripsi' => 'Sekretaris gereja',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // =============================================
        // CHANGE 5: Database Cleanup — drop unused tables
        // =============================================
        $unusedTables = [
            'amplop_session',
            'anggaran_kegiatan',
            'approval_dokumen',
            'audit_log',
            'bahan_persidangan',
            'login_attempts',
            'mapping_kode_lama',
            'realisasi_anggaran',
            'pengurus_gereja',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($unusedTables as $tbl) {
            Schema::dropIfExists($tbl);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Sub akun cleanup
        DB::table('kode_akun')->where('parent_kode', 'I(APn).01.01')->delete();
        DB::table('kode_akun')->where('parent_kode', 'I(APn).01.02')->delete();

        Schema::table('kode_akun', function (Blueprint $table) {
            $table->dropColumn(['parent_kode', 'kategori_akun']);
        });

        // Petugas restore columns
        Schema::table('petugas', function (Blueprint $table) {
            $table->string('jenis_tugas', 50)->default('');
            $table->unsignedBigInteger('kode_akun_id')->nullable();
            $table->decimal('insentif', 15, 2)->default(0);
        });

        Schema::table('petugas_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('kode_akun_id')->nullable();
            $table->decimal('insentif', 12, 0)->default(0);
        });

        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->unsignedBigInteger('kode_akun_insentif_id')->nullable();
        });
    }
};
