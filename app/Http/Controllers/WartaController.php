<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use App\Models\Transaksi;
use App\Models\WartaMingguan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WartaController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        // If "Simpan sebagai Warta" clicked
        if ($request->get('action') === 'simpan' && $tanggalAwal && $tanggalAkhir) {
            return $this->store($request);
        }

        // Preview summary
        $previewPenerimaan = 0;
        $previewPengeluaran = 0;
        if ($tanggalAwal && $tanggalAkhir) {
            $previewPenerimaan = Transaksi::where('tipe', 'penerimaan')
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->whereIn('status', ['verified', 'approved'])
                ->sum('jumlah');
            $previewPengeluaran = Transaksi::where('tipe', 'pengeluaran')
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->whereIn('status', ['verified', 'approved'])
                ->sum('jumlah');
        }

        // List saved wartas
        $wartas = WartaMingguan::orderByDesc('tanggal_awal')->get();

        return view('warta.index', compact('wartas', 'previewPenerimaan', 'previewPengeluaran'));
    }

    /**
     * Save a new warta entry with fixed date range
     */
    private function store(Request $request)
    {
        $tanggalAwal = Carbon::parse($request->tanggal_awal);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);

        $penerimaan = Transaksi::where('tipe', 'penerimaan')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->whereIn('status', ['verified', 'approved'])
            ->sum('jumlah');

        $pengeluaran = Transaksi::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->whereIn('status', ['verified', 'approved'])
            ->sum('jumlah');

        // Get saldo from previous warta
        $prevWarta = WartaMingguan::where('tanggal_akhir', '<', $tanggalAwal)
            ->orderByDesc('tanggal_akhir')
            ->first();
        $saldoAwal = $prevWarta ? $prevWarta->saldo_akhir_minggu : 0;

        // If no previous warta, try saldo_awal table
        if (!$prevWarta) {
            $saldoAwal = DB::table('saldo_awal')
                ->where('tahun', $tanggalAwal->year)
                ->sum('saldo');
        }

        $warta = WartaMingguan::create([
            'minggu_ke'          => (int) ceil($tanggalAwal->day / 7),
            'bulan'              => $tanggalAwal->month,
            'tahun'              => $tanggalAwal->year,
            'tanggal_awal'       => $tanggalAwal,
            'tanggal_akhir'      => $tanggalAkhir,
            'saldo_awal_minggu'  => $saldoAwal,
            'total_penerimaan'   => $penerimaan,
            'total_pengeluaran'  => $pengeluaran,
            'saldo_akhir_minggu' => $saldoAwal + $penerimaan - $pengeluaran,
            'generated_by'       => auth()->id(),
        ]);

        return redirect()->route('warta.show', $warta->id)
            ->with('success', 'Warta berhasil disimpan.');
    }

    /**
     * Refresh an existing warta — recalculate from transaksi
     */
    public function refresh(int $id)
    {
        $warta = WartaMingguan::findOrFail($id);

        $penerimaan = Transaksi::where('tipe', 'penerimaan')
            ->whereBetween('tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->whereIn('status', ['verified', 'approved'])
            ->sum('jumlah');

        $pengeluaran = Transaksi::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->whereIn('status', ['verified', 'approved'])
            ->sum('jumlah');

        $prevWarta = WartaMingguan::where('tanggal_akhir', '<', $warta->tanggal_awal)
            ->where('id', '!=', $warta->id)
            ->orderByDesc('tanggal_akhir')
            ->first();
        $saldoAwal = $prevWarta ? $prevWarta->saldo_akhir_minggu : 0;

        if (!$prevWarta) {
            $saldoAwal = DB::table('saldo_awal')
                ->where('tahun', $warta->tanggal_awal->year)
                ->sum('saldo');
        }

        $warta->update([
            'saldo_awal_minggu'  => $saldoAwal,
            'total_penerimaan'   => $penerimaan,
            'total_pengeluaran'  => $pengeluaran,
            'saldo_akhir_minggu' => $saldoAwal + $penerimaan - $pengeluaran,
        ]);

        return back()->with('success', 'Warta berhasil di-refresh.');
    }

    /**
     * Delete a warta entry
     */
    public function destroy(int $id)
    {
        WartaMingguan::findOrFail($id)->delete();
        return redirect()->route('warta.index')->with('success', 'Warta berhasil dihapus.');
    }

    /**
     * Show warta detail with full breakdown
     */
    public function show(int $id)
    {
        $warta = WartaMingguan::findOrFail($id);

        // Get detailed penerimaan with date
        $penerimaan = Transaksi::with('kodeAkun')
            ->where('tipe', 'penerimaan')
            ->whereBetween('tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->whereIn('status', ['verified', 'approved'])
            ->orderBy('tanggal')
            ->get();

        // Get detailed pengeluaran with date
        $pengeluaran = Transaksi::with('kodeAkun')
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->whereIn('status', ['verified', 'approved'])
            ->orderBy('tanggal')
            ->get();

        // Saldo breakdown per kantong (matching Saldo feature labels)
        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
        $kantongLabels = [
            'rutin' => 'Dana Rutin MPH',
            'phbg' => 'Dana PHBG',
            'pembangunan' => 'Dana Pembangunan P4A',
            'cadangan' => 'Dana Cadangan',
            'diakonia_sosial' => 'Dana Diakonia Sosial',
            'diakonia_kedukaan' => 'Dana Diakonia Kedukaan',
        ];

        // Amplop PS data per keluarga (from jadwal_amplop)
        $amplopPS = DB::table('jadwal_amplop')
            ->join('jadwal_amplop_detail', 'jadwal_amplop.id', '=', 'jadwal_amplop_detail.amplop_id')
            ->join('jadwal_ibadah', 'jadwal_amplop.jadwal_id', '=', 'jadwal_ibadah.id_jadwal')
            ->leftJoin('warga_jemaat', 'jadwal_amplop.warga_id', '=', 'warga_jemaat.id_warga')
            ->leftJoin('kode_akun', 'jadwal_amplop_detail.kode_akun_id', '=', 'kode_akun.id')
            ->whereBetween('jadwal_ibadah.tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->select(
                'jadwal_amplop.warga_id',
                DB::raw('COALESCE(warga_jemaat.nama_lengkap, jadwal_amplop.nama_pemberi) as nama_lengkap'),
                DB::raw('COALESCE(kode_akun.keterangan, kode_akun.nama) as jenis_ps'),
                DB::raw('SUM(jadwal_amplop_detail.nominal) as total')
            )
            ->groupBy('jadwal_amplop.warga_id', DB::raw('COALESCE(warga_jemaat.nama_lengkap, jadwal_amplop.nama_pemberi)'), DB::raw('COALESCE(kode_akun.keterangan, kode_akun.nama)'))
            ->orderBy(DB::raw('COALESCE(warga_jemaat.nama_lengkap, jadwal_amplop.nama_pemberi)'))
            ->get()
            ->groupBy('warga_id');

        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        // Insentif per jadwal ibadah (grouped)
        $insentifPerJadwal = DB::table('jadwal_ibadah')
            ->join('jadwal_petugas', 'jadwal_ibadah.id_jadwal', '=', 'jadwal_petugas.jadwal_id')
            ->leftJoin('petugas', 'jadwal_petugas.petugas_id', '=', 'petugas.id_petugas')
            ->whereBetween('jadwal_ibadah.tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->where('jadwal_petugas.nominal_insentif', '>', 0)
            ->select(
                'jadwal_ibadah.id_jadwal', 'jadwal_ibadah.jenis_ibadah', 'jadwal_ibadah.tanggal', 'jadwal_ibadah.waktu',
                'jadwal_petugas.jenis_tugas',
                DB::raw('COALESCE(jadwal_petugas.nama_external, petugas.nama_petugas) as nama_petugas'),
                'jadwal_petugas.nominal_insentif',
                DB::raw('CASE WHEN jadwal_petugas.is_external = 1 THEN "Luar" ELSE "Sion" END as asal')
            )
            ->orderBy('jadwal_ibadah.tanggal')
            ->orderBy('jadwal_petugas.jenis_tugas')
            ->get()
            ->groupBy('id_jadwal');

        return view('warta.show', compact('warta', 'penerimaan', 'pengeluaran', 'amplopPS', 'kantongLabels', 'profil', 'settings', 'insentifPerJadwal'));
    }

    /**
     * Print warta as PDF
     */
    public function print(int $id)
    {
        $warta = WartaMingguan::findOrFail($id);

        $penerimaan = Transaksi::with('kodeAkun')
            ->where('tipe', 'penerimaan')
            ->whereBetween('tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->whereIn('status', ['verified', 'approved'])
            ->orderBy('tanggal')
            ->get();

        $pengeluaran = Transaksi::with('kodeAkun')
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->whereIn('status', ['verified', 'approved'])
            ->orderBy('tanggal')
            ->get();

        // Amplop PS per keluarga
        $amplopPS = DB::table('jadwal_amplop')
            ->join('jadwal_amplop_detail', 'jadwal_amplop.id', '=', 'jadwal_amplop_detail.amplop_id')
            ->join('jadwal_ibadah', 'jadwal_amplop.jadwal_id', '=', 'jadwal_ibadah.id_jadwal')
            ->leftJoin('warga_jemaat', 'jadwal_amplop.warga_id', '=', 'warga_jemaat.id_warga')
            ->leftJoin('kode_akun', 'jadwal_amplop_detail.kode_akun_id', '=', 'kode_akun.id')
            ->whereBetween('jadwal_ibadah.tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->select(
                'jadwal_amplop.warga_id',
                DB::raw('COALESCE(warga_jemaat.nama_lengkap, jadwal_amplop.nama_pemberi) as nama_lengkap'),
                DB::raw('COALESCE(kode_akun.keterangan, kode_akun.nama) as jenis_ps'),
                DB::raw('SUM(jadwal_amplop_detail.nominal) as total')
            )
            ->groupBy('jadwal_amplop.warga_id', DB::raw('COALESCE(warga_jemaat.nama_lengkap, jadwal_amplop.nama_pemberi)'), DB::raw('COALESCE(kode_akun.keterangan, kode_akun.nama)'))
            ->orderBy(DB::raw('COALESCE(warga_jemaat.nama_lengkap, jadwal_amplop.nama_pemberi)'))
            ->get()
            ->groupBy('warga_id');

        $kantongLabels = [
            'rutin' => 'Dana Rutin MPH',
            'phbg' => 'Dana PHBG',
            'pembangunan' => 'Dana Pembangunan P4A',
            'cadangan' => 'Dana Cadangan',
            'diakonia_sosial' => 'Dana Diakonia Sosial',
            'diakonia_kedukaan' => 'Dana Diakonia Kedukaan',
        ];

        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');

        // Insentif per jadwal ibadah (grouped)
        $insentifPerJadwal = DB::table('jadwal_ibadah')
            ->join('jadwal_petugas', 'jadwal_ibadah.id_jadwal', '=', 'jadwal_petugas.jadwal_id')
            ->leftJoin('petugas', 'jadwal_petugas.petugas_id', '=', 'petugas.id_petugas')
            ->whereBetween('jadwal_ibadah.tanggal', [$warta->tanggal_awal, $warta->tanggal_akhir])
            ->where('jadwal_petugas.nominal_insentif', '>', 0)
            ->select(
                'jadwal_ibadah.id_jadwal', 'jadwal_ibadah.jenis_ibadah', 'jadwal_ibadah.tanggal', 'jadwal_ibadah.waktu',
                'jadwal_petugas.jenis_tugas',
                DB::raw('COALESCE(jadwal_petugas.nama_external, petugas.nama_petugas) as nama_petugas'),
                'jadwal_petugas.nominal_insentif',
                DB::raw('CASE WHEN jadwal_petugas.is_external = 1 THEN "Luar" ELSE "Sion" END as asal')
            )
            ->orderBy('jadwal_ibadah.tanggal')
            ->orderBy('jadwal_petugas.jenis_tugas')
            ->get()
            ->groupBy('id_jadwal');

        $pdf = Pdf::loadView('print.warta-mingguan', compact('warta', 'penerimaan', 'pengeluaran', 'amplopPS', 'kantongLabels', 'profil', 'settings', 'insentifPerJadwal'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("warta-{$warta->tanggal_awal->format('Ymd')}-{$warta->tanggal_akhir->format('Ymd')}.pdf");
    }
}
