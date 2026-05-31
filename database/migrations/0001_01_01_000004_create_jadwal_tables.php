<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- PETUGAS ---
        Schema::create('petugas', function (Blueprint $table) {
            $table->id('id_petugas');
            $table->unsignedBigInteger('warga_id')->nullable();
            $table->string('nama_petugas', 100);
            $table->string('jenis_tugas', 50);
            $table->foreignId('kode_akun_id')->nullable()->constrained('kode_akun');
            $table->string('no_telepon', 20)->nullable();
            $table->decimal('insentif', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('warga_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
        });

        // --- JENIS IBADAH MASTER ---
        Schema::create('jenis_ibadah_master', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // --- JADWAL IBADAH ---
        Schema::create('jadwal_ibadah', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->date('tanggal');
            $table->string('jenis_ibadah', 100)->default('Ibadah Minggu');
            $table->time('waktu')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('lokasi', 200)->nullable();
            $table->integer('jumlah_hadir')->default(0);
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });

        // --- JADWAL PETUGAS (with insentif per assignment) ---
        Schema::create('jadwal_petugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id');
            $table->unsignedBigInteger('petugas_id');
            $table->string('jenis_tugas', 50);
            $table->decimal('nominal_insentif', 15, 2)->default(0);
            $table->boolean('is_override')->default(false);
            $table->timestamps();

            $table->foreign('jadwal_id')->references('id_jadwal')->on('jadwal_ibadah')->cascadeOnDelete();
            $table->foreign('petugas_id')->references('id_petugas')->on('petugas');
        });

        // --- JADWAL KANTONG (persembahan per kantong) ---
        Schema::create('jadwal_kantong', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id');
            $table->foreignId('kode_akun_id')->constrained('kode_akun');
            $table->string('label', 200);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('jadwal_id')->references('id_jadwal')->on('jadwal_ibadah')->cascadeOnDelete();
        });

        // --- JADWAL AMPLOP (per KK) ---
        Schema::create('jadwal_amplop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id');
            $table->unsignedBigInteger('warga_id')->nullable();
            $table->string('nama_pemberi', 200);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('jadwal_id')->references('id_jadwal')->on('jadwal_ibadah')->cascadeOnDelete();
            $table->foreign('warga_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
        });

        // --- JADWAL AMPLOP DETAIL (per kode akun per amplop) ---
        Schema::create('jadwal_amplop_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amplop_id')->constrained('jadwal_amplop')->cascadeOnDelete();
            $table->foreignId('kode_akun_id')->constrained('kode_akun');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_amplop_detail');
        Schema::dropIfExists('jadwal_amplop');
        Schema::dropIfExists('jadwal_kantong');
        Schema::dropIfExists('jadwal_petugas');
        Schema::dropIfExists('jadwal_ibadah');
        Schema::dropIfExists('jenis_ibadah_master');
        Schema::dropIfExists('petugas');
    }
};
