<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use App\Models\Transaksi;
use App\Models\UnitPelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    // --- Master Akun ZA6 ---
    public function masterAkun(Request $request)
    {
        $tipe = $request->get('tipe', 'semua');
        $up = $request->get('up');
        $search = $request->get('q');

        $query = KodeAkun::with('unitPelayanan')->where('aktif', true);

        if ($tipe !== 'semua') $query->where('tipe', $tipe);
        if ($up) $query->where('nomor_up', $up);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('sumber_lapangan', 'like', "%{$search}%");
            });
        }

        $akun = $query->orderBy('kode')->paginate(25)->withQueryString();
        $units = UnitPelayanan::orderBy('nomor_up')->get();

        $stats = [
            'total' => KodeAkun::where('aktif', true)->count(),
            'penerimaan' => KodeAkun::penerimaan()->where('aktif', true)->count(),
            'pengeluaran' => KodeAkun::pengeluaran()->where('aktif', true)->count(),
        ];

        return view('keuangan.master-akun', compact('akun', 'units', 'stats', 'tipe', 'up', 'search'));
    }

    // --- Transaksi ---
    public function transaksi(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $tipe = $request->get('tipe', 'semua');

        $query = Transaksi::with('kodeAkun', 'pembuatUser', 'creator')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan);

        if ($tipe !== 'semua') $query->where('tipe', $tipe);

        $transaksi = $query->orderByDesc('tanggal')->paginate(20)->withQueryString();

        $summary = [
            'penerimaan' => Transaksi::where('tahun', $tahun)->where('bulan', $bulan)->where('tipe', 'penerimaan')->sum('jumlah'),
            'pengeluaran' => Transaksi::where('tahun', $tahun)->where('bulan', $bulan)->where('tipe', 'pengeluaran')->sum('jumlah'),
        ];

        $akunPenerimaan = KodeAkun::penerimaan()->where('aktif', true)->orderBy('kode')->get();
        $akunPengeluaran = KodeAkun::pengeluaran()->where('aktif', true)->orderBy('kode')->get();

        return view('keuangan.transaksi', compact('transaksi', 'summary', 'bulan', 'tahun', 'tipe', 'akunPenerimaan', 'akunPengeluaran'));
    }

    public function storeTransaksi(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_akun_id' => 'required|exists:kode_akun,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $akun = KodeAkun::findOrFail($request->kode_akun_id);

        // Determine kantong_dana based on kode akun UP
        $kantong = 'rutin';
        if (in_array($akun->nomor_up, [8])) $kantong = 'phbg';
        elseif (in_array($akun->nomor_up, [9])) $kantong = 'pembangunan';
        elseif (in_array($akun->nomor_up, [10])) $kantong = 'diakonia_sosial';
        elseif (in_array($akun->nomor_up, [11])) $kantong = 'diakonia_kedukaan';
        // Cadangan is I(APn).01.03 specifically
        if ($akun->kode === 'I(APn).01.03') $kantong = 'cadangan';

        $tanggal = \Carbon\Carbon::parse($request->tanggal);

        $transaksi = Transaksi::create([
            'tanggal' => $tanggal,
            'kode_akun_id' => $akun->id,
            'tipe' => $akun->tipe,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'nama_penyetor' => $request->nama_penyetor,
            'jenis_ibadah' => $request->jenis_ibadah,
            'bulan' => $tanggal->month,
            'tahun' => $tanggal->year,
            'kantong_dana' => $kantong,
            'status' => 'draft',
            'dibuat_oleh' => auth()->id(),
        ]);

        // Save detail items (ATK breakdown)
        if ($request->has('detail_uraian')) {
            foreach ($request->detail_uraian as $i => $uraian) {
                if (empty($uraian)) continue;
                $qty = $request->detail_qty[$i] ?? 1;
                $harga = $request->detail_harga[$i] ?? 0;
                $transaksi->details()->create([
                    'uraian' => $uraian,
                    'qty' => $qty,
                    'satuan' => $request->detail_satuan[$i] ?? null,
                    'harga_satuan' => $harga,
                    'jumlah' => $qty * $harga,
                ]);
            }
        }

        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function destroyTransaksi(Transaksi $transaksi)
    {
        $transaksi->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function verifyTransaksi(Transaksi $transaksi)
    {
        $transaksi->update(['status' => 'verified']);
        return back()->with('success', 'Transaksi berhasil divalidasi.');
    }

    public function approveTransaksi(Transaksi $transaksi)
    {
        $transaksi->update(['status' => 'approved']);
        return back()->with('success', 'Transaksi berhasil di-approve.');
    }

    // --- Saldo ---
    public function saldo(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
        $saldoData = [];

        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')->sum('jumlah');
            $saldoData[] = [
                'kantong' => $k,
                'label' => match($k) {
                    'rutin' => 'Dana Rutin MPH',
                    'phbg' => 'Dana PHBG',
                    'pembangunan' => 'Dana Pembangunan P4A',
                    'cadangan' => 'Dana Cadangan',
                    'diakonia_sosial' => 'Dana Diakonia Sosial',
                    'diakonia_kedukaan' => 'Dana Diakonia Kedukaan',
                },
                'awal' => $awal,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'akhir' => $awal + $masuk - $keluar,
            ];
        }

        return view('keuangan.saldo', compact('saldoData', 'tahun'));
    }

    public function updateSaldo(Request $request)
    {
        $tahun = $request->tahun;
        foreach ($request->saldo as $kantong => $amount) {
            DB::table('saldo_awal')->updateOrInsert(
                ['tahun' => $tahun, 'kantong_dana' => $kantong],
                ['saldo' => $amount, 'updated_at' => now()]
            );
        }
        return back()->with('success', 'Saldo awal berhasil diperbarui.');
    }

    // --- Overview / Manajemen Keuangan ---
    public function index(Request $request)
    {
        $tahun = (int)$request->get('tahun', date('Y'));
        $tab = $request->get('tab', 'ringkasan');

        // === RINGKASAN SALDO ===
        $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
        $saldoRingkas = [];
        $totalSaldo = 0;
        foreach ($kantongList as $k) {
            $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
            $masuk = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')->sum('jumlah');
            $keluar = Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')->sum('jumlah');
            $akhir = $awal + $masuk - $keluar;
            $totalSaldo += $akhir;
            $saldoRingkas[] = [
                'kantong' => $k,
                'label' => match($k) {
                    'rutin' => 'Dana Rutin MPH', 'phbg' => 'Dana PHBG',
                    'pembangunan' => 'Dana Pembangunan P4A', 'cadangan' => 'Dana Cadangan',
                    'diakonia_sosial' => 'Dana Diakonia Sosial', 'diakonia_kedukaan' => 'Dana Diakonia Kedukaan',
                },
                'akhir' => $akhir,
            ];
        }
        $totalPenerimaan = Transaksi::where('tahun', $tahun)->where('tipe', 'penerimaan')->sum('jumlah');
        $totalPengeluaran = Transaksi::where('tahun', $tahun)->where('tipe', 'pengeluaran')->sum('jumlah');

        // === RAPP REALISASI ===
        $rappRealisasi = [];
        $rappApproved = DB::table('pedoman_rapp')
            ->where('tahun', $tahun)->where('status', 'disetujui')->first();

        if ($rappApproved) {
            $details = DB::table('pedoman_rapp_detail as d')
                ->join('kode_akun as ka', 'd.kode_akun_id', '=', 'ka.id')
                ->where('d.pedoman_id', $rappApproved->id)
                ->select('d.*', 'ka.kode', 'ka.nama as nama_akun', 'ka.tipe', 'ka.nomor_up')
                ->orderBy('ka.kode')
                ->get();

            foreach ($details as $det) {
                $anggaran = $det->volume * $det->nominal;
                $aktual = Transaksi::where('tahun', $tahun)
                    ->where('kode_akun_id', $det->kode_akun_id)->sum('jumlah');
                $persen = $anggaran > 0 ? round(($aktual / $anggaran) * 100, 1) : 0;

                // Color logic: penerimaan hijau jika tercapai, pengeluaran merah jika melebihi
                if ($det->tipe === 'penerimaan') {
                    $barColor = $persen >= 100 ? 'success' : 'warning';
                    $status = $persen >= 100 ? 'Tercapai' : 'Belum Tercapai';
                } else {
                    $barColor = $persen > 100 ? 'danger' : 'info';
                    $status = $persen > 100 ? 'Melebihi Budget' : 'Dalam Budget';
                }

                $rappRealisasi[] = [
                    'kode' => $det->kode,
                    'nama' => $det->nama_akun,
                    'tipe' => $det->tipe,
                    'anggaran' => $anggaran,
                    'aktual' => $aktual,
                    'persen' => min($persen, 200),
                    'barColor' => $barColor,
                    'status' => $status,
                ];
            }
        }

        // === CHART DATA (monthly) ===
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = [
                'bulan' => $m,
                'penerimaan' => Transaksi::where('tipe', 'penerimaan')->where('tahun', $tahun)->where('bulan', $m)->sum('jumlah'),
                'pengeluaran' => Transaksi::where('tipe', 'pengeluaran')->where('tahun', $tahun)->where('bulan', $m)->sum('jumlah'),
            ];
        }

        return view('keuangan.index', compact(
            'tahun', 'tab', 'saldoRingkas', 'totalSaldo', 'totalPenerimaan', 'totalPengeluaran',
            'rappRealisasi', 'rappApproved', 'chartData'
        ));
    }
}
