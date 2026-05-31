<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── LINGKUNGAN SION ────────────────────────────
        Schema::create('lingkungan_sion', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50); // Sion 1, Sion 2, Sion 3
            $table->foreignId('ketua_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bendahara_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sekretaris_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('wilayah_deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // ─── KELUARGA ──────────────────────────────────
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 30)->nullable();
            $table->string('nama_kepala_keluarga');
            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota', 100)->nullable();
            $table->foreignId('lingkungan_sion_id')->nullable()->constrained('lingkungan_sion')->nullOnDelete();
            $table->string('no_telepon', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // ─── ALTER WARGA_JEMAAT ─────────────────────────
        Schema::table('warga_jemaat', function (Blueprint $table) {
            $table->foreignId('keluarga_id')->nullable()->after('id_warga')->constrained('keluarga')->nullOnDelete();
            $table->string('hubungan_keluarga', 30)->nullable()->after('keluarga_id'); // KK/Istri/Anak/Lainnya
            $table->foreignId('lingkungan_sion_id')->nullable()->after('lingkungan')->constrained('lingkungan_sion')->nullOnDelete();
            $table->string('golongan_darah', 5)->nullable()->after('tanggal_lahir');
            $table->string('agama_sebelum', 50)->nullable()->after('pekerjaan');
            $table->enum('status_baptis', ['Sudah Baptis', 'Belum Baptis'])->default('Belum Baptis')->after('status_sidi');
            $table->date('tanggal_baptis')->nullable()->after('status_baptis');
            $table->date('tanggal_sidi')->nullable()->after('tanggal_baptis');
            $table->string('nama_ayah')->nullable()->after('nama_pasangan');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
        });

        // ─── PENANDATANGAN DOKUMEN ──────────────────────
        Schema::create('penandatangan_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('module', 30); // baptis, nikah, surat
            $table->tinyInteger('urutan')->default(1);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_tampilan');
            $table->string('jabatan_tampilan');
            $table->string('foto_ttd')->nullable(); // path to signature image
            $table->boolean('qr_enabled')->default(true);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // ─── PENDAFTARAN BAPTIS ─────────────────────────
        Schema::create('pendaftaran_baptis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id')->nullable();
            // Data calon baptis
            $table->string('nama_lengkap');
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->foreignId('lingkungan_sion_id')->nullable()->constrained('lingkungan_sion')->nullOnDelete();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('agama_sebelum', 50)->nullable();
            // Katekisasi
            $table->date('katekisasi_mulai')->nullable();
            $table->date('katekisasi_selesai')->nullable();
            $table->decimal('nilai_ujian', 5, 2)->nullable();
            // Workflow
            $table->enum('status', ['pending', 'terdaftar', 'lulus_katekisasi', 'dijadwalkan', 'selesai', 'ditolak'])->default('pending');
            $table->date('tanggal_baptis_rencana')->nullable();
            $table->unsignedBigInteger('jadwal_ibadah_id')->nullable();
            $table->text('catatan')->nullable();
            $table->text('catatan_penolakan')->nullable();
            // Approval
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_at')->nullable();
            // File backup
            $table->unsignedBigInteger('file_manager_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('warga_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('jadwal_ibadah_id')->references('id_jadwal')->on('jadwal_ibadah')->nullOnDelete();
            $table->foreign('file_manager_id')->references('id')->on('file_manager')->nullOnDelete();
        });

        // ─── PENDAFTARAN NIKAH ──────────────────────────
        Schema::create('pendaftaran_nikah', function (Blueprint $table) {
            $table->id();
            // Mempelai Pria
            $table->unsignedBigInteger('pria_warga_id')->nullable();
            $table->string('pria_nama');
            $table->string('pria_tempat_lahir', 100)->nullable();
            $table->date('pria_tanggal_lahir')->nullable();
            $table->text('pria_alamat')->nullable();
            $table->string('pria_pekerjaan', 100)->nullable();
            $table->string('pria_agama', 50)->nullable();
            $table->string('pria_status_sidi', 30)->nullable();
            $table->string('pria_nama_ayah')->nullable();
            $table->string('pria_nama_ibu')->nullable();
            $table->string('pria_no_telepon', 20)->nullable();
            // Mempelai Wanita
            $table->unsignedBigInteger('wanita_warga_id')->nullable();
            $table->string('wanita_nama');
            $table->string('wanita_tempat_lahir', 100)->nullable();
            $table->date('wanita_tanggal_lahir')->nullable();
            $table->text('wanita_alamat')->nullable();
            $table->string('wanita_pekerjaan', 100)->nullable();
            $table->string('wanita_agama', 50)->nullable();
            $table->string('wanita_status_sidi', 30)->nullable();
            $table->string('wanita_nama_ayah')->nullable();
            $table->string('wanita_nama_ibu')->nullable();
            $table->string('wanita_no_telepon', 20)->nullable();
            // Administrasi
            $table->decimal('biaya_administrasi', 15, 2)->default(0);
            $table->foreignId('kode_akun_admin_id')->nullable()->constrained('kode_akun');
            $table->decimal('biaya_fasilitas', 15, 2)->default(0);
            $table->foreignId('kode_akun_fasilitas_id')->nullable()->constrained('kode_akun');
            // Jadwal
            $table->date('tanggal_nikah_rencana')->nullable();
            $table->unsignedBigInteger('jadwal_ibadah_id')->nullable();
            // Workflow
            $table->enum('status', ['pending', 'terdaftar', 'dijadwalkan', 'divalidasi', 'selesai', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('divalidasi_at')->nullable();
            // File Manager
            $table->unsignedBigInteger('folder_lampiran_id')->nullable();
            $table->unsignedBigInteger('file_manager_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('pria_warga_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('wanita_warga_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('jadwal_ibadah_id')->references('id_jadwal')->on('jadwal_ibadah')->nullOnDelete();
            $table->foreign('folder_lampiran_id')->references('id')->on('file_manager')->nullOnDelete();
            $table->foreign('file_manager_id')->references('id')->on('file_manager')->nullOnDelete();
        });

        // ─── NIKAH INSENTIF (dynamic rows) ──────────────
        Schema::create('pendaftaran_nikah_insentif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_nikah_id')->constrained('pendaftaran_nikah')->cascadeOnDelete();
            $table->string('jenis_insentif', 100); // Pianis, LCD, Sound, Kebersihan, Pendeta, Song Leader, etc
            $table->decimal('nominal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── NIKAH LAMPIRAN ────────────────────────────
        Schema::create('pendaftaran_nikah_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_nikah_id')->constrained('pendaftaran_nikah')->cascadeOnDelete();
            $table->string('jenis_lampiran', 50); // KTP, KK, Surat_Baptis, Foto, Hal2, Hal3, Lainnya
            $table->unsignedBigInteger('file_manager_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('file_manager_id')->references('id')->on('file_manager')->nullOnDelete();
        });

        // ─── ALTER JADWAL_IBADAH ────────────────────────
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->enum('kategori', [
                'ibadah_rutin', 'ibadah_khusus', 'pernikahan', 'pemakaman',
                'acara_keluarga', 'ulang_tahun', 'acara_gereja', 'baptis', 'lainnya'
            ])->default('ibadah_rutin')->after('jenis_ibadah');
            $table->enum('lokasi_tipe', ['gereja', 'rumah_jemaat', 'gedung_lain'])->default('gereja')->after('lokasi');
            $table->text('lokasi_alamat')->nullable()->after('lokasi_tipe');
            $table->unsignedBigInteger('warga_terkait_id')->nullable()->after('lokasi_alamat');
            $table->unsignedBigInteger('pendaftaran_nikah_id')->nullable()->after('warga_terkait_id');
            $table->unsignedBigInteger('pendaftaran_baptis_id')->nullable()->after('pendaftaran_nikah_id');
            $table->string('penanggung_jawab')->nullable()->after('pendaftaran_baptis_id');
            $table->string('contact_person')->nullable()->after('penanggung_jawab');
            $table->string('no_telepon_cp', 20)->nullable()->after('contact_person');
            $table->time('jam_selesai')->nullable()->after('waktu');
            $table->string('dress_code', 100)->nullable()->after('jumlah_hadir');
            $table->string('tema_ibadah')->nullable()->after('dress_code');

            $table->foreign('warga_terkait_id')->references('id_warga')->on('warga_jemaat')->nullOnDelete();
            $table->foreign('pendaftaran_nikah_id')->references('id')->on('pendaftaran_nikah')->nullOnDelete();
            $table->foreign('pendaftaran_baptis_id')->references('id')->on('pendaftaran_baptis')->nullOnDelete();
        });

        // ─── ALTER JADWAL_PETUGAS ───────────────────────
        Schema::table('jadwal_petugas', function (Blueprint $table) {
            $table->string('kategori_insentif', 50)->default('pelayanan')->after('nominal_insentif');
            $table->enum('status_bayar', ['belum', 'sudah'])->default('belum')->after('kategori_insentif');
            $table->date('tanggal_bayar')->nullable()->after('status_bayar');
            $table->string('sumber', 30)->default('rutin')->after('tanggal_bayar');
        });

        // ─── BIAYA NIKAH SETTINGS ───────────────────────
        // Insert default settings for nikah costs
        \Illuminate\Support\Facades\DB::table('system_settings')->insert([
            ['setting_key' => 'nikah_biaya_administrasi', 'setting_value' => '500000'],
            ['setting_key' => 'nikah_biaya_fasilitas', 'setting_value' => '300000'],
            ['setting_key' => 'nikah_insentif_pianis', 'setting_value' => '200000'],
            ['setting_key' => 'nikah_insentif_lcd', 'setting_value' => '100000'],
            ['setting_key' => 'nikah_insentif_sound', 'setting_value' => '150000'],
            ['setting_key' => 'nikah_insentif_kebersihan', 'setting_value' => '100000'],
        ]);
    }

    public function down(): void
    {
        Schema::table('jadwal_petugas', function (Blueprint $table) {
            $table->dropColumn(['kategori_insentif', 'status_bayar', 'tanggal_bayar', 'sumber']);
        });
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->dropForeign(['warga_terkait_id']);
            $table->dropForeign(['pendaftaran_nikah_id']);
            $table->dropForeign(['pendaftaran_baptis_id']);
            $table->dropColumn([
                'kategori', 'lokasi_tipe', 'lokasi_alamat', 'warga_terkait_id',
                'pendaftaran_nikah_id', 'pendaftaran_baptis_id',
                'penanggung_jawab', 'contact_person', 'no_telepon_cp',
                'jam_selesai', 'dress_code', 'tema_ibadah'
            ]);
        });
        Schema::dropIfExists('pendaftaran_nikah_lampiran');
        Schema::dropIfExists('pendaftaran_nikah_insentif');
        Schema::dropIfExists('pendaftaran_nikah');
        Schema::dropIfExists('pendaftaran_baptis');
        Schema::dropIfExists('penandatangan_dokumen');
        Schema::table('warga_jemaat', function (Blueprint $table) {
            $table->dropForeign(['keluarga_id']);
            $table->dropForeign(['lingkungan_sion_id']);
            $table->dropColumn([
                'keluarga_id', 'hubungan_keluarga', 'lingkungan_sion_id',
                'golongan_darah', 'agama_sebelum', 'status_baptis',
                'tanggal_baptis', 'tanggal_sidi', 'nama_ayah', 'nama_ibu'
            ]);
        });
        Schema::dropIfExists('keluarga');
        Schema::dropIfExists('lingkungan_sion');

        \Illuminate\Support\Facades\DB::table('system_settings')
            ->where('setting_key', 'like', 'nikah_%')->delete();
    }
};
