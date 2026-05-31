<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

class ResetSaldoTahunan extends Command
{
    protected $signature = 'saldo:reset-yearly {--tahun= : Tahun berjalan (default: tahun ini)}';
    protected $description = 'Hitung saldo akhir tahun berjalan dan set sebagai saldo awal tahun baru';

    public function handle()
    {
        $tahunIni = $this->option('tahun') ?: date('Y');
        $tahunBaru = $tahunIni + 1;

        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];

        $this->info("Reset saldo: {$tahunIni} → {$tahunBaru}");

        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahunIni)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahunIni)->where('kantong_dana', $k)->where('tipe', 'penerimaan')->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahunIni)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')->sum('jumlah');
            $akhir = $awal + $masuk - $keluar;

            DB::table('saldo_awal')->updateOrInsert(
                ['tahun' => $tahunBaru, 'kantong_dana' => $k],
                ['saldo' => $akhir, 'updated_at' => now()]
            );

            $this->line("  {$k}: Rp " . number_format($akhir, 0, ',', '.'));
        }

        $this->info("Saldo awal {$tahunBaru} berhasil diset.");
    }
}
