<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── A. Fix finalized jadwal transactions: draft → verified ──
        DB::table('transaksi')
            ->where('status', 'draft')
            ->where('keterangan', 'like', 'Kantong:%')
            ->update(['status' => 'verified']);
        DB::table('transaksi')
            ->where('status', 'draft')
            ->where('keterangan', 'like', 'Amplop persembahan%')
            ->update(['status' => 'verified']);
        DB::table('transaksi')
            ->where('status', 'draft')
            ->where('keterangan', 'like', 'Insentif petugas%')
            ->update(['status' => 'verified']);

        // ── B. Mark existing kolekte entries & add KKM Maju Kedepan ──
        // Set sumber_lapangan='kolekte' on existing kolekte entries
        DB::table('kode_akun')
            ->where('tipe', 'penerimaan')
            ->where('kode', 'LIKE', 'I(APn).%.01')
            ->update(['sumber_lapangan' => 'kolekte']);

        // New KKM Maju Kedepan + Perayaan entries
        $newKolekte = [
            // MPH (UP 01) — Maju Kedepan
            ['kode' => 'I(APn).01.15', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan Ibadah Minggu', 'keterangan' => 'KKM Maju Kedepan Ibadah Minggu', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            // Perayaan
            ['kode' => 'I(APn).01.16', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 16, 'nama' => 'KK Perayaan Natal', 'keterangan' => 'KK Perayaan Natal', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.17', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 17, 'nama' => 'KKM Maju Kedepan Perayaan Natal', 'keterangan' => 'KKM Maju Kedepan Perayaan Natal', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.18', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 18, 'nama' => 'KK Perayaan Paskah', 'keterangan' => 'KK Perayaan Paskah', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.19', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 19, 'nama' => 'KKM Maju Kedepan Perayaan Paskah', 'keterangan' => 'KKM Maju Kedepan Perayaan Paskah', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.20', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 20, 'nama' => 'KK Perayaan Kenaikan Yesus Kristus', 'keterangan' => 'KK Perayaan Kenaikan Yesus Kristus', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.21', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 21, 'nama' => 'KKM Maju Kedepan Perayaan Kenaikan Yesus', 'keterangan' => 'KKM Maju Kedepan Perayaan Kenaikan Yesus', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.22', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 22, 'nama' => 'KK Perayaan Pentakosta', 'keterangan' => 'KK Perayaan Pentakosta', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.23', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 23, 'nama' => 'KKM Maju Kedepan Perayaan Pentakosta', 'keterangan' => 'KKM Maju Kedepan Perayaan Pentakosta', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.24', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 24, 'nama' => 'KK Perayaan Tahun Baru', 'keterangan' => 'KK Perayaan Tahun Baru', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.25', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 25, 'nama' => 'KKM Maju Kedepan Perayaan Tahun Baru', 'keterangan' => 'KKM Maju Kedepan Perayaan Tahun Baru', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.26', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 26, 'nama' => 'KK Perayaan HUT Gereja', 'keterangan' => 'KK Perayaan HUT Gereja', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.27', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 27, 'nama' => 'KKM Maju Kedepan Perayaan HUT Gereja', 'keterangan' => 'KKM Maju Kedepan Perayaan HUT Gereja', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.28', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 28, 'nama' => 'KK Ibadah Syukuran', 'keterangan' => 'KK Ibadah Syukuran', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.29', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 29, 'nama' => 'KKM Maju Kedepan Ibadah Syukuran', 'keterangan' => 'KKM Maju Kedepan Ibadah Syukuran', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.30', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 30, 'nama' => 'KK Ibadah Tutup Tahun', 'keterangan' => 'KK Ibadah Tutup Tahun', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).01.31', 'tipe' => 'penerimaan', 'nomor_up' => 1, 'nomor_menu' => 31, 'nama' => 'KKM Maju Kedepan Ibadah Tutup Tahun', 'keterangan' => 'KKM Maju Kedepan Ibadah Tutup Tahun', 'sumber_lapangan' => 'kolekte', 'aktif' => true],

            // KKM Maju Kedepan per Kategorial UP
            ['kode' => 'I(APn).02.15', 'tipe' => 'penerimaan', 'nomor_up' => 2, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan SPA', 'keterangan' => 'KKM Maju Kedepan Ibadah SPA', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).03.15', 'tipe' => 'penerimaan', 'nomor_up' => 3, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan SPRP', 'keterangan' => 'KKM Maju Kedepan Ibadah SPRP', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).04.15', 'tipe' => 'penerimaan', 'nomor_up' => 4, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan SPPer', 'keterangan' => 'KKM Maju Kedepan Ibadah SPPer', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).05.15', 'tipe' => 'penerimaan', 'nomor_up' => 5, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan SPB', 'keterangan' => 'KKM Maju Kedepan Ibadah SPB', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).06.15', 'tipe' => 'penerimaan', 'nomor_up' => 6, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan SPUI', 'keterangan' => 'KKM Maju Kedepan Ibadah SPUI', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
            ['kode' => 'I(APn).07.15', 'tipe' => 'penerimaan', 'nomor_up' => 7, 'nomor_menu' => 15, 'nama' => 'KKM Maju Kedepan KPI/PKB', 'keterangan' => 'KKM Maju Kedepan Ibadah KPI/PKB', 'sumber_lapangan' => 'kolekte', 'aktif' => true],
        ];

        foreach ($newKolekte as $k) {
            DB::table('kode_akun')->updateOrInsert(['kode' => $k['kode']], $k);
        }

        // ── E. Ulang tahun CMS toggle ──
        DB::table('cms_settings')->updateOrInsert(
            ['key' => 'section_ultah'],
            ['value' => '1', 'updated_at' => now()]
        );

        // ── F. Add judul_ibadah to jadwal_ibadah ──
        if (!Schema::hasColumn('jadwal_ibadah', 'judul_ibadah')) {
            Schema::table('jadwal_ibadah', function (Blueprint $table) {
                $table->string('judul_ibadah', 300)->nullable()->after('jenis_ibadah');
            });
        }
    }

    public function down(): void
    {
        Schema::table('jadwal_ibadah', function (Blueprint $table) {
            $table->dropColumn('judul_ibadah');
        });
        DB::table('cms_settings')->where('key', 'section_ultah')->delete();
        DB::table('kode_akun')->where('sumber_lapangan', 'kolekte')
            ->where('nomor_menu', '>=', 15)->delete();
    }
};
