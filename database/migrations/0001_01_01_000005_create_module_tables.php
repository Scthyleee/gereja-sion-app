<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- PENDAFTARAN ---
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->boolean('is_jemaat')->default(false);
            $table->unsignedBigInteger('warga_id')->nullable();
            $table->unsignedBigInteger('pasangan_id')->nullable();
            $table->string('nama_pasangan')->nullable();
            $table->enum('jenis', ['warga', 'baptis', 'sidi', 'pernikahan', 'pelayanan']);
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
            $table->enum('status_sidi', ['Sudah Sidi', 'Belum Sidi'])->nullable();
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai', 'Duda/Janda'])->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->text('dokumen')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->foreignId('kode_akun_penerimaan_id')->nullable()->constrained('kode_akun');
            $table->foreignId('kode_akun_pengeluaran_id')->nullable()->constrained('kode_akun');
            $table->enum('status', ['pending', 'terdaftar', 'selesai', 'ditolak'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('warga_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('pasangan_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
        });

        // --- BAHAN PERSIDANGAN ---
        Schema::create('bahan_persidangan', function (Blueprint $table) {
            $table->id('id_dokumen');
            $table->string('judul');
            $table->string('kategori', 50)->nullable();
            $table->date('tanggal_sidang')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('deskripsi')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // --- PEDOMAN UMUM ---
        Schema::create('pedoman', function (Blueprint $table) {
            $table->id('id_pedoman');
            $table->string('judul');
            $table->string('kategori', 50)->nullable();
            $table->smallInteger('tahun')->nullable();
            $table->string('versi', 20)->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('deskripsi')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // --- FILE MANAGER ---
        Schema::create('file_manager', function (Blueprint $table) {
            $table->id();
            $table->string('nama_file');
            $table->enum('tipe', ['file', 'folder']);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('ukuran')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('file_manager')->nullOnDelete();
        });

        // --- AUDIT LOG ---
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('aksi');
            $table->string('modul')->nullable();
            $table->text('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // --- PENGURUS GEREJA ---
        Schema::create('pengurus_gereja', function (Blueprint $table) {
            $table->id('id_pengurus');
            $table->string('nama_lengkap');
            $table->string('jabatan', 100);
            $table->string('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // --- APPROVAL DOKUMEN ---
        Schema::create('approval_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->unsignedBigInteger('record_id');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users');
            $table->timestamp('diajukan_at')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->timestamp('disetujui_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // --- ARSIP DOKUMEN ---
        Schema::create('arsip_dokumen_otomatis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->string('kategori', 50)->nullable();
            $table->unsignedBigInteger('file_manager_id')->nullable();
            $table->string('module_source')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('file_manager_id')->references('id')->on('file_manager')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_dokumen_otomatis');
        Schema::dropIfExists('approval_dokumen');
        Schema::dropIfExists('pengurus_gereja');
        Schema::dropIfExists('audit_log');
        Schema::dropIfExists('file_manager');
        Schema::dropIfExists('pedoman');
        Schema::dropIfExists('bahan_persidangan');
        Schema::dropIfExists('pendaftaran');
    }
};
