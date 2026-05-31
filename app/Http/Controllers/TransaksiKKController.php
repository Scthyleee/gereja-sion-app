<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKK;
use App\Models\KodeAkun;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiKKController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));

        $query = TransaksiKK::with('kodeAkun', 'jemaat', 'creator')
            ->where('bulan', $bulan);

        $transaksi = $query->orderByDesc('tanggal')->paginate(25)->withQueryString();

        $summary = TransaksiKK::where('bulan', $bulan)
            ->select('jenis_ibadah', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('jenis_ibadah')->orderBy('jenis_ibadah')->get();

        $totalBulan = TransaksiKK::where('bulan', $bulan)->sum('jumlah');

        return view('keuangan.kk.index', compact('transaksi', 'summary', 'totalBulan', 'bulan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_ibadah' => 'required|string|max:100',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $tanggal = \Carbon\Carbon::parse($request->tanggal);
        $bulan = $tanggal->format('Y-m');
        $mingguKe = TransaksiKK::calculateMingguKe($tanggal);

        // Default kode akun for KK = I(APn).01.01
        $kodeAkun = KodeAkun::where('kode', 'I(APn).01.01')->first();

        DB::transaction(function () use ($request, $tanggal, $bulan, $mingguKe, $kodeAkun) {
            $kk = TransaksiKK::create([
                'tanggal' => $tanggal,
                'bulan' => $bulan,
                'minggu_ke' => $mingguKe,
                'jenis_ibadah' => $request->jenis_ibadah,
                'kode_akun_id' => $kodeAkun?->id,
                'jumlah' => $request->jumlah,
                'jml_kantong' => $request->jml_kantong ?? 0,
                'petugas_hitung' => $request->petugas_hitung,
                'diterima_oleh' => $request->diterima_oleh ?? auth()->user()->nama,
                'keterangan' => $request->keterangan,
                'status' => 'draft',
                'jemaat_id' => $request->jemaat_id, // nullable, for KK Online
                'created_by' => auth()->id(),
            ]);

            // Sync to main transaksi
            $trx = Transaksi::create([
                'tanggal' => $tanggal,
                'kode_akun_id' => $kodeAkun?->id ?? 1,
                'tipe' => 'penerimaan',
                'jenis_ibadah' => $request->jenis_ibadah,
                'jumlah' => $request->jumlah,
                'keterangan' => "KK: {$request->jenis_ibadah}",
                'bulan' => $tanggal->month,
                'tahun' => $tanggal->year,
                'kantong_dana' => 'rutin',
                'status' => 'draft',
                'dibuat_oleh' => auth()->id(),
            ]);
            $kk->update(['transaksi_id' => $trx->id]);
        });

        return back()->with('success', 'Kolekte berhasil dicatat.');
    }

    public function verify($id)
    {
        $kk = TransaksiKK::findOrFail($id);
        $kk->update(['status' => 'verified']);
        if ($kk->transaksi_id) {
            Transaksi::where('id', $kk->transaksi_id)->update(['status' => 'verified']);
        }
        return back()->with('success', 'Transaksi KK diverifikasi.');
    }

    public function destroy($id)
    {
        $kk = TransaksiKK::findOrFail($id);
        if ($kk->transaksi_id) {
            Transaksi::where('id', $kk->transaksi_id)->delete();
        }
        $kk->delete();
        return back()->with('success', 'Transaksi KK dihapus.');
    }
}
