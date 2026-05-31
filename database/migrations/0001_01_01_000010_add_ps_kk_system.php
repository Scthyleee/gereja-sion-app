<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── TRANSAKSI PS (Persembahan Syukur per jemaat) ───
        Schema::create('transaksi_ps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jemaat_id')->nullable();
            $table->date('tanggal');
            $table->string('bulan', 7); // YYYY-MM
            $table->string('jenis_ps', 50);
            $table->foreignId('kode_akun_id')->nullable()->constrained('kode_akun');
            $table->enum('kantong_dana', ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'])->default('rutin');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->string('diterima_oleh')->nullable();
            $table->string('no_kuitansi', 30)->nullable();
            $table->enum('status', ['draft', 'verified'])->default('draft');
            $table->enum('sumber', ['jadwal', 'kantor'])->default('kantor');
            $table->unsignedBigInteger('jadwal_ibadah_id')->nullable();
            $table->unsignedBigInteger('transaksi_id')->nullable(); // link to main transaksi table
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('jemaat_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('jadwal_ibadah_id')->references('id_jadwal')->on('jadwal_ibadah')->nullOnDelete();
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->nullOnDelete();
            $table->index(['bulan', 'jenis_ps']);
            $table->index('jemaat_id');
        });

        // ─── TRANSAKSI KK (Kantong Kolekte per ibadah) ───
        Schema::create('transaksi_kk', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('bulan', 7); // YYYY-MM
            $table->tinyInteger('minggu_ke')->default(1); // 1-5
            $table->string('jenis_ibadah', 100);
            $table->foreignId('kode_akun_id')->nullable()->constrained('kode_akun');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->integer('jml_kantong')->default(0);
            $table->string('petugas_hitung')->nullable();
            $table->string('diterima_oleh')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'verified'])->default('draft');
            $table->unsignedBigInteger('jemaat_id')->nullable(); // for KK Online
            $table->unsignedBigInteger('jadwal_ibadah_id')->nullable();
            $table->unsignedBigInteger('transaksi_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('jemaat_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('jadwal_ibadah_id')->references('id_jadwal')->on('jadwal_ibadah')->nullOnDelete();
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->nullOnDelete();
            $table->index(['bulan', 'minggu_ke']);
        });

        // ─── AMPLOP SESSION ───
        Schema::create('amplop_session', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jemaat_id')->nullable();
            $table->date('tanggal');
            $table->decimal('total_sesi', 15, 2)->default(0);
            $table->string('diterima_oleh')->nullable();
            $table->enum('status', ['draft', 'verified'])->default('draft');
            $table->enum('sumber', ['jadwal', 'kantor'])->default('kantor');
            $table->unsignedBigInteger('jadwal_ibadah_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('jemaat_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('jadwal_ibadah_id')->references('id_jadwal')->on('jadwal_ibadah')->nullOnDelete();
        });

        // ─── RAPP TARGET (per jemaat per jenis PS) ───
        Schema::create('rapp_target', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->unsignedBigInteger('jemaat_id');
            $table->string('jenis_ps', 50);
            $table->decimal('target_per_bulan', 15, 2)->default(0);
            $table->foreignId('kode_akun_id')->nullable()->constrained('kode_akun');
            $table->timestamps();

            $table->foreign('jemaat_id')->references('id_warga')->on('warga_jemaat')->cascadeOnDelete();
            $table->unique(['tahun', 'jemaat_id', 'jenis_ps']);
        });

        // ─── RIWAYAT JEMAAT (auto-archive, backend only) ───
        Schema::create('riwayat_jemaat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jemaat_id');
            $table->smallInteger('tahun');
            $table->string('bulan', 7); // YYYY-MM
            $table->string('jenis_ps', 50);
            $table->string('kode_akun', 30)->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->unsignedBigInteger('transaksi_ps_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('jemaat_id')->references('id_warga')->on('warga_jemaat')->cascadeOnDelete();
            $table->foreign('transaksi_ps_id')->references('id')->on('transaksi_ps')->nullOnDelete();
            $table->index(['jemaat_id', 'tahun', 'bulan']);
        });

        // ─── DATA BACKUPS ───
        Schema::create('data_backups', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->enum('jenis', ['tahunan', 'bulanan', 'manual'])->default('tahunan');
            $table->string('deskripsi')->nullable();
            $table->json('file_paths')->nullable();
            $table->integer('total_files')->default(0);
            $table->decimal('total_size_mb', 10, 2)->default(0);
            $table->enum('status', ['draft', 'generating', 'completed', 'failed'])->default('draft');
            $table->string('sql_path')->nullable();
            $table->string('zip_path')->nullable();
            $table->boolean('dibersihkan')->default(false);
            $table->timestamp('tanggal_bersih')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // ─── JENIS PS CONFIG (seeder) ───
        Schema::create('jenis_ps_config', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 80);
            $table->string('kode_akun', 30);
            $table->string('kantong_dana', 30)->default('rutin');
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Seed jenis PS config
        \Illuminate\Support\Facades\DB::table('jenis_ps_config')->insert([
            ['nama' => 'PS Bulanan', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Perpuluhan', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS HUT Kelahiran', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS HUT Pernikahan', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 4, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Natal', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 5, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Tahun Baru', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 6, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Paskah', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 7, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Pentakosta', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 8, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS HUT Gereja', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 9, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Khusus / Lainnya', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 10, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Iuran GELIBU / SUMBU', 'kode_akun' => 'I(APn).01.08', 'kantong_dana' => 'rutin', 'urutan' => 11, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Biaya Adm. Warga Jemaat', 'kode_akun' => 'I(APn).01.06', 'kantong_dana' => 'rutin', 'urutan' => 12, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Penerimaan Pembangunan P4A', 'kode_akun' => 'I(APn).09.01', 'kantong_dana' => 'pembangunan', 'urutan' => 13, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Penerimaan PHBG', 'kode_akun' => 'I(APn).08.01', 'kantong_dana' => 'phbg', 'urutan' => 14, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Penerimaan Diakonia Sosial', 'kode_akun' => 'I(APn).10.01', 'kantong_dana' => 'diakonia_sosial', 'urutan' => 15, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Penghimpunan Dana Cadangan', 'kode_akun' => 'I(APn).01.03', 'kantong_dana' => 'cadangan', 'urutan' => 16, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'KK Online', 'kode_akun' => 'I(APn).01.01', 'kantong_dana' => 'rutin', 'urutan' => 17, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PS Kel/Pribadi', 'kode_akun' => 'I(APn).01.02', 'kantong_dana' => 'rutin', 'urutan' => 18, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'KK SHM Online', 'kode_akun' => 'I(APn).01.01', 'kantong_dana' => 'rutin', 'urutan' => 19, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_ps_config');
        Schema::dropIfExists('data_backups');
        Schema::dropIfExists('riwayat_jemaat');
        Schema::dropIfExists('rapp_target');
        Schema::dropIfExists('amplop_session');
        Schema::dropIfExists('transaksi_kk');
        Schema::dropIfExists('transaksi_ps');
    }
};
