<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // ===== 1. Split Kolekte Perayaan I(APn).01.01b into individual sub-accounts =====
        // Deactivate the umbrella entry
        DB::table('kode_akun')->where('kode', 'I(APn).01.01b')->update(['aktif' => false]);

        $subAkun = [
            ['kode' => 'I(APn).01.01b1', 'nama' => 'KK Ibadah Natal',           'parent_kode' => 'I(APn).01.01'],
            ['kode' => 'I(APn).01.01b2', 'nama' => 'KK Ibadah Paskah',          'parent_kode' => 'I(APn).01.01'],
            ['kode' => 'I(APn).01.01b3', 'nama' => 'KK Ibadah Tahun Baru',      'parent_kode' => 'I(APn).01.01'],
            ['kode' => 'I(APn).01.01b4', 'nama' => 'KK Ibadah Tutup Tahun',     'parent_kode' => 'I(APn).01.01'],
            ['kode' => 'I(APn).01.01b5', 'nama' => 'KK Ibadah Pentakosta',      'parent_kode' => 'I(APn).01.01'],
            ['kode' => 'I(APn).01.01b6', 'nama' => 'KK Ibadah HUT Gereja',      'parent_kode' => 'I(APn).01.01'],
            ['kode' => 'I(APn).01.01b7', 'nama' => 'KK Ibadah Perayaan Lainnya','parent_kode' => 'I(APn).01.01'],
        ];

        foreach ($subAkun as $akun) {
            DB::table('kode_akun')->updateOrInsert(
                ['kode' => $akun['kode']],
                [
                    'nama'             => $akun['nama'],
                    'parent_kode'      => $akun['parent_kode'],
                    'tipe'             => 'penerimaan',
                    'nomor_up'         => 1,
                    'nomor_menu'       => 1,
                    'keterangan'       => $akun['nama'],
                    'sumber_lapangan'  => 'kolekte',
                    'kategori_akun'    => 'mph',
                    'aktif'            => true,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }

        // ===== 2. Create bahan_persidangan table =====
        if (!Schema::hasTable('bahan_persidangan')) {
            Schema::create('bahan_persidangan', function (Blueprint $table) {
                $table->bigIncrements('id_dokumen');
                $table->string('judul', 200);
                $table->string('kategori', 50)->default('Umum');
                $table->date('tanggal_sidang')->nullable();
                $table->text('deskripsi')->nullable();
                $table->string('file_path', 500)->nullable();
                $table->string('file_name', 200)->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
            });
        }

        // ===== 3. Add kode_akun_insentif_id to jadwal_ibadah =====
        if (!Schema::hasColumn('jadwal_ibadah', 'kode_akun_insentif_id')) {
            Schema::table('jadwal_ibadah', function (Blueprint $table) {
                $table->unsignedBigInteger('kode_akun_insentif_id')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        // Re-activate umbrella
        DB::table('kode_akun')->where('kode', 'I(APn).01.01b')->update(['aktif' => true]);
        // Remove sub-akun
        DB::table('kode_akun')->where('kode', 'like', 'I(APn).01.01b_')->delete();
        
        Schema::dropIfExists('bahan_persidangan');

        if (Schema::hasColumn('jadwal_ibadah', 'kode_akun_insentif_id')) {
            Schema::table('jadwal_ibadah', function (Blueprint $table) {
                $table->dropColumn('kode_akun_insentif_id');
            });
        }
    }
};
