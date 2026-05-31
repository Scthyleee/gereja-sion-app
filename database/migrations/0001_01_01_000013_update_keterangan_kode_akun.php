<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // === PENERIMAAN — keterangan sesuai ZA5 ===
        // UP 01 - MPH (Majelis Pekerja Harian)
        $updates = [
            'I(APn).01.01' => 'KK Ibadah Minggu / Ibadah Biasa',
            'I(APn).01.02' => 'PS MPH (Persembahan Syukur Majelis)',
            'I(APn).01.03' => 'Dana Tabungan / Cadangan',
            'I(APn).01.04' => 'Donasi untuk MPH',
            'I(APn).01.05' => 'Sewa Asset Gereja',
            'I(APn).01.06' => 'Biaya Administrasi Jemaat',
            'I(APn).01.07' => 'Bunga Tabungan Gereja',
            'I(APn).01.08' => 'Dana SUMBU-GELIBU',
            'I(APn).01.09' => 'Hasil Investasi Gereja',
            'I(APn).01.10' => 'Share dari UP Domain 02-17',
            'I(APn).01.11' => 'Bantuan / Sumbangan untuk MPH',
            'I(APn).01.12' => 'Bagi Hasil Dana Transitoris',
            'I(APn).01.13' => 'Setoran Dana Transitoris',
            'I(APn).01.14' => 'Penjualan Asset Gereja',

            // UP 02 - SPA (Seksi Pelayanan Anak)
            'I(APn).02.01' => 'KK Ibadah SPA',
            'I(APn).02.02' => 'PS untuk SPA',
            'I(APn).02.03' => 'Iuran Anggota SPA',
            'I(APn).02.04' => 'Donasi untuk SPA (dari MPH)',
            'I(APn).02.05' => 'Share MPH untuk SPA',
            'I(APn).02.06' => 'Bantuan untuk SPA',
            'I(APn).02.07' => 'Pendapatan Lain SPA',

            // UP 03 - SPRP (Seksi Pelayanan Remaja dan Pemuda)
            'I(APn).03.01' => 'KK Ibadah SPRP',
            'I(APn).03.02' => 'PS untuk SPRP',
            'I(APn).03.03' => 'Iuran Anggota SPRP',
            'I(APn).03.04' => 'Donasi untuk SPRP (dari MPH)',
            'I(APn).03.05' => 'Share MPH untuk SPRP',
            'I(APn).03.06' => 'Bantuan untuk SPRP',
            'I(APn).03.07' => 'Pendapatan Lain SPRP',

            // UP 04 - SPPer (Seksi Pelayanan Perempuan)
            'I(APn).04.01' => 'KK Ibadah SPPer',
            'I(APn).04.02' => 'PS untuk SPPer',
            'I(APn).04.03' => 'Iuran Anggota SPPer',
            'I(APn).04.04' => 'Donasi untuk SPPer (dari MPH)',
            'I(APn).04.05' => 'Share MPH untuk SPPer',
            'I(APn).04.06' => 'Bantuan untuk SPPer',
            'I(APn).04.07' => 'Pendapatan Lain SPPer',

            // UP 05 - SPB (Seksi Pelayanan Bapak)
            'I(APn).05.01' => 'KK Ibadah SPB',
            'I(APn).05.02' => 'PS untuk SPB',
            'I(APn).05.03' => 'Iuran Anggota SPB',
            'I(APn).05.04' => 'Donasi untuk SPB (dari MPH)',
            'I(APn).05.05' => 'Share MPH untuk SPB',
            'I(APn).05.06' => 'Bantuan untuk SPB',
            'I(APn).05.07' => 'Pendapatan Lain SPB',

            // UP 06 - SPUI (Seksi Pelayanan Usia Indah)
            'I(APn).06.01' => 'KK Ibadah SPUI',
            'I(APn).06.02' => 'PS untuk SPUI',
            'I(APn).06.03' => 'Iuran Anggota SPUI',
            'I(APn).06.04' => 'Donasi untuk SPUI (dari MPH)',
            'I(APn).06.05' => 'Share MPH untuk SPUI',
            'I(APn).06.06' => 'Bantuan untuk SPUI',
            'I(APn).06.07' => 'Pendapatan Lain SPUI',

            // UP 07 - KPI/PKB (Kesaksian & Pemberitaan Injil / Kabar Baik)
            'I(APn).07.01' => 'KK Ibadah KPI/PKB',
            'I(APn).07.02' => 'PS untuk KPI/PKB',
            'I(APn).07.03' => 'Iuran Anggota KPI/PKB',
            'I(APn).07.04' => 'Donasi untuk KPI/PKB (dari MPH)',
            'I(APn).07.05' => 'Share MPH untuk KPI/PKB',
            'I(APn).07.06' => 'Bantuan untuk KPI/PKB',
            'I(APn).07.07' => 'Pendapatan Lain KPI/PKB',

            // UP 08 - PHBG (Panitia Hari Raya Gereja)
            'I(APn).08.01' => 'KK Ibadah PHBG',
            'I(APn).08.02' => 'PS untuk PHBG',
            'I(APn).08.03' => 'Iuran Anggota PHBG',
            'I(APn).08.04' => 'Donasi untuk PHBG (dari MPH)',
            'I(APn).08.05' => 'Share MPH untuk PHBG',
            'I(APn).08.06' => 'Bantuan untuk PHBG',
            'I(APn).08.07' => 'Pendapatan Lain PHBG',

            // UP 09 - P4A/SarPrasG (Panitia Pembangunan & Sarana Prasarana)
            'I(APn).09.01' => 'KK Ibadah P4A/SarPrasG',
            'I(APn).09.02' => 'PS untuk P4A/SarPrasG',
            'I(APn).09.03' => 'Iuran Anggota P4A/SarPrasG',
            'I(APn).09.04' => 'Donasi untuk P4A/SarPrasG (dari MPH)',
            'I(APn).09.05' => 'Share MPH untuk P4A/SarPrasG',
            'I(APn).09.06' => 'Bantuan untuk P4A/SarPrasG',
            'I(APn).09.07' => 'Pendapatan Lain P4A/SarPrasG',

            // UP 10 - TPDS (Tim Pelayanan Diakonia Sosial)
            'I(APn).10.01' => 'KK Ibadah TPDS',
            'I(APn).10.02' => 'PS untuk TPDS',
            'I(APn).10.03' => 'Iuran Anggota TPDS',
            'I(APn).10.04' => 'Donasi untuk TPDS (dari MPH)',
            'I(APn).10.05' => 'Share MPH untuk TPDS',
            'I(APn).10.06' => 'Bantuan untuk TPDS',
            'I(APn).10.07' => 'Pendapatan Lain TPDS',

            // UP 11 - TPDK (Tim Pelayanan Diakonia Kedukaan)
            'I(APn).11.01' => 'KK Ibadah TPDK',
            'I(APn).11.02' => 'PS untuk TPDK',
            'I(APn).11.03' => 'Iuran Anggota TPDK',
            'I(APn).11.04' => 'Donasi untuk TPDK (dari MPH)',
            'I(APn).11.05' => 'Share MPH untuk TPDK',
            'I(APn).11.06' => 'Bantuan untuk TPDK',
            'I(APn).11.07' => 'Pendapatan Lain TPDK',

            // UP 12 - TPMTI (Tim Pelayanan Multimedia & TI)
            'I(APn).12.01' => 'KK Ibadah TPMTI',
            'I(APn).12.02' => 'PS untuk TPMTI',
            'I(APn).12.03' => 'Iuran Anggota TPMTI',
            'I(APn).12.04' => 'Donasi untuk TPMTI (dari MPH)',
            'I(APn).12.05' => 'Share MPH untuk TPMTI',
            'I(APn).12.06' => 'Bantuan untuk TPMTI',
            'I(APn).12.07' => 'Pendapatan Lain TPMTI',

            // UP 13 - TPMPSG (Tim Pelayanan Musik & Paduan Suara Gerejawi)
            'I(APn).13.01' => 'KK Ibadah TPMPSG',
            'I(APn).13.02' => 'PS untuk TPMPSG',
            'I(APn).13.03' => 'Iuran Anggota TPMPSG',
            'I(APn).13.04' => 'Donasi untuk TPMPSG (dari MPH)',
            'I(APn).13.05' => 'Share MPH untuk TPMPSG',
            'I(APn).13.06' => 'Bantuan untuk TPMPSG',
            'I(APn).13.07' => 'Pendapatan Lain TPMPSG',

            // UP 14 - TPUD (Tim Pelayanan Usaha Dana)
            'I(APn).14.01' => 'KK Ibadah TPUD',
            'I(APn).14.02' => 'PS untuk TPUD',
            'I(APn).14.03' => 'Iuran Anggota TPUD',
            'I(APn).14.04' => 'Donasi untuk TPUD (dari MPH)',
            'I(APn).14.05' => 'Share MPH untuk TPUD',
            'I(APn).14.06' => 'Bantuan untuk TPUD',
            'I(APn).14.07' => 'Pendapatan Lain TPUD',

            // UP 15 - MP (Majelis Pertimbangan)
            'I(APn).15.01' => 'KK Ibadah MP',
            'I(APn).15.02' => 'PS untuk MP',
            'I(APn).15.03' => 'Iuran Anggota MP',
            'I(APn).15.04' => 'Donasi untuk MP (dari MPH)',
            'I(APn).15.05' => 'Share MPH untuk MP',
            'I(APn).15.06' => 'Bantuan untuk MP',
            'I(APn).15.07' => 'Pendapatan Lain MP',

            // UP 16 - BPP (Badan Pengawas Perbendaharaan)
            'I(APn).16.01' => 'KK Ibadah BPP',
            'I(APn).16.02' => 'PS untuk BPP',
            'I(APn).16.03' => 'Iuran Anggota BPP',
            'I(APn).16.04' => 'Donasi untuk BPP (dari MPH)',
            'I(APn).16.05' => 'Share MPH untuk BPP',
            'I(APn).16.06' => 'Bantuan untuk BPP',
            'I(APn).16.07' => 'Pendapatan Lain BPP',

            // UP 17 - PLP SION (Pengurus Lingkungan Pelayanan Sion)
            'I(APn).17.01' => 'KK Ibadah PLP SION',
            'I(APn).17.02' => 'PS untuk PLP SION',
            'I(APn).17.03' => 'Iuran Anggota PLP SION',
            'I(APn).17.04' => 'Donasi untuk PLP SION (dari MPH)',
            'I(APn).17.05' => 'Share MPH untuk PLP SION',
            'I(APn).17.06' => 'Bantuan untuk PLP SION',
            'I(APn).17.07' => 'Pendapatan Lain PLP SION',
        ];

        foreach ($updates as $kode => $ket) {
            DB::table('kode_akun')->where('kode', $kode)->update(['keterangan' => $ket]);
        }

        // === PENGELUARAN ===
        $pengeluaran = [
            // UP 01 - MPH
            'II(APg).01.01' => 'Insentif Petugas Ibadah',
            'II(APg).01.02' => 'Honor Pegawai Gereja',
            'II(APg).01.03' => 'Tunjangan Transport Petugas',
            'II(APg).01.04' => 'Biaya Rapat/Sidang MPH',
            'II(APg).01.05' => 'Biaya Administrasi/ATK',
            'II(APg).01.06' => 'Biaya Listrik/Air/Telepon',
            'II(APg).01.07' => 'Biaya Pemeliharaan Gedung',
            'II(APg).01.08' => 'Biaya Pelayanan Sakramen',
            'II(APg).01.09' => 'Biaya Diakonia/Sosial',
            'II(APg).01.10' => 'Dana Bantuan Jemaat Sakit/Duka',
        ];

        foreach ($pengeluaran as $kode => $ket) {
            DB::table('kode_akun')->where('kode', $kode)->update(['keterangan' => $ket]);
        }

        // Bulk fix remaining APg keterangan per UP (02-17)
        $upNames = [
            2 => 'SPA', 3 => 'SPRP', 4 => 'SPPer', 5 => 'SPB',
            6 => 'SPUI', 7 => 'KPI/PKB', 8 => 'PHBG', 9 => 'P4A/SarPrasG',
            10 => 'TPDS', 11 => 'TPDK', 12 => 'TPMTI', 13 => 'TPMPSG',
            14 => 'TPUD', 15 => 'MP', 16 => 'BPP', 17 => 'PLP SION',
        ];
        $apgLabels = [
            '01' => 'Honor/Insentif Pelaksana',
            '02' => 'Bahan & Peralatan Teknis',
            '03' => 'Pelatihan-pelatihan',
            '04' => 'Bantuan Diakonia',
            '05' => 'Biaya Rapat/Pertemuan',
            '06' => 'Share untuk MPH & Kontingensi',
            '07' => 'Lain-lain',
        ];
        foreach ($upNames as $up => $nama) {
            $upStr = str_pad($up, 2, '0', STR_PAD_LEFT);
            foreach ($apgLabels as $sub => $label) {
                $kode = "II(APg).{$upStr}.{$sub}";
                DB::table('kode_akun')->where('kode', $kode)->update([
                    'keterangan' => "{$label} — {$nama}"
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('kode_akun')->update(['keterangan' => null]);
    }
};
