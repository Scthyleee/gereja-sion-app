<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- TRANSAKSI ---
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('kode_akun_id')->constrained('kode_akun');
            $table->enum('tipe', ['penerimaan', 'pengeluaran']);
            $table->string('jenis_ibadah', 100)->nullable();
            $table->string('nama_penyetor', 200)->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->enum('kantong_dana', ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'])->default('rutin');
            $table->enum('status', ['draft', 'verified', 'approved'])->default('draft');
            $table->string('bukti')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['tahun', 'bulan', 'kantong_dana']);
            $table->index('tanggal');
        });

        // --- ANGGARAN (RAPP) ---
        Schema::create('pedoman_rapp', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->smallInteger('tahun');
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pedoman_rapp_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedoman_id')->constrained('pedoman_rapp')->cascadeOnDelete();
            $table->foreignId('kode_akun_id')->constrained('kode_akun');
            $table->text('keterangan')->nullable();
            $table->integer('volume')->default(1);
            $table->decimal('nominal', 15, 2)->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('anggaran_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kode_akun_id')->constrained('kode_akun');
            $table->string('nama_kegiatan');
            $table->smallInteger('tahun');
            $table->decimal('total_anggaran', 15, 2)->default(0);
            $table->integer('volume')->default(1);
            $table->string('satuan', 50)->nullable();
            $table->text('uraian')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('realisasi_anggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggaran_id')->constrained('anggaran_kegiatan');
            $table->date('tanggal');
            $table->string('item_pengeluaran');
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_anggaran');
        Schema::dropIfExists('anggaran_kegiatan');
        Schema::dropIfExists('pedoman_rapp_detail');
        Schema::dropIfExists('pedoman_rapp');
        Schema::dropIfExists('transaksi');
    }
};
