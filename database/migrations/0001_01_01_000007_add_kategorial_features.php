<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add unit_pelayanan_id to users
        if (!Schema::hasColumn('users', 'unit_pelayanan_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('unit_pelayanan_id')->nullable()->after('warga_id');
            });
        }

        // 2. Input Kategorial — form submissions from each UP
        Schema::create('input_kategorial', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('unit_pelayanan_id');
            $table->enum('jenis_input', ['kolekte_ibadah', 'amplop_ps', 'amplop_kk', 'pengeluaran']);
            $table->date('tanggal');
            $table->tinyInteger('minggu_ke')->nullable();
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->string('jenis_ibadah', 100)->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['unit_pelayanan_id', 'tahun', 'bulan']);
            $table->index('status');
        });

        // 3. Input Kategorial Detail — line items
        Schema::create('input_kategorial_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_kategorial_id')->constrained('input_kategorial')->cascadeOnDelete();
            $table->foreignId('kode_akun_id')->constrained('kode_akun');
            $table->string('nama_penyetor', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->timestamps();
        });

        // 4. Approval Settings — configurable validators
        Schema::create('approval_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50); // 'input_kategorial', 'rapp'
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->tinyInteger('urutan')->default(1);
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('module');
        });

        // 5. RAPP Realisasi — actual vs budget tracking
        Schema::create('rapp_realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedoman_rapp_detail_id')->constrained('pedoman_rapp_detail')->cascadeOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->nullOnDelete();
            $table->tinyInteger('bulan');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 6. Warta Mingguan — weekly report tracker
        Schema::create('warta_mingguan', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('minggu_ke');
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->decimal('saldo_awal_minggu', 15, 2)->default(0);
            $table->decimal('total_penerimaan', 15, 2)->default(0);
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo_akhir_minggu', 15, 2)->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users');
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['minggu_ke', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warta_mingguan');
        Schema::dropIfExists('rapp_realisasi');
        Schema::dropIfExists('approval_settings');
        Schema::dropIfExists('input_kategorial_detail');
        Schema::dropIfExists('input_kategorial');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'unit_pelayanan_id')) {
                $table->dropColumn('unit_pelayanan_id');
            }
        });
    }
};
