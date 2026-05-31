<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance pedoman_rapp: add UP link + gabungan flag
        Schema::table('pedoman_rapp', function (Blueprint $table) {
            $table->unsignedTinyInteger('unit_pelayanan_id')->nullable()->after('tahun');
            $table->boolean('is_gabungan')->default(false)->after('status');
        });

        // 2. Page permissions for access control
        Schema::create('page_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug', 50);
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('unit_pelayanan_id')->nullable();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_edit')->default(true);
            $table->boolean('can_approve')->default(false);
            $table->timestamps();
            $table->index(['page_slug', 'role_id']);
            $table->index(['page_slug', 'user_id']);
        });

        // 3. Petugas slot template
        Schema::create('petugas_slot_template', function (Blueprint $table) {
            $table->id();
            $table->string('nama_slot', 100);
            $table->integer('jumlah_default')->default(1);
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->string('catatan', 200)->nullable();
            $table->timestamps();
        });

        // 4. Seed default slot templates
        DB::table('petugas_slot_template')->insert([
            ['nama_slot' => 'Pendeta Pelayanan', 'jumlah_default' => 1, 'urutan' => 1, 'aktif' => true, 'catatan' => 'Insentif jika dari luar gereja, 0 jika Pendeta Gereja Sion', 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Song Leader', 'jumlah_default' => 2, 'urutan' => 2, 'aktif' => true, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Pianis / Pemusik', 'jumlah_default' => 1, 'urutan' => 3, 'aktif' => true, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Operator LCD', 'jumlah_default' => 1, 'urutan' => 4, 'aktif' => true, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Operator Multimedia / Livestreaming', 'jumlah_default' => 1, 'urutan' => 5, 'aktif' => true, 'catatan' => 'Untuk Ibadah Minggu Pagi dan Perayaan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Pengedar Kolekte', 'jumlah_default' => 3, 'urutan' => 6, 'aktif' => true, 'catatan' => 'Dari jemaat: Dkn, Pnt, dll', 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Pembacaan Alkitab', 'jumlah_default' => 1, 'urutan' => 7, 'aktif' => true, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_slot' => 'Liturgos', 'jumlah_default' => 1, 'urutan' => 8, 'aktif' => true, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. Add petugas quota setting
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'petugas_quota_per_bulan'],
            ['setting_value' => '4', 'updated_at' => now()]
        );

        // 6. Add jenis_tugas to jadwal_petugas for slot tracking
        if (!Schema::hasColumn('jadwal_petugas', 'slot_nama')) {
            Schema::table('jadwal_petugas', function (Blueprint $table) {
                $table->string('slot_nama', 100)->nullable()->after('jenis_tugas');
            });
        }
    }

    public function down(): void
    {
        Schema::table('jadwal_petugas', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_petugas', 'slot_nama')) $table->dropColumn('slot_nama');
        });
        Schema::dropIfExists('petugas_slot_template');
        Schema::dropIfExists('page_permissions');
        Schema::table('pedoman_rapp', function (Blueprint $table) {
            if (Schema::hasColumn('pedoman_rapp', 'unit_pelayanan_id')) $table->dropColumn('unit_pelayanan_id');
            if (Schema::hasColumn('pedoman_rapp', 'is_gabungan')) $table->dropColumn('is_gabungan');
        });
    }
};
