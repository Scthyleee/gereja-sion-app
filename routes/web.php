<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JemaatController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PersidanganController;
use App\Http\Controllers\PedomanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\InputKategorialController;
use App\Http\Controllers\WartaController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\PendaftaranBaptisController;
use App\Http\Controllers\PendaftaranNikahController;
use App\Http\Controllers\LingkunganSionController;
use App\Http\Controllers\TransaksiPSController;
use App\Http\Controllers\TransaksiKKController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CmsController;

// --- Auth ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Public CMS Page (no login required) ---
Route::get('/', [PublicController::class, 'index'])->name('public.home');
Route::get('/warta/{id}/download', [WartaController::class, 'print'])->name('warta.public.download');

// --- Admin Panel (protected, prefix /admin) ---
Route::middleware(['auth', 'page.access'])->prefix('admin')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Keuangan
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::get('/keuangan/master-akun', [KeuanganController::class, 'masterAkun'])->name('keuangan.master');
    Route::get('/keuangan/transaksi', [KeuanganController::class, 'transaksi'])->name('keuangan.transaksi');
    Route::post('/keuangan/transaksi', [KeuanganController::class, 'storeTransaksi'])->name('keuangan.transaksi.store');
    Route::delete('/keuangan/transaksi/{transaksi}', [KeuanganController::class, 'destroyTransaksi'])->name('keuangan.transaksi.destroy');
    Route::post('/keuangan/transaksi/{transaksi}/verify', [KeuanganController::class, 'verifyTransaksi'])->name('keuangan.transaksi.verify');
    Route::post('/keuangan/transaksi/{transaksi}/approve', [KeuanganController::class, 'approveTransaksi'])->name('keuangan.transaksi.approve');
    Route::get('/keuangan/saldo', [KeuanganController::class, 'saldo'])->name('keuangan.saldo');
    Route::post('/keuangan/saldo', [KeuanganController::class, 'updateSaldo'])->name('keuangan.saldo.update');

    // === PERSEMBAHAN SYUKUR (PS) ===
    Route::get('/ps', [TransaksiPSController::class, 'index'])->name('ps.index');
    Route::get('/ps/create', [TransaksiPSController::class, 'create'])->name('ps.create');
    Route::post('/ps', [TransaksiPSController::class, 'store'])->name('ps.store');
    Route::put('/ps/{id}/verify', [TransaksiPSController::class, 'verify'])->name('ps.verify');
    Route::delete('/ps/{id}', [TransaksiPSController::class, 'destroy'])->name('ps.destroy');
    Route::get('/ps/search-jemaat', [TransaksiPSController::class, 'searchJemaat'])->name('ps.search-jemaat');

    // === KANTONG KOLEKTE (KK) ===
    Route::get('/kk', [TransaksiKKController::class, 'index'])->name('kk.index');
    Route::post('/kk', [TransaksiKKController::class, 'store'])->name('kk.store');
    Route::put('/kk/{id}/verify', [TransaksiKKController::class, 'verify'])->name('kk.verify');
    Route::delete('/kk/{id}', [TransaksiKKController::class, 'destroy'])->name('kk.destroy');

    // Jadwal & Pelayanan
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/{jadwal}', [JadwalController::class, 'show'])->name('jadwal.show');
    Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::post('/jadwal/{jadwal}/petugas', [JadwalController::class, 'storePetugas'])->name('jadwal.petugas.store');
    Route::post('/jadwal/{jadwal}/petugas-external', [JadwalController::class, 'storePetugasExternal'])->name('jadwal.petugas-external.store');
    Route::post('/jadwal/{jadwal}/petugas-pelayanan', [JadwalController::class, 'storePetugasPelayanan'])->name('jadwal.petugas-pelayanan.store');
    Route::post('/jadwal/{jadwal}/petugas-random', [JadwalController::class, 'randomPetugas'])->name('jadwal.petugas-random');
    Route::delete('/jadwal/petugas/{jp}', [JadwalController::class, 'destroyPetugas'])->name('jadwal.petugas.destroy');
    Route::post('/jadwal/{jadwal}/kantong', [JadwalController::class, 'storeKantong'])->name('jadwal.kantong.store');
    Route::delete('/jadwal/kantong/{kantong}', [JadwalController::class, 'destroyKantong'])->name('jadwal.kantong.destroy');
    Route::post('/jadwal/{jadwal}/amplop', [JadwalController::class, 'storeAmplop'])->name('jadwal.amplop.store');
    Route::delete('/jadwal/amplop/{amplop}', [JadwalController::class, 'destroyAmplop'])->name('jadwal.amplop.destroy');
    Route::post('/jadwal/{jadwal}/finalize', [JadwalController::class, 'finalize'])->name('jadwal.finalize');
    Route::post('/jadwal/{jadwal}/unfinalize', [JadwalController::class, 'unfinalize'])->name('jadwal.unfinalize');
    Route::put('/jadwal/{jadwal}/hadir', [JadwalController::class, 'updateHadir'])->name('jadwal.hadir.update');
    Route::post('/jadwal/{jadwal}/upload-ttd', [JadwalController::class, 'uploadTtd'])->name('jadwal.upload-ttd');
    Route::post('/jadwal/jenis-ibadah', [JadwalController::class, 'storeJenisIbadah'])->name('jadwal.jenis-ibadah.store');
    Route::get('/jadwal-petugas', [JadwalController::class, 'petugasMaster'])->name('petugas.index');
    Route::post('/jadwal-petugas', [JadwalController::class, 'storePetugasMaster'])->name('petugas.store');
    Route::put('/jadwal-petugas/{petugas}', [JadwalController::class, 'updatePetugasMaster'])->name('petugas.update');
    Route::delete('/jadwal-petugas/{petugas}', [JadwalController::class, 'destroyPetugasMaster'])->name('petugas.destroy');
    Route::post('/jadwal-petugas/{petugas}/role', [JadwalController::class, 'addPetugasRole'])->name('petugas.role.add');
    Route::delete('/petugas-role/{role}', [JadwalController::class, 'deletePetugasRole'])->name('petugas.role.delete');
    Route::delete('/jadwal-petugas/{petugas}/role-delete-by-name', [JadwalController::class, 'deleteRoleByName'])->name('petugas.role.deleteByName');
    Route::get('/jadwal-insentif', [JadwalController::class, 'insentif'])->name('insentif.index');
    Route::get('/insentif-setting', [JadwalController::class, 'insentifSetting'])->name('insentif.setting');
    Route::post('/insentif-setting', [JadwalController::class, 'storeInsentifConfig'])->name('insentif.config.store');
    Route::put('/insentif-setting/{config}', [JadwalController::class, 'updateInsentifConfig'])->name('insentif.config.update');
    Route::delete('/insentif-setting/{config}', [JadwalController::class, 'destroyInsentifConfig'])->name('insentif.config.destroy');

    // Jemaat
    Route::get('/jemaat', [JemaatController::class, 'index'])->name('jemaat.index');
    Route::post('/jemaat', [JemaatController::class, 'store'])->name('jemaat.store');
    Route::put('/jemaat/{jemaat}', [JemaatController::class, 'update'])->name('jemaat.update');
    Route::delete('/jemaat/{jemaat}', [JemaatController::class, 'destroy'])->name('jemaat.destroy');

    // === PENDAFTARAN KELUARGA ===
    Route::get('/keluarga', [KeluargaController::class, 'index'])->name('keluarga.index');
    Route::get('/keluarga/create', [KeluargaController::class, 'create'])->name('keluarga.create');
    Route::post('/keluarga', [KeluargaController::class, 'store'])->name('keluarga.store');
    Route::get('/keluarga/{keluarga}', [KeluargaController::class, 'show'])->name('keluarga.show');
    Route::get('/keluarga/{keluarga}/edit', [KeluargaController::class, 'edit'])->name('keluarga.edit');
    Route::put('/keluarga/{keluarga}', [KeluargaController::class, 'update'])->name('keluarga.update');
    Route::delete('/keluarga/{keluarga}', [KeluargaController::class, 'destroy'])->name('keluarga.destroy');
    Route::post('/keluarga/{keluarga}/anggota', [KeluargaController::class, 'storeAnggota'])->name('keluarga.anggota.store');
    Route::put('/keluarga/{keluarga}/anggota/{anggota}', [KeluargaController::class, 'updateAnggota'])->name('keluarga.anggota.update');
    Route::delete('/keluarga/{keluarga}/anggota/{anggota}', [KeluargaController::class, 'destroyAnggota'])->name('keluarga.anggota.destroy');

    // === PENDAFTARAN BAPTIS ===
    Route::get('/baptis', [PendaftaranBaptisController::class, 'index'])->name('baptis.index');
    Route::get('/baptis/create', [PendaftaranBaptisController::class, 'create'])->name('baptis.create');
    Route::post('/baptis', [PendaftaranBaptisController::class, 'store'])->name('baptis.store');
    Route::get('/baptis/{bapti}', [PendaftaranBaptisController::class, 'show'])->name('baptis.show');
    Route::put('/baptis/{bapti}/status', [PendaftaranBaptisController::class, 'updateStatus'])->name('baptis.status');
    Route::delete('/baptis/{bapti}', [PendaftaranBaptisController::class, 'destroy'])->name('baptis.destroy');

    // === PENDAFTARAN NIKAH ===
    Route::get('/nikah', [PendaftaranNikahController::class, 'index'])->name('nikah.index');
    Route::get('/nikah/create', [PendaftaranNikahController::class, 'create'])->name('nikah.create');
    Route::post('/nikah', [PendaftaranNikahController::class, 'store'])->name('nikah.store');
    Route::get('/nikah/{nikah}', [PendaftaranNikahController::class, 'show'])->name('nikah.show');
    Route::post('/nikah/{nikah}/lampiran', [PendaftaranNikahController::class, 'uploadLampiran'])->name('nikah.lampiran');
    Route::post('/nikah/{nikah}/validate', [PendaftaranNikahController::class, 'validate_nikah'])->name('nikah.validate');
    Route::put('/nikah/{nikah}/status', [PendaftaranNikahController::class, 'updateStatus'])->name('nikah.status');
    Route::delete('/nikah/{nikah}', [PendaftaranNikahController::class, 'destroy'])->name('nikah.destroy');
    Route::post('/nikah/{nikah}/insentif', [PendaftaranNikahController::class, 'storeInsentif'])->name('nikah.insentif.store');
    Route::delete('/nikah/{nikah}/insentif/{insentif}', [PendaftaranNikahController::class, 'destroyInsentif'])->name('nikah.insentif.destroy');

    // Pendaftaran (legacy)
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::put('/pendaftaran/{id}/status', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.status');
    Route::delete('/pendaftaran/{id}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

    // Persidangan
    Route::get('/persidangan', [PersidanganController::class, 'index'])->name('persidangan.index');
    Route::post('/persidangan', [PersidanganController::class, 'store'])->name('persidangan.store');
    Route::delete('/persidangan/{id}', [PersidanganController::class, 'destroy'])->name('persidangan.destroy');

    // Pedoman
    Route::get('/pedoman', [PedomanController::class, 'index'])->name('pedoman.index');
    Route::post('/pedoman', [PedomanController::class, 'storePedoman'])->name('pedoman.store');
    Route::delete('/pedoman/{id}', [PedomanController::class, 'destroyPedoman'])->name('pedoman.destroy');
    Route::post('/pedoman/rapp', [PedomanController::class, 'storeRapp'])->name('pedoman.rapp.store');
    Route::get('/pedoman/rapp/{id}', [PedomanController::class, 'showRapp'])->name('pedoman.rapp.show');
    Route::post('/pedoman/rapp/{id}/detail', [PedomanController::class, 'addRappDetail'])->name('pedoman.rapp.detail.store');
    Route::put('/pedoman/rapp/detail/{detailId}', [PedomanController::class, 'updateRappDetail'])->name('pedoman.rapp.detail.update');
    Route::delete('/pedoman/rapp/detail/{detailId}', [PedomanController::class, 'deleteRappDetail'])->name('pedoman.rapp.detail.destroy');
    Route::put('/pedoman/rapp/{id}/approve', [PedomanController::class, 'approveRapp'])->name('pedoman.rapp.approve');
    Route::post('/pedoman/rapp/merge', [PedomanController::class, 'mergeRapp'])->name('pedoman.rapp.merge');
    Route::delete('/pedoman/rapp/{id}', [PedomanController::class, 'destroyRapp'])->name('pedoman.rapp.destroy');
    Route::get('/pedoman/rapp/{id}/realisasi', [PedomanController::class, 'showRealisasi'])->name('pedoman.rapp.realisasi');

    // Input Kategorial
    Route::get('/input-kategorial', [InputKategorialController::class, 'index'])->name('input-kategorial.index');
    Route::get('/input-kategorial/create', [InputKategorialController::class, 'create'])->name('input-kategorial.create');
    Route::post('/input-kategorial', [InputKategorialController::class, 'store'])->name('input-kategorial.store');
    Route::get('/input-kategorial/{id}', [InputKategorialController::class, 'show'])->name('input-kategorial.show');
    Route::put('/input-kategorial/{id}/ajukan', [InputKategorialController::class, 'ajukan'])->name('input-kategorial.ajukan');
    Route::put('/input-kategorial/{id}/approve', [InputKategorialController::class, 'approve'])->name('input-kategorial.approve');
    Route::put('/input-kategorial/{id}/reject', [InputKategorialController::class, 'reject'])->name('input-kategorial.reject');
    Route::get('/input-kategorial/{id}/edit', [InputKategorialController::class, 'edit'])->name('input-kategorial.edit');
    Route::put('/input-kategorial/{id}', [InputKategorialController::class, 'update'])->name('input-kategorial.update');
    Route::delete('/input-kategorial/{id}', [InputKategorialController::class, 'destroy'])->name('input-kategorial.destroy');

    // Warta Mingguan
    Route::get('/warta', [WartaController::class, 'index'])->name('warta.index');
    Route::get('/warta/{id}', [WartaController::class, 'show'])->name('warta.show');
    Route::get('/warta/{id}/print', [WartaController::class, 'print'])->name('warta.print');
    Route::post('/warta/{id}/refresh', [WartaController::class, 'refresh'])->name('warta.refresh');
    Route::delete('/warta/{id}', [WartaController::class, 'destroy'])->name('warta.destroy');

    // Lingkungan Sion
    Route::get('/lingkungan', [LingkunganSionController::class, 'index'])->name('lingkungan.index');
    Route::get('/lingkungan/dashboard', [LingkunganSionController::class, 'dashboard'])->name('lingkungan.dashboard');
    Route::get('/lingkungan/{lingkungan}', [LingkunganSionController::class, 'show'])->name('lingkungan.show');
    Route::put('/lingkungan/{lingkungan}', [LingkunganSionController::class, 'update'])->name('lingkungan.update');

    // File Manager
    Route::get('/files', [FileManagerController::class, 'index'])->name('files.index');
    Route::post('/files/upload', [FileManagerController::class, 'upload'])->name('files.upload');
    Route::post('/files/folder', [FileManagerController::class, 'createFolder'])->name('files.folder');
    Route::delete('/files', [FileManagerController::class, 'destroy'])->name('files.destroy');

    // Laporan
    Route::get('/laporan', fn() => view('laporan.index'))->name('laporan.index');

    // Print / PDF
    Route::get('/print/surat-tugas/{jadwal}', [PrintController::class, 'suratTugas'])->name('print.surat-tugas');
    Route::get('/print/amplop/{jadwal}', [PrintController::class, 'amplop'])->name('print.amplop');
    Route::get('/print/tali-kasih/{jadwal}', [PrintController::class, 'taliKasih'])->name('print.tali-kasih');
    Route::get('/print/warta/{jadwal}', [PrintController::class, 'warta'])->name('print.warta');
    Route::get('/print/surat-tanda-tangan/{jadwal}', [PrintController::class, 'suratTandaTangan'])->name('print.surat-tanda-tangan');
    Route::get('/print/jadwal-ibadah/{jadwal}', [PrintController::class, 'jadwalIbadah'])->name('print.jadwal-ibadah');
    Route::get('/print/rekap-insentif', [PrintController::class, 'rekapInsentif'])->name('print.rekap-insentif');
    Route::get('/print/za14', [PrintController::class, 'laporanZA14'])->name('print.za14');
    Route::get('/print/za15', [PrintController::class, 'laporanZA15'])->name('print.za15');
    Route::get('/print/za16', [PrintController::class, 'laporanZA16'])->name('print.za16');
    Route::get('/print/surat-baptis/{bapti}', [PrintController::class, 'suratBaptis'])->name('print.surat-baptis');
    Route::get('/print/surat-nikah/{nikah}', [PrintController::class, 'suratNikah'])->name('print.surat-nikah');
    Route::get('/print/kartu-keluarga/{keluarga}', [PrintController::class, 'kartuKeluarga'])->name('print.kartu-keluarga');
    Route::get('/print/rekap-ps', [PrintController::class, 'rekapPS'])->name('print.rekap-ps');
    Route::get('/print/rekap-kk', [PrintController::class, 'rekapKK'])->name('print.rekap-kk');
    Route::get('/print/daftar-jemaat', [PrintController::class, 'daftarJemaat'])->name('print.daftar-jemaat');
    Route::get('/print/daftar-keluarga', [PrintController::class, 'daftarKeluarga'])->name('print.daftar-keluarga');
    Route::get('/print/warta-mingguan', [PrintController::class, 'wartaMingguan'])->name('print.warta-mingguan');
    Route::get('/print/rapp/{id}', [PrintController::class, 'rappGabungan'])->name('print.rapp');
    Route::get('/print/jadwal-mingguan', [PrintController::class, 'jadwalMingguan'])->name('print.jadwal-mingguan');
    Route::get('/print/kwitansi/{transaksi}', [PrintController::class, 'kwitansi'])->name('print.kwitansi');

    // Backup & Arsip
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/generate', [BackupController::class, 'generate'])->name('backup.generate');
    Route::get('/backup/{id}/download', [BackupController::class, 'download'])->name('backup.download');
    Route::post('/backup/{id}/cleanup', [BackupController::class, 'cleanup'])->name('backup.cleanup');
    Route::post('/backup/upload-sql', [BackupController::class, 'uploadSql'])->name('backup.upload-sql');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profil', [SettingsController::class, 'updateProfil'])->name('settings.profil');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
    Route::put('/settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])->name('settings.users.destroy');
    Route::put('/settings/users/{user}/reset-password', [SettingsController::class, 'resetPassword'])->name('settings.users.reset-password');
    Route::put('/settings/permissions', [SettingsController::class, 'updatePermissions'])->name('settings.permissions');
    Route::put('/settings/petugas', [SettingsController::class, 'updatePetugasSettings'])->name('settings.petugas');
    Route::post('/settings/slot', [SettingsController::class, 'storeSlot'])->name('settings.slot.store');
    Route::put('/settings/slot/{id}', [SettingsController::class, 'updateSlot'])->name('settings.slot.update');
    Route::delete('/settings/slot/{id}', [SettingsController::class, 'destroySlot'])->name('settings.slot.destroy');
    Route::put('/settings/approval', [SettingsController::class, 'updateApprovalSettings'])->name('settings.approval');
    Route::put('/settings/nikah-biaya', [SettingsController::class, 'updateNikahBiaya'])->name('settings.nikah-biaya');
    Route::post('/settings/ttd', [SettingsController::class, 'storeTtd'])->name('settings.ttd.store');
    Route::post('/settings/maintenance', [SettingsController::class, 'toggleMaintenance'])->name('settings.maintenance');

    // CMS Admin Panel
    Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
    Route::post('/cms/settings', [CmsController::class, 'updateSettings'])->name('cms.settings.update');
    Route::post('/cms/announcements', [CmsController::class, 'storeAnnouncement'])->name('cms.announcements.store');
    Route::put('/cms/announcements/{id}', [CmsController::class, 'updateAnnouncement'])->name('cms.announcements.update');
    Route::delete('/cms/announcements/{id}', [CmsController::class, 'destroyAnnouncement'])->name('cms.announcements.destroy');
    Route::post('/cms/gallery', [CmsController::class, 'storeGallery'])->name('cms.gallery.store');
    Route::delete('/cms/gallery/{id}', [CmsController::class, 'destroyGallery'])->name('cms.gallery.destroy');
    Route::post('/cms/verses', [CmsController::class, 'storeVerse'])->name('cms.verses.store');
    Route::delete('/cms/verses/{id}', [CmsController::class, 'destroyVerse'])->name('cms.verses.destroy');
    Route::post('/cms/video', [CmsController::class, 'storeVideo'])->name('cms.video.store');
    Route::delete('/cms/video/{id}', [CmsController::class, 'destroyVideo'])->name('cms.video.destroy');
});
