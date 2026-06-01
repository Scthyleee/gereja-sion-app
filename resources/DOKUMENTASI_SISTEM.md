# DOKUMENTASI SISTEM — GerejaApp (Laravel 12)

Sistem Informasi Manajemen Gereja SION. Stack: Laravel 12, MySQL, Bootstrap 5, DomPDF, Simple QR Code.

---

## 1. DATABASE TABLES & RELASI

### Core
| Tabel | PK | Deskripsi |
|---|---|---|
| `roles` | id | Daftar role (Super Admin, Admin, Staf, Bendahara, Majelis, Viewer, Ketua MPH, SPPer, SPA, SPRP, SPB, SPUI, KPI/PKB, PHBG, Pembangunan, Ketua Lingkungan, Bendahara Lingkungan, Sekretaris Lingkungan) |
| `users` | id | FK: role_id→roles, warga_id→warga_jemaat, unit_pelayanan_id→unit_pelayanan, lingkungan_sion_id→lingkungan_sion |
| `sessions` | id(string) | FK: user_id→users |
| `login_attempts` | id | Brute force protection |
| `profil_gereja` | id | Nama, alamat, telepon, email, logo gereja |
| `system_settings` | id | Key-value config (biaya nikah, insentif, quota petugas, dll) |

### Master Data
| Tabel | PK | Deskripsi |
|---|---|---|
| `warga_jemaat` | id_warga | Data jemaat. FK: keluarga_id→keluarga, lingkungan_sion_id→lingkungan_sion. Fields: nama, JK, TTL, alamat, status_baptis, status_sidi, pekerjaan, hubungan_keluarga, nama_ayah, nama_ibu |
| `unit_pelayanan` | id | Unit pelayanan (MPH, SPA, SPRP, SPPer, SPB, SPUI, KPI/PKB, PHBG, Pembangunan). Field: nomor_up (unique) |
| `kode_akun` | id | Master akun keuangan ZA6. FK: nomor_up→unit_pelayanan. Fields: kode, tipe(penerimaan/pengeluaran), nama |
| `saldo_awal` | id | Saldo awal per tahun per kantong_dana |
| `mapping_kode_lama` | id | Mapping kode akun lama ke baru |

### Keuangan
| Tabel | PK | Deskripsi |
|---|---|---|
| `transaksi` | id | FK: kode_akun_id, dibuat_oleh→users, diverifikasi_oleh→users. Fields: tanggal, tipe, jumlah, kantong_dana, bulan, tahun, status(draft/verified/approved) |
| `pedoman_rapp` | id | Anggaran (RAPP). FK: created_by, approved_by→users, unit_pelayanan_id |
| `pedoman_rapp_detail` | id | Detail RAPP. FK: pedoman_id→pedoman_rapp, kode_akun_id |
| `rapp_realisasi` | id | Realisasi vs anggaran. FK: pedoman_rapp_detail_id, transaksi_id |
| `anggaran_kegiatan` | id | FK: kode_akun_id, created_by→users |
| `realisasi_anggaran` | id | FK: anggaran_id→anggaran_kegiatan |

### Jadwal & Pelayanan
| Tabel | PK | Deskripsi |
|---|---|---|
| `petugas` | id_petugas | FK: warga_id→warga_jemaat, kode_akun_id. Fields: nama, jenis_tugas, insentif, status |
| `jenis_ibadah_master` | id | Master jenis ibadah |
| `jadwal_ibadah` | id_jadwal | Fields: tanggal, jenis_ibadah, waktu, jam_selesai, lokasi, lokasi_tipe(gereja/rumah_jemaat/gedung_lain), lokasi_alamat, kategori(ibadah_rutin/khusus/pernikahan/baptis/dll), jumlah_hadir, status. FK: pendaftaran_nikah_id, pendaftaran_baptis_id, warga_terkait_id |
| `jadwal_petugas` | id | FK: jadwal_id→jadwal_ibadah, petugas_id→petugas. Fields: jenis_tugas, nominal_insentif, status_bayar, sumber |
| `jadwal_kantong` | id | Persembahan per kantong. FK: jadwal_id, kode_akun_id |
| `jadwal_amplop` | id | Amplop per KK. FK: jadwal_id, warga_id |
| `jadwal_amplop_detail` | id | Detail amplop. FK: amplop_id, kode_akun_id |
| `petugas_slot_template` | id | Template slot petugas (Pendeta, Song Leader, Pianis, LCD, dll) |

### Pendaftaran & Registrasi
| Tabel | PK | Deskripsi |
|---|---|---|
| `lingkungan_sion` | id | Wilayah gerejawi (Sion 1/2/3). FK: ketua_id, bendahara_id, sekretaris_id→users |
| `keluarga` | id | Data KK. FK: lingkungan_sion_id, created_by→users. Fields: no_kk, nama_kepala_keluarga, alamat, rt, rw, kelurahan, kecamatan, kota |
| `pendaftaran_baptis` | id | FK: warga_id, lingkungan_sion_id, jadwal_ibadah_id, disetujui_oleh→users. Status: pending→terdaftar→lulus_katekisasi→dijadwalkan→selesai/ditolak |
| `pendaftaran_nikah` | id | FK: pria_warga_id, wanita_warga_id→warga_jemaat, jadwal_ibadah_id, kode_akun_admin_id, kode_akun_fasilitas_id, divalidasi_oleh→users. Status: pending→terdaftar→dijadwalkan→divalidasi→selesai/ditolak |
| `pendaftaran_nikah_insentif` | id | Dynamic insentif rows. FK: pendaftaran_nikah_id. Fields: jenis_insentif, nominal |
| `pendaftaran_nikah_lampiran` | id | File lampiran. FK: pendaftaran_nikah_id, file_manager_id |
| `pendaftaran` (legacy) | id_pendaftaran | Pendaftaran lama (warga/baptis/sidi/pernikahan/pelayanan) |
| `penandatangan_dokumen` | id | Config tanda tangan per modul. Fields: module(baptis/nikah/surat), urutan, nama_tampilan, jabatan_tampilan, qr_enabled |

### Dokumen & Modul Lain
| Tabel | PK | Deskripsi |
|---|---|---|
| `bahan_persidangan` | id_dokumen | Upload dokumen sidang |
| `pedoman` | id_pedoman | Dokumen pedoman umum |
| `file_manager` | id | File/folder manager. Self-referencing: parent_id |
| `audit_log` | id_log | Log aktivitas user |
| `pengurus_gereja` | id_pengurus | Data pengurus gereja |
| `approval_dokumen` | id | Approval workflow generic |
| `arsip_dokumen_otomatis` | id | Arsip auto-generated |
| `input_kategorial` | id | Input keuangan per UP. FK: unit_pelayanan_id, dibuat_oleh, divalidasi_oleh→users |
| `input_kategorial_detail` | id | Detail input. FK: input_kategorial_id, kode_akun_id |
| `approval_settings` | id | Config validator per modul |
| `page_permissions` | id | Akses halaman per role/user |
| `warta_mingguan` | id | Laporan mingguan otomatis |

---

## 2. MODELS (app/Models/)

| Model | Table | Relasi Utama |
|---|---|---|
| User | users | belongsTo(Role), belongsTo(UnitPelayanan). Methods: hasRole(), isKategorial(), canValidate() |
| Role | roles | - |
| WargaJemaat | warga_jemaat | belongsTo(Keluarga), belongsTo(LingkunganSion) |
| KodeAkun | kode_akun | belongsTo(UnitPelayanan). Scopes: penerimaan(), pengeluaran() |
| UnitPelayanan | unit_pelayanan | hasMany(KodeAkun) |
| Transaksi | transaksi | belongsTo(KodeAkun), belongsTo(User, dibuat_oleh) |
| JadwalIbadah | jadwal_ibadah | hasMany(JadwalPetugas), hasMany(JadwalKantong), hasMany(JadwalAmplop) |
| JadwalPetugas | jadwal_petugas | belongsTo(JadwalIbadah), belongsTo(Petugas) |
| JadwalKantong | jadwal_kantong | belongsTo(JadwalIbadah), belongsTo(KodeAkun) |
| JadwalAmplop | jadwal_amplop | belongsTo(JadwalIbadah), hasMany(JadwalAmplopDetail) |
| JadwalAmplopDetail | jadwal_amplop_detail | belongsTo(JadwalAmplop), belongsTo(KodeAkun) |
| Petugas | petugas | belongsTo(WargaJemaat), belongsTo(KodeAkun), hasMany(JadwalPetugas) |
| LingkunganSion | lingkungan_sion | belongsTo(User, ketua/bendahara/sekretaris), hasMany(Keluarga), hasMany(WargaJemaat) |
| Keluarga | keluarga | belongsTo(LingkunganSion), hasMany(WargaJemaat, anggota) |
| PendaftaranBaptis | pendaftaran_baptis | belongsTo(WargaJemaat), belongsTo(LingkunganSion), belongsTo(JadwalIbadah) |
| PendaftaranNikah | pendaftaran_nikah | belongsTo(WargaJemaat, pria/wanita), belongsTo(JadwalIbadah), hasMany(Insentif), hasMany(Lampiran) |
| PendaftaranNikahInsentif | pendaftaran_nikah_insentif | belongsTo(PendaftaranNikah) |
| PendaftaranNikahLampiran | pendaftaran_nikah_lampiran | belongsTo(PendaftaranNikah) |
| PenandatanganDokumen | penandatangan_dokumen | - |
| InputKategorial | input_kategorial | belongsTo(UnitPelayanan), hasMany(Detail), belongsTo(User) |
| InputKategorialDetail | input_kategorial_detail | belongsTo(InputKategorial), belongsTo(KodeAkun) |
| RappRealisasi | rapp_realisasi | belongsTo(PedomanRappDetail), belongsTo(Transaksi) |
| WartaMingguan | warta_mingguan | belongsTo(User) |

---

## 3. CONTROLLERS & FUNGSI

### AuthController
- `showLogin()` — Halaman login
- `login()` — Proses autentikasi + brute force protection
- `logout()` — Logout

### DashboardController
- `index()` — Dashboard utama: statistik jemaat, keuangan bulan ini, jadwal terdekat, grafik

### KeuanganController
- `index()` — Ringkasan keuangan (penerimaan vs pengeluaran)
- `masterAkun()` — CRUD master kode akun ZA6
- `transaksi()` — List transaksi dengan filter bulan/tahun/kantong
- `storeTransaksi()` — Simpan transaksi baru
- `destroyTransaksi()` — Hapus transaksi
- `saldo()` — Saldo per kantong dana

### JadwalController
- `index()` — Tab: Jadwal, Insentif, Petugas. Filter bulan/tahun
- `store()` — Buat jadwal baru (+ kategori, lokasi_tipe, jam_selesai)
- `show($jadwal)` — Detail jadwal + petugas + kantong + amplop
- `update/destroy` — Edit/hapus jadwal
- `storePetugas/destroyPetugas` — Assign/remove petugas ke jadwal
- `storeKantong/destroyKantong` — Input kantong persembahan
- `storeAmplop/destroyAmplop` — Input amplop KK
- `finalize($jadwal)` — Finalisasi: auto-create transaksi dari kantong + amplop
- `petugasMaster()` — CRUD master petugas
- `insentif()` — Rekap insentif bulanan

### JemaatController
- `index()` — List warga jemaat + search
- `store()` — Tambah warga
- `update/destroy` — Edit/hapus warga

### KeluargaController
- `index()` — List keluarga + filter lingkungan
- `create/store` — Form pendaftaran keluarga + dynamic anggota
- `show($keluarga)` — Detail KK + tabel anggota
- `edit/update` — Edit data keluarga
- `destroy` — Hapus keluarga

### PendaftaranBaptisController
- `index()` — List pendaftaran baptis + filter status
- `create/store` — Form pendaftaran baptis
- `show($bapti)` — Detail + workflow actions + penandatangan preview
- `updateStatus()` — Workflow: pending→terdaftar→dijadwalkan→selesai. Auto-create jadwal_ibadah, auto-update warga status_baptis
- `destroy` — Hapus

### PendaftaranNikahController
- `index()` — List pendaftaran nikah
- `create/store` — Form 2 mempelai + biaya + dynamic insentif. Auto-create jadwal + auto-create folder file_manager
- `show($nikah)` — Detail mempelai + biaya + insentif + lampiran + workflow
- `uploadLampiran()` — Upload file lampiran (KTP, KK, Surat Baptis, dll)
- `validate_nikah()` — Validasi: auto-create transaksi ke kode_akun + insentif ke jadwal_petugas
- `updateStatus/destroy`

### LingkunganSionController
- `index()` — List lingkungan + withCount(keluarga, warga)
- `show($lingkungan)` — Detail: stats KK/jiwa/baptis, pengurus, daftar keluarga
- `dashboard()` — Self-service untuk Ketua/Bendahara/Sekretaris: stats + keuangan lingkungan
- `update()` — Edit info lingkungan

### InputKategorialController
- `index()` — List input per UP, filter status/bulan/tahun
- `create/store` — Form input keuangan kategorial + detail line items
- `show($id)` — Detail input + approval trail
- `edit/update` — Edit draft
- `ajukan($id)` — Ajukan untuk approval
- `approve($id)` — Approve + auto-create transaksi
- `reject($id)` — Tolak dengan catatan
- `destroy`

### PedomanController
- `index()` — Tab: Pedoman Umum + RAPP
- `storePedoman/destroyPedoman` — Upload/hapus dokumen pedoman
- `storeRapp/showRapp` — Buat/detail RAPP
- `addRappDetail/deleteRappDetail` — Detail anggaran per kode akun
- `approveRapp` — Approve RAPP
- `mergeRapp` — Gabung RAPP dari beberapa UP
- `showRealisasi` — Realisasi vs anggaran

### WartaController
- `index()` — List warta mingguan
- `generate()` — Auto-generate warta dari data transaksi minggu tersebut
- `show/print` — Detail/cetak warta

### PrintController (PDF)
- `suratTugas($jadwal)` — PDF surat tugas petugas
- `amplop($jadwal)` — PDF rekap amplop
- `warta($jadwal)` — PDF warta jemaat
- `rekapInsentif()` — PDF rekap insentif bulanan
- `laporanZA14()` — PDF penerimaan bulanan
- `laporanZA15()` — PDF pengeluaran bulanan
- `laporanZA16()` — PDF neraca saldo
- `suratBaptis($bapti)` — PDF surat baptisan + QR signature
- `suratNikah($nikah)` — PDF surat nikah + biaya + QR signature
- `kartuKeluarga($keluarga)` — PDF formulir KK

### SettingsController
- `index()` — Tab: Profil, Users, Permissions, Petugas, Slot Template, Approval, Audit Log, Biaya Nikah, Tanda Tangan
- `updateProfil/storeUser/updateUser/destroyUser`
- `updatePermissions/updatePetugasSettings`
- `storeSlot/updateSlot/destroySlot` — CRUD slot template petugas
- `updateApprovalSettings` — Config approver per modul
- `updateNikahBiaya()` — Update biaya nikah di system_settings
- `storeTtd()` — Tambah penandatangan dokumen

### FileManagerController
- `index()` — Browse file/folder
- `upload/createFolder/destroy`

### PersidanganController
- `index/store/destroy` — CRUD bahan persidangan

---

## 4. VIEWS (resources/views/)

```
layouts/app.blade.php          — Layout utama + sidebar + Sacred Dusk theme
auth/login.blade.php           — Halaman login
dashboard.blade.php            — Dashboard utama

keuangan/
  index.blade.php              — Ringkasan keuangan
  master.blade.php             — Master kode akun
  transaksi.blade.php          — Input/list transaksi
  saldo.blade.php              — Saldo per kantong

jadwal/
  index.blade.php              — Jadwal + Insentif + Petugas (3 tab)
  show.blade.php               — Detail jadwal

jemaat/
  index.blade.php              — Data warga jemaat

pendaftaran/
  keluarga/
    index, create, show, edit  — CRUD keluarga
  baptis/
    index, create, show        — Pendaftaran baptis + workflow
  nikah/
    index, create, show        — Pendaftaran nikah + biaya + lampiran

lingkungan/
  index.blade.php              — List lingkungan (admin)
  show.blade.php               — Detail lingkungan
  dashboard.blade.php          — Self-service dashboard

input-kategorial/
  index, create, show, edit    — Input keuangan kategorial

pedoman/
  index.blade.php              — Pedoman + RAPP
  rapp-show, rapp-realisasi

warta/
  index, show                  — Warta mingguan

settings/
  index.blade.php              — 9 tab pengaturan

files/
  index.blade.php              — File manager

persidangan/
  index.blade.php              — Bahan persidangan

print/
  surat-tugas, amplop, warta, rekap-insentif
  laporan-za14, laporan-za15, laporan-za16
  surat-baptis, surat-nikah, kartu-keluarga
```

---

## 5. ROUTES (routes/web.php)

Total: ~80+ routes. Semua protected oleh middleware `auth` kecuali login.

---

## 6. ROLE & ACCESS

| Role | Akses |
|---|---|
| Super Admin | Semua fitur + settings |
| Admin | Data, jadwal, pendaftaran, keuangan |
| Staf | Data, pendaftaran |
| Bendahara | Keuangan, laporan |
| Majelis | View keuangan, laporan |
| Ketua MPH | RAPP, approval |
| SPPer/SPA/SPRP/SPB/SPUI/KPI/PHBG/Pembangunan | Input kategorial per UP |
| Ketua/Bendahara/Sekretaris Lingkungan | Dashboard lingkungan |
| Viewer | View only |

---

## 7. FITUR UTAMA

1. **Manajemen Keuangan** — Transaksi, 6 kantong dana, ZA14/15/16, saldo
2. **Jadwal & Pelayanan** — Jadwal ibadah, assign petugas, kantong, amplop, finalisasi auto-transaksi
3. **Insentif Petugas** — Tracking per assignment, status bayar, rekap bulanan
4. **Pendaftaran Keluarga** — KK + anggota dynamic + lingkungan sion
5. **Pendaftaran Baptis** — Workflow 6 status, auto-jadwal, auto-update warga
6. **Pendaftaran Nikah** — 2 mempelai, biaya dynamic, insentif dynamic, lampiran, auto-jadwal, auto-transaksi
7. **Lingkungan Sion** — 3 wilayah, pengurus, dashboard self-service
8. **Input Kategorial** — Input keuangan per UP, approval workflow, auto-transaksi
9. **RAPP** — Anggaran per UP, approval, merge, realisasi tracking
10. **Warta Mingguan** — Auto-generate dari data transaksi
11. **Print PDF** — 10 jenis dokumen, QR signature
12. **File Manager** — Upload/folder/browse
13. **Settings** — 9 tab konfigurasi
14. **Audit Log** — Tracking aktivitas user

---

## 8. PACKAGES

- `barryvdh/laravel-dompdf` — PDF generation
- `simplesoftwareio/simple-qrcode` — QR code untuk tanda tangan digital
- Bootstrap 5 + Font Awesome (CDN)
- Google Fonts: Outfit

## 9. DEFAULT ACCOUNTS (password: gereja123)

- admin@gereja.com (Super Admin)
- admingereja@gereja.com, staf@gereja.com, bendahara@gereja.com, majelis@gereja.com, viewer@gereja.com
- ketuamph@gereja.com, spper@gereja.com, spa@gereja.com, sprp@gereja.com, spb@gereja.com, spui@gereja.com, kpi@gereja.com, phbg@gereja.com, pembangunan@gereja.com
- ketuasion1-3@gereja.com, bendaharasion1-3@gereja.com, sekretarission1-3@gereja.com
