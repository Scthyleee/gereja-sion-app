<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\JadwalIbadah;
use App\Models\WargaJemaat;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = date('Y');

        $totalPenerimaan = Transaksi::where('tipe', 'penerimaan')->where('tahun', $tahun)->sum('jumlah');
        $totalPengeluaran = Transaksi::where('tipe', 'pengeluaran')->where('tahun', $tahun)->sum('jumlah');

        $saldoAwal = DB::table('saldo_awal')->where('tahun', $tahun)->sum('saldo');
        $saldoBerjalan = $saldoAwal + $totalPenerimaan - $totalPengeluaran;

        $totalJemaat = WargaJemaat::where('status_jemaat', 'aktif')->count();

        $jadwalMendatang = JadwalIbadah::where('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->limit(5)
            ->get();

        // Warta terbaru
        $wartaTerbaru = DB::table('warta_mingguan')
            ->orderByDesc('tanggal_awal')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'tahun', 'totalPenerimaan', 'totalPengeluaran', 'saldoBerjalan',
            'totalJemaat', 'jadwalMendatang', 'wartaTerbaru'
        ));
    }
}
