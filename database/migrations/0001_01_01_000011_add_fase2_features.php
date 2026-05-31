<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah jabatan_gereja di warga_jemaat (Diakon/Penetua/null)
        Schema::table('warga_jemaat', function (Blueprint $table) {
            $table->string('jabatan_gereja', 50)->nullable()->after('status_jemaat')
                ->comment('Diakon, Penetua, atau null');
        });

        // 2. Tambah kategori & pendeta flag di petugas
        Schema::table('petugas', function (Blueprint $table) {
            $table->enum('kategori_petugas', ['insentif', 'pelayanan'])->default('insentif')
                ->after('status')->comment('insentif = dibayar, pelayanan = tidak dibayar');
            $table->boolean('is_pendeta_sion')->default(false)->after('kategori_petugas')
                ->comment('true = pendeta gereja sion (tidak bayar insentif)');
        });

        // 3. Tambah foto TTD + jumlah hadir yang bisa diedit di jadwal_ibadah
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->string('foto_ttd_path', 500)->nullable()->after('status')
                ->comment('Path foto tanda tangan petugas untuk verifikasi');
        });

        // 4. Tambah is_pendeta_luar + custom insentif di jadwal_petugas
        Schema::table('jadwal_petugas', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('is_override')
                ->comment('true = petugas luar (pendeta/pianis luar)');
            $table->string('nama_external', 200)->nullable()->after('is_external')
                ->comment('Nama petugas luar jika is_external=true');
            $table->enum('kategori', ['insentif', 'pelayanan'])->default('insentif')
                ->after('nama_external');
        });

        // 5. Config biaya insentif per tipe event
        Schema::create('insentif_config', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_event', 50)->comment('ibadah_biasa, perayaan, pernikahan');
            $table->string('jenis_tugas', 100)->comment('Pendeta Pelayanan, Organis, Song Leader, dll');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Seed default insentif config
        $configs = [
            // Ibadah Biasa
            ['ibadah_biasa', 'Pendeta Pelayanan (Luar)', 250000],
            ['ibadah_biasa', 'Organis', 50000],
            ['ibadah_biasa', 'Song Leader', 50000],
            ['ibadah_biasa', 'Operator Slide', 75000],
            ['ibadah_biasa', 'Multimedia', 150000],
            ['ibadah_biasa', 'Pianis (Luar)', 100000],
            // Perayaan
            ['perayaan', 'Pendeta Pelayanan (Luar)', 350000],
            ['perayaan', 'Organis', 75000],
            ['perayaan', 'Song Leader', 75000],
            ['perayaan', 'Operator Slide', 100000],
            ['perayaan', 'Multimedia', 200000],
            ['perayaan', 'Pianis (Luar)', 150000],
            // Pernikahan (auto-fill dari biaya nikah, tapi default juga disediakan)
            ['pernikahan', 'Pendeta Pelayanan (Luar)', 500000],
            ['pernikahan', 'Organis', 100000],
            ['pernikahan', 'Song Leader', 100000],
            ['pernikahan', 'Operator Slide', 100000],
            ['pernikahan', 'Multimedia', 250000],
        ];

        $now = now();
        foreach ($configs as $c) {
            \DB::table('insentif_config')->insert([
                'tipe_event' => $c[0],
                'jenis_tugas' => $c[1],
                'nominal' => $c[2],
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insentif_config');

        Schema::table('jadwal_petugas', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'nama_external', 'kategori']);
        });
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->dropColumn('foto_ttd_path');
        });
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropColumn(['kategori_petugas', 'is_pendeta_sion']);
        });
        Schema::table('warga_jemaat', function (Blueprint $table) {
            $table->dropColumn('jabatan_gereja');
        });
    }
};
