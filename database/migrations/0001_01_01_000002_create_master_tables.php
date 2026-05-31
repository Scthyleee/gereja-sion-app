<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- WARGA JEMAAT ---
        Schema::create('warga_jemaat', function (Blueprint $table) {
            $table->id('id_warga');
            $table->string('nama_lengkap');
            $table->string('no_kk', 30)->nullable();
            $table->string('lingkungan', 50)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai', 'Duda/Janda'])->nullable();
            $table->string('nama_pasangan')->nullable();
            $table->unsignedBigInteger('pasangan_id')->nullable();
            $table->enum('status_sidi', ['Sudah Sidi', 'Belum Sidi'])->default('Belum Sidi');
            $table->boolean('is_petugas')->default(false);
            $table->enum('status_jemaat', ['aktif', 'pindah', 'meninggal'])->default('aktif');
            $table->date('tanggal_bergabung')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // --- UNIT PELAYANAN (ZA5) ---
        Schema::create('unit_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('nomor_up')->unique();
            $table->string('kode_apn', 20);
            $table->string('kode_apg', 20);
            $table->string('inisial', 10);
            $table->string('nama', 150);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // --- KODE AKUN (ZA6) ---
        Schema::create('kode_akun', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->enum('tipe', ['penerimaan', 'pengeluaran']);
            $table->tinyInteger('nomor_up');
            $table->tinyInteger('nomor_menu');
            $table->string('nama', 250);
            $table->text('keterangan')->nullable();
            $table->text('sumber_lapangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->foreign('nomor_up')->references('nomor_up')->on('unit_pelayanan');
            $table->index(['tipe', 'nomor_up']);
        });

        // --- MAPPING KODE LAMA ---
        Schema::create('mapping_kode_lama', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lama', 30);
            $table->string('nama_lama');
            $table->foreignId('kode_akun_baru_id')->constrained('kode_akun');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // --- SALDO AWAL ---
        Schema::create('saldo_awal', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->enum('kantong_dana', ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan']);
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
            $table->unique(['tahun', 'kantong_dana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_awal');
        Schema::dropIfExists('mapping_kode_lama');
        Schema::dropIfExists('kode_akun');
        Schema::dropIfExists('unit_pelayanan');
        Schema::dropIfExists('warga_jemaat');
    }
};
