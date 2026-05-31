<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLegacyData extends Command
{
    protected $signature = 'migrate:legacy {--dry-run : Preview without inserting}';
    protected $description = 'Migrate data from old db_gereja to new db_gereja_v2';

    private $oldDb;
    private int $migrated = 0;
    private int $skipped = 0;

    public function handle()
    {
        $dry = $this->option('dry-run');
        $this->info($dry ? '🔍 DRY RUN MODE — no data will be written' : '🚀 Starting legacy data migration...');
        $this->newLine();

        // Connect to old database
        $this->oldDb = new \mysqli('localhost', 'root', '', 'db_gereja');
        if ($this->oldDb->connect_error) {
            $this->error("Cannot connect to db_gereja: " . $this->oldDb->connect_error);
            return 1;
        }
        $this->oldDb->set_charset('utf8mb4');

        $this->migrateWargaJemaat($dry);
        $this->migratePetugas($dry);
        $this->migrateJadwalIbadah($dry);
        $this->migrateJadwalPetugas($dry);
        $this->migrateJadwalKantong($dry);
        $this->migrateTransaksi($dry);
        $this->migratePendaftaran($dry);
        $this->migratePersidangan($dry);
        $this->migratePedoman($dry);

        $this->oldDb->close();
        $this->newLine();
        $this->info("✅ Migration complete. Migrated: {$this->migrated} | Skipped: {$this->skipped}");
        return 0;
    }

    private function query(string $sql)
    {
        return $this->oldDb->query($sql);
    }

    private function migrateWargaJemaat(bool $dry)
    {
        $this->info('📋 Migrating warga_jemaat...');
        $res = $this->query("SELECT * FROM warga_jemaat ORDER BY id_warga");
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('warga_jemaat')->where('id_warga', $row['id_warga'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $data = [
                'id_warga' => $row['id_warga'],
                'nama_lengkap' => $row['nama_lengkap'],
                'no_kk' => $row['no_kk'],
                'lingkungan' => $row['lingkungan'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'tempat_lahir' => $row['tempat_lahir'],
                'tanggal_lahir' => $row['tanggal_lahir'],
                'alamat' => $row['alamat'],
                'no_telepon' => $row['no_telepon'],
                'email' => $row['email'],
                'pekerjaan' => $row['pekerjaan'],
                'status_pernikahan' => $row['status_pernikahan'],
                'status_sidi' => $row['status_sidi'],
                'status_jemaat' => strtolower($row['status_jemaat'] ?? 'aktif'),
                'tanggal_bergabung' => $row['tanggal_bergabung'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];

            if (!$dry) DB::table('warga_jemaat')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} warga jemaat");
    }

    private function migratePetugas(bool $dry)
    {
        $this->info('📋 Migrating petugas...');
        $res = $this->query("SELECT * FROM petugas ORDER BY id_petugas");
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('petugas')->where('id_petugas', $row['id_petugas'])->exists();
            if ($exists) { $this->skipped++; continue; }

            // Map old kode_akun string to new kode_akun ID
            $kodeAkunId = null;
            if (!empty($row['kode_akun'])) {
                $kodeAkunId = DB::table('kode_akun')->where('kode', $row['kode_akun'])->value('id');
            }

            $data = [
                'id_petugas' => $row['id_petugas'],
                'warga_id' => $row['warga_id'],
                'nama_petugas' => $row['nama_petugas'],
                'jenis_tugas' => $row['jenis_tugas'],
                'kode_akun_id' => $kodeAkunId,
                'no_telepon' => $row['no_telepon'],
                'insentif' => $row['insentif'],
                'status' => $row['status'] === 'active' ? 'active' : 'inactive',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];

            if (!$dry) DB::table('petugas')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} petugas");
    }

    private function migrateJadwalIbadah(bool $dry)
    {
        $this->info('📋 Migrating jadwal_ibadah...');
        $res = $this->query("SELECT * FROM jadwal_ibadah ORDER BY id_jadwal");
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('jadwal_ibadah')->where('id_jadwal', $row['id_jadwal'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $data = [
                'id_jadwal' => $row['id_jadwal'],
                'tanggal' => $row['tanggal'],
                'jenis_ibadah' => $row['jenis_ibadah'],
                'waktu' => $row['waktu'],
                'keterangan' => $row['keterangan'],
                'lokasi' => $row['lokasi'],
                'jumlah_hadir' => $row['jumlah_hadir'] ?? 0,
                'status' => $row['status'] ?? 'scheduled',
                'created_at' => $row['created_at'],
            ];

            if (!$dry) DB::table('jadwal_ibadah')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} jadwal ibadah");
    }

    private function migrateJadwalPetugas(bool $dry)
    {
        $this->info('📋 Migrating jadwal_petugas...');
        $res = $this->query("SELECT * FROM jadwal_petugas ORDER BY id");
        if (!$res) { $this->line("   → skipped (table not found)"); return; }
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('jadwal_petugas')->where('id', $row['id'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $data = [
                'id' => $row['id'],
                'jadwal_id' => $row['jadwal_id'],
                'petugas_id' => $row['petugas_id'],
                'jenis_tugas' => $row['jenis_tugas'] ?? '',
                'nominal_insentif' => $row['nominal_insentif'] ?? 0,
                'created_at' => $row['created_at'] ?? now(),
            ];

            if (!$dry) DB::table('jadwal_petugas')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} jadwal petugas");
    }

    private function migrateJadwalKantong(bool $dry)
    {
        $this->info('📋 Migrating jadwal_kantong...');
        $res = $this->query("SELECT * FROM jadwal_kantong ORDER BY id");
        if (!$res) { $this->line("   → skipped (table not found)"); return; }
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('jadwal_kantong')->where('id', $row['id'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $kodeAkunId = null;
            if (!empty($row['kode_akun_id'])) {
                $kodeAkunId = $row['kode_akun_id'];
            } elseif (!empty($row['kode_akun'])) {
                $kodeAkunId = DB::table('kode_akun')->where('kode', $row['kode_akun'])->value('id');
            }

            if (!$kodeAkunId) {
                $this->warn("   ⚠ Kantong #{$row['id']} skipped — kode_akun not resolved");
                $this->skipped++; continue;
            }

            $data = [
                'id' => $row['id'],
                'jadwal_id' => $row['jadwal_id'],
                'kode_akun_id' => $kodeAkunId,
                'label' => $row['label'] ?? '',
                'total' => $row['total'] ?? 0,
                'created_at' => $row['created_at'] ?? now(),
            ];

            if (!$dry) DB::table('jadwal_kantong')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} jadwal kantong");
    }

    private function migrateTransaksi(bool $dry)
    {
        $this->info('📋 Migrating transaksi...');
        $res = $this->query("SELECT * FROM transaksi ORDER BY id_transaksi");
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            // Map old kode_akun string to new kode_akun ID
            $kodeAkunId = null;
            if (!empty($row['kode_akun'])) {
                $kodeAkunId = DB::table('kode_akun')->where('kode', $row['kode_akun'])->value('id');
            }
            if (!$kodeAkunId) {
                $this->warn("   ⚠ Transaksi #{$row['id_transaksi']} skipped — kode_akun '{$row['kode_akun']}' not found");
                $this->skipped++; continue;
            }

            // Map jenis to tipe
            $tipe = $row['jenis'] === 'pemasukan' ? 'penerimaan' : 'pengeluaran';

            // Detect kantong_dana from kode_akun prefix
            $akun = DB::table('kode_akun')->where('id', $kodeAkunId)->first();
            $kantong = 'rutin'; // default
            if ($akun) {
                $kantong = match(true) {
                    str_starts_with($akun->kode, '1.1.') || str_starts_with($akun->kode, '2.1.') => 'rutin',
                    str_starts_with($akun->kode, '1.2.') || str_starts_with($akun->kode, '2.2.') => 'phbg',
                    str_starts_with($akun->kode, '1.3.') || str_starts_with($akun->kode, '2.3.') => 'pembangunan',
                    str_starts_with($akun->kode, '1.4.') || str_starts_with($akun->kode, '2.4.') => 'cadangan',
                    str_starts_with($akun->kode, '1.5.') || str_starts_with($akun->kode, '2.5.') => 'diakonia_sosial',
                    str_starts_with($akun->kode, '1.6.') || str_starts_with($akun->kode, '2.6.') => 'diakonia_kedukaan',
                    default => 'rutin',
                };
            }

            $tanggal = $row['tanggal'];
            $data = [
                'kode_akun_id' => $kodeAkunId,
                'tanggal' => $tanggal,
                'bulan' => date('n', strtotime($tanggal)),
                'tahun' => date('Y', strtotime($tanggal)),
                'tipe' => $tipe,
                'kantong_dana' => $kantong,
                'jumlah' => abs($row['jumlah']),
                'keterangan' => $row['keterangan'],
                'bukti' => $row['bukti'],
                'dibuat_oleh' => $row['created_by'] ?? 1,
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];

            if (!$dry) DB::table('transaksi')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} transaksi");
    }

    private function migratePendaftaran(bool $dry)
    {
        $this->info('📋 Migrating pendaftaran...');
        $res = $this->query("SELECT * FROM pendaftaran ORDER BY id_pendaftaran");
        $count = 0;

        $jenisMap = ['Warga Jemaat' => 'warga', 'Sidi' => 'sidi', 'Pernikahan' => 'pernikahan', 'Baptis' => 'baptis', 'Pelayanan' => 'pelayanan'];
        $statusMap = ['Pending' => 'pending', 'Disetujui' => 'terdaftar', 'Ditolak' => 'ditolak'];

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('pendaftaran')->where('id_pendaftaran', $row['id_pendaftaran'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $data = [
                'id_pendaftaran' => $row['id_pendaftaran'],
                'is_jemaat' => $row['is_jemaat'],
                'warga_id' => $row['warga_id'],
                'pasangan_id' => $row['pasangan_id'],
                'nama_pasangan' => $row['nama_pasangan'],
                'jenis' => $jenisMap[$row['jenis']] ?? 'warga',
                'nama_lengkap' => $row['nama_lengkap'],
                'no_kk' => $row['no_kk'],
                'lingkungan' => $row['lingkungan'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'tempat_lahir' => $row['tempat_lahir'],
                'tanggal_lahir' => $row['tanggal_lahir'],
                'alamat' => $row['alamat'],
                'no_telepon' => $row['no_telepon'],
                'email' => $row['email'],
                'pekerjaan' => $row['pekerjaan'],
                'status_sidi' => $row['status_sidi'],
                'status_pernikahan' => $row['status_pernikahan'],
                'tanggal_bergabung' => $row['tanggal_bergabung'],
                'nama_ayah' => $row['nama_ayah'],
                'nama_ibu' => $row['nama_ibu'],
                'tanggal_daftar' => $row['tanggal_daftar'],
                'keterangan' => $row['keterangan'],
                'biaya' => is_numeric($row['biaya']) ? $row['biaya'] : 0,
                'status' => $statusMap[$row['status']] ?? 'pending',
                'created_by' => $row['created_by'] ?? 1,
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];

            if (!$dry) DB::table('pendaftaran')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} pendaftaran");
    }

    private function migratePersidangan(bool $dry)
    {
        $this->info('📋 Migrating bahan_persidangan...');
        $res = $this->query("SELECT * FROM bahan_persidangan ORDER BY id_dokumen");
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('bahan_persidangan')->where('id_dokumen', $row['id_dokumen'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $data = [
                'id_dokumen' => $row['id_dokumen'],
                'judul' => $row['judul'],
                'kategori' => $row['kategori'] ?? null,
                'tanggal_sidang' => $row['tanggal_sidang'] ?? null,
                'deskripsi' => $row['deskripsi'] ?? null,
                'file_path' => $row['file_path'] ?? null,
                'file_name' => $row['file_name'] ?? null,
                'uploaded_by' => $row['uploaded_by'] ?? 1,
                'created_at' => $row['created_at'],
            ];

            if (!$dry) DB::table('bahan_persidangan')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} bahan persidangan");
    }

    private function migratePedoman(bool $dry)
    {
        $this->info('📋 Migrating pedoman...');
        $res = $this->query("SELECT * FROM pedoman ORDER BY id_pedoman");
        if (!$res) { $this->line("   → skipped"); return; }
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            $exists = DB::table('pedoman')->where('id_pedoman', $row['id_pedoman'])->exists();
            if ($exists) { $this->skipped++; continue; }

            $data = [
                'id_pedoman' => $row['id_pedoman'],
                'judul' => $row['judul'],
                'kategori' => $row['kategori'] ?? null,
                'tahun' => $row['tahun'] ?? null,
                'versi' => $row['versi'] ?? null,
                'deskripsi' => $row['deskripsi'] ?? null,
                'file_path' => $row['file_path'] ?? null,
                'file_name' => $row['file_name'] ?? null,
                'uploaded_by' => $row['uploaded_by'] ?? 1,
                'created_at' => $row['created_at'],
            ];

            if (!$dry) DB::table('pedoman')->insert($data);
            $count++; $this->migrated++;
        }
        $this->line("   → {$count} pedoman");
    }
}
