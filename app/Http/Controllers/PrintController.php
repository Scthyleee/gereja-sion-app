<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\KodeAkun;
use App\Models\Transaksi;
use App\Models\PendaftaranBaptis;
use App\Models\PendaftaranNikah;
use App\Models\Keluarga;
use App\Models\PenandatanganDokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintController extends Controller
{
    // ===== SURAT TUGAS =====
    public function suratTugas(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $pdf = Pdf::loadView('print.surat-tugas', compact('jadwal', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("surat-tugas-{$jadwal->id_jadwal}.pdf");
    }

    // ===== AMPLOP KECIL PER PETUGAS =====
    public function amplop(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $pdf = Pdf::loadView('print.amplop', compact('jadwal', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("amplop-{$jadwal->id_jadwal}.pdf");
    }

    // ===== TALI KASIH / AMPLOP INSENTIF =====
    public function taliKasih(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $pdf = Pdf::loadView('print.tali-kasih', compact('jadwal', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("tali-kasih-{$jadwal->id_jadwal}.pdf");
    }

    // ===== SURAT TANDA TANGAN (DAFTAR PEMBAYARAN INSENTIF) =====
    public function suratTandaTangan(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $pdf = Pdf::loadView('print.surat-tanda-tangan', compact('jadwal', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("surat-tanda-tangan-{$jadwal->id_jadwal}.pdf");
    }

    // ===== WARTA JEMAAT (uses warta-mingguan-full format) =====
    public function warta(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas', 'kantong.kodeAkun', 'amplop.detail.kodeAkun');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $tanggalAwal = $jadwal->tanggal->copy()->startOfWeek()->toDateString();
        $tanggalAkhir = $jadwal->tanggal->copy()->endOfWeek()->toDateString();

        $psPeriode = \App\Models\TransaksiPS::with('jemaat')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')->orderBy('sumber')->get();

        $kkPeriode = \App\Models\TransaksiKK::with('jemaat')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')->get();

        $pengeluaran = Transaksi::with('kodeAkun')
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')->get();

        $tahun = $jadwal->tanggal->year;
        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
        $saldoData = [];
        $saldoMingguLalu = 0;
        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')
                ->where('tanggal', '<=', $tanggalAkhir)->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')
                ->where('tanggal', '<=', $tanggalAkhir)->sum('jumlah');
            $saldoData[] = (object)['kantong' => $k, 'label' => match($k) {
                'rutin'=>'DANA RUTIN','phbg'=>'DANA HBG','pembangunan'=>'DANA PEMBANGUNAN',
                'cadangan'=>'DANA CADANGAN','diakonia_sosial'=>'DANA DIAKONAL SOSIAL','diakonia_kedukaan'=>'DANA DIAKONIA KEDUKAAN'
            }, 'akhir' => $awal + $masuk - $keluar];

            $masukLalu = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')
                ->where('tanggal', '<', $tanggalAwal)->sum('jumlah');
            $keluarLalu = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')
                ->where('tanggal', '<', $tanggalAwal)->sum('jumlah');
            $saldoMingguLalu += ($awal + $masukLalu - $keluarLalu);
        }

        $totalPenerimaan = $psPeriode->sum('jumlah') + $kkPeriode->sum('jumlah');
        $totalPengeluaran = $pengeluaran->sum('jumlah');
        $totalSaldo = collect($saldoData)->sum('akhir');

        $amplopPerIbadah = $psPeriode->where('sumber', 'jadwal')
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d') . '|' . ($item->jadwal_ibadah_id ?? 'kantor'));

        $pdf = Pdf::loadView('print.warta-mingguan-full', compact(
            'psPeriode', 'kkPeriode', 'pengeluaran', 'saldoData', 'totalSaldo',
            'saldoMingguLalu', 'totalPenerimaan', 'totalPengeluaran',
            'tanggalAwal', 'tanggalAkhir', 'profil', 'settings', 'amplopPerIbadah'
        ))->setPaper('a4', 'portrait');
        return $pdf->stream("warta-{$jadwal->id_jadwal}.pdf");
    }

    // ===== PRINT JADWAL IBADAH (SINGLE) =====
    public function jadwalIbadah(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas', 'kantong.kodeAkun', 'amplop.detail.kodeAkun');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $pdf = Pdf::loadView('print.jadwal-ibadah', compact('jadwal', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("jadwal-ibadah-{$jadwal->id_jadwal}.pdf");
    }

    // ===== REKAP INSENTIF BULANAN =====
    public function rekapInsentif(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $profil = DB::table('profil_gereja')->first();

        $data = DB::table('jadwal_petugas')
            ->join('petugas', 'jadwal_petugas.petugas_id', '=', 'petugas.id_petugas')
            ->join('jadwal_ibadah', 'jadwal_petugas.jadwal_id', '=', 'jadwal_ibadah.id_jadwal')
            ->whereMonth('jadwal_ibadah.tanggal', $bulan)
            ->whereYear('jadwal_ibadah.tanggal', $tahun)
            ->select('petugas.nama_petugas', 'petugas.jenis_tugas',
                DB::raw('COUNT(*) as jumlah_tugas'),
                DB::raw('SUM(jadwal_petugas.nominal_insentif) as total_insentif'))
            ->groupBy('petugas.id_petugas', 'petugas.nama_petugas', 'petugas.jenis_tugas')
            ->orderByDesc('total_insentif')
            ->get();

        $pdf = Pdf::loadView('print.rekap-insentif', compact('data', 'bulan', 'tahun', 'profil'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream("rekap-insentif-{$bulan}-{$tahun}.pdf");
    }

    // ===== LAPORAN ZA14 (Penerimaan Bulanan) =====
    public function laporanZA14(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $data = KodeAkun::penerimaan()->where('aktif', true)->orderBy('kode')
            ->get()->map(function ($akun) use ($bulan, $tahun) {
                $jumlah = Transaksi::where('kode_akun_id', $akun->id)
                    ->where('bulan', $bulan)->where('tahun', $tahun)
                    ->sum('jumlah');
                return (object) ['kode' => $akun->kode, 'nama' => $akun->keterangan ?: $akun->nama, 'jumlah' => $jumlah];
            })->filter(fn($r) => $r->jumlah > 0);

        $pdf = Pdf::loadView('print.laporan-za14', compact('data', 'bulan', 'tahun', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("laporan-za14-{$bulan}-{$tahun}.pdf");
    }

    // ===== LAPORAN ZA15 (Pengeluaran Bulanan) =====
    public function laporanZA15(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $data = KodeAkun::pengeluaran()->where('aktif', true)->orderBy('kode')
            ->get()->map(function ($akun) use ($bulan, $tahun) {
                $jumlah = Transaksi::where('kode_akun_id', $akun->id)
                    ->where('bulan', $bulan)->where('tahun', $tahun)
                    ->sum('jumlah');
                return (object) ['kode' => $akun->kode, 'nama' => $akun->keterangan ?: $akun->nama, 'jumlah' => $jumlah];
            })->filter(fn($r) => $r->jumlah > 0);

        $pdf = Pdf::loadView('print.laporan-za15', compact('data', 'bulan', 'tahun', 'profil', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("laporan-za15-{$bulan}-{$tahun}.pdf");
    }

    // ===== LAPORAN ZA16 (Neraca Saldo) =====
    public function laporanZA16(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
        $saldoData = [];
        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')->sum('jumlah');
            $saldoData[] = (object) [
                'kantong' => $k,
                'label' => match($k) { 'rutin'=>'Dana Rutin MPH','phbg'=>'Dana PHBG','pembangunan'=>'Dana Pembangunan P4A','cadangan'=>'Dana Cadangan','diakonia_sosial'=>'Dana Diakonia Sosial','diakonia_kedukaan'=>'Dana Diakonia Kedukaan' },
                'awal' => $awal, 'masuk' => $masuk, 'keluar' => $keluar, 'akhir' => $awal + $masuk - $keluar,
            ];
        }

        $pdf = Pdf::loadView('print.laporan-za16', compact('saldoData', 'tahun', 'profil', 'settings'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream("laporan-za16-{$tahun}.pdf");
    }

    // ===== SURAT BAPTISAN KUDUS =====
    public function suratBaptis(PendaftaranBaptis $bapti)
    {
        $bapti->load('warga', 'lingkunganSion', 'jadwal');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');
        $penandatangan = PenandatanganDokumen::where('module', 'baptis')
            ->where('aktif', true)->orderBy('urutan')->get();

        $qrCodes = [];
        foreach ($penandatangan as $p) {
            if ($p->qr_enabled) {
                $data = "GEREJA SION|BAPTIS|{$bapti->nama_lengkap}|{$p->nama_tampilan}|{$p->jabatan_tampilan}|" . now()->toDateString();
                $qrCodes[$p->id] = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('M')->generate($data));
            }
        }

        $pdf = Pdf::loadView('print.surat-baptis', compact('bapti', 'profil', 'settings', 'penandatangan', 'qrCodes'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("surat-baptis-{$bapti->id}.pdf");
    }

    // ===== SURAT NIKAH =====
    public function suratNikah(PendaftaranNikah $nikah)
    {
        $nikah->load('priaWarga', 'wanitaWarga', 'jadwal', 'insentif');
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');
        $penandatangan = PenandatanganDokumen::where('module', 'nikah')
            ->where('aktif', true)->orderBy('urutan')->get();

        $qrCodes = [];
        foreach ($penandatangan as $p) {
            if ($p->qr_enabled) {
                $data = "GEREJA SION|NIKAH|{$nikah->pria_nama}&{$nikah->wanita_nama}|{$p->nama_tampilan}|{$p->jabatan_tampilan}|" . now()->toDateString();
                $qrCodes[$p->id] = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('M')->generate($data));
            }
        }

        $pdf = Pdf::loadView('print.surat-nikah', compact('nikah', 'profil', 'settings', 'penandatangan', 'qrCodes'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("surat-nikah-{$nikah->id}.pdf");
    }

    // ===== KARTU KELUARGA JEMAAT =====
    public function kartuKeluarga(Keluarga $keluarga)
    {
        $keluarga->load('anggota', 'lingkunganSion');
        $profil = DB::table('profil_gereja')->first();

        $pdf = Pdf::loadView('print.kartu-keluarga', compact('keluarga', 'profil'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("kartu-keluarga-{$keluarga->id}.pdf");
    }

    // ===== REKAP PS BULANAN =====
    public function rekapPS(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $profil = DB::table('profil_gereja')->first();

        $data = \App\Models\TransaksiPS::with('jemaat', 'kodeAkun')
            ->where('bulan', $bulan)
            ->orderBy('tanggal')->orderBy('jenis_ps')
            ->get();

        $summary = \App\Models\TransaksiPS::where('bulan', $bulan)
            ->select('jenis_ps', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('jenis_ps')->orderBy('jenis_ps')->get();

        $pdf = Pdf::loadView('print.rekap-ps', compact('data', 'summary', 'bulan', 'profil'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream("rekap-ps-{$bulan}.pdf");
    }

    // ===== REKAP KK =====
    public function rekapKK(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $profil = DB::table('profil_gereja')->first();

        $data = \App\Models\TransaksiKK::with('jemaat')
            ->where('bulan', $bulan)
            ->orderBy('tanggal')->orderBy('jenis_ibadah')
            ->get();

        $pdf = Pdf::loadView('print.rekap-kk', compact('data', 'bulan', 'profil'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("rekap-kk-{$bulan}.pdf");
    }

    // ===== DAFTAR JEMAAT =====
    public function daftarJemaat(Request $request)
    {
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');
        $status = $request->get('status', 'aktif');
        $jemaat = \App\Models\WargaJemaat::when($status !== 'semua', fn($q) => $q->where('status_jemaat', $status))
            ->orderBy('nama_lengkap')->get();

        $pdf = Pdf::loadView('print.daftar-jemaat', compact('jemaat', 'profil', 'settings', 'status'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream("daftar-jemaat.pdf");
    }

    // ===== DAFTAR KELUARGA =====
    public function daftarKeluarga(Request $request)
    {
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('cms_settings')->pluck('value', 'key');
        $keluarga = Keluarga::with('anggota', 'lingkunganSion')->orderBy('nama_kepala_keluarga')->get();

        $pdf = Pdf::loadView('print.daftar-keluarga', compact('keluarga', 'profil', 'settings'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream("daftar-keluarga.pdf");
    }

    // ===== WARTA MINGGUAN (FORMAT REFERENSI) =====
    public function wartaMingguan(Request $request)
    {
        $tanggalAwal = $request->get('dari', now()->startOfWeek()->toDateString());
        $tanggalAkhir = $request->get('sampai', now()->endOfWeek()->toDateString());
        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        // Penerimaan dari PS (per tanggal, grouped by jemaat)
        $psPeriode = \App\Models\TransaksiPS::with('jemaat')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')->orderBy('sumber')
            ->get();

        // Penerimaan dari KK
        $kkPeriode = \App\Models\TransaksiKK::with('jemaat')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')
            ->get();

        // Pengeluaran
        $pengeluaran = Transaksi::with('kodeAkun')
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')
            ->get();

        // Saldo calculation
        $tahun = date('Y');
        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
        $saldoData = [];
        $totalSaldo = 0;
        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')
                ->where('tanggal', '<=', $tanggalAkhir)->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')
                ->where('tanggal', '<=', $tanggalAkhir)->sum('jumlah');
            $akhir = $awal + $masuk - $keluar;
            $totalSaldo += $akhir;
            $saldoData[] = (object)[
                'kantong' => $k,
                'label' => match($k) { 'rutin'=>'DANA RUTIN','phbg'=>'DANA HBG','pembangunan'=>'DANA PEMBANGUNAN','cadangan'=>'DANA CADANGAN','diakonia_sosial'=>'DANA DIAKONAL SOSIAL','diakonia_kedukaan'=>'DANA DIAKONIA KEDUKAAN' },
                'akhir' => $akhir,
            ];
        }

        // Saldo minggu lalu
        $saldoMingguLalu = 0;
        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')
                ->where('tanggal', '<', $tanggalAwal)->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')
                ->where('tanggal', '<', $tanggalAwal)->sum('jumlah');
            $saldoMingguLalu += ($awal + $masuk - $keluar);
        }

        $totalPenerimaan = $psPeriode->sum('jumlah') + $kkPeriode->sum('jumlah');
        $totalPengeluaran = $pengeluaran->sum('jumlah');

        // PS per ibadah (for page 2: amplop table)
        $amplopPerIbadah = $psPeriode->where('sumber', 'jadwal')
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d') . '|' . ($item->jadwal_ibadah_id ?? 'kantor'));

        $pdf = Pdf::loadView('print.warta-mingguan-full', compact(
            'psPeriode', 'kkPeriode', 'pengeluaran', 'saldoData', 'totalSaldo',
            'saldoMingguLalu', 'totalPenerimaan', 'totalPengeluaran',
            'tanggalAwal', 'tanggalAkhir', 'profil', 'settings', 'amplopPerIbadah'
        ))->setPaper('a4', 'portrait');
        return $pdf->stream("warta-mingguan-{$tanggalAwal}.pdf");
    }

    // ===== RAPP GABUNGAN PDF =====
    public function rappGabungan(int $id)
    {
        $rapp = DB::table('pedoman_rapp')->where('id', $id)->first();
        if (!$rapp) abort(404);

        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $details = DB::table('pedoman_rapp_detail')
            ->join('kode_akun', 'pedoman_rapp_detail.kode_akun_id', '=', 'kode_akun.id')
            ->leftJoin('unit_pelayanan', 'kode_akun.nomor_up', '=', 'unit_pelayanan.nomor_up')
            ->where('pedoman_id', $id)
            ->select(
                'pedoman_rapp_detail.*',
                'kode_akun.kode', 'kode_akun.nama as nama_akun',
                'kode_akun.keterangan', 'kode_akun.tipe', 'kode_akun.nomor_up',
                'unit_pelayanan.inisial as detail_up_inisial',
                'unit_pelayanan.nama as detail_up_nama'
            )
            ->orderBy('kode_akun.tipe')
            ->orderBy('kode_akun.nomor_up')
            ->orderBy('pedoman_rapp_detail.urutan')
            ->get();

        $penerimaan = $details->where('tipe', 'penerimaan');
        $pengeluaran = $details->where('tipe', 'pengeluaran');

        $pdf = Pdf::loadView('print.rapp-gabungan', compact(
            'rapp', 'penerimaan', 'pengeluaran', 'profil', 'settings'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream("rapp-gabungan-{$rapp->tahun}.pdf");
    }

    // ===== JADWAL IBADAH MINGGUAN PDF =====
    public function jadwalMingguan(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal', now()->startOfWeek()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', now()->endOfWeek()->format('Y-m-d'));

        $jadwalList = \App\Models\JadwalIbadah::with(['petugas.petugas'])
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        $pdf = Pdf::loadView('print.jadwal-mingguan', compact(
            'jadwalList', 'tanggalAwal', 'tanggalAkhir', 'profil', 'settings'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream("jadwal-mingguan-{$tanggalAwal}.pdf");
    }

    // ===== KWITANSI =====
    public function kwitansi(Transaksi $transaksi)
    {
        $transaksi->load('kodeAkun', 'details', 'creator');

        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('cms_settings')->pluck('value', 'key')->toArray();

        $terbilang = \App\Helpers\Terbilang::rupiah($transaksi->jumlah);

        $pdf = Pdf::loadView('print.kwitansi', compact('transaksi', 'profil', 'settings', 'terbilang'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("kwitansi-{$transaksi->tipe}-{$transaksi->id}.pdf");
    }
}
