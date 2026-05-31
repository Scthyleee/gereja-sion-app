<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPS;
use App\Models\TransaksiKK;
use App\Models\WargaJemaat;
use App\Models\KodeAkun;
use App\Models\Transaksi;
use App\Models\RiwayatJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiPSController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $jenisPs = $request->get('jenis_ps', 'semua');
        $sumber = $request->get('sumber', 'semua');

        $query = TransaksiPS::with('jemaat', 'kodeAkun', 'creator')
            ->where('bulan', $bulan);

        if ($jenisPs !== 'semua') $query->where('jenis_ps', $jenisPs);
        if ($sumber !== 'semua') $query->where('sumber', $sumber);

        $transaksi = $query->orderByDesc('tanggal')->paginate(25)->withQueryString();

        // Summary per jenis PS
        $summary = TransaksiPS::where('bulan', $bulan)
            ->select('jenis_ps', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as jumlah_trx'))
            ->groupBy('jenis_ps')->orderBy('jenis_ps')->get();

        $totalBulan = TransaksiPS::where('bulan', $bulan)->sum('jumlah');

        // Jenis PS config
        $jenisConfig = DB::table('jenis_ps_config')->where('aktif', true)->orderBy('urutan')->get();

        return view('keuangan.ps.index', compact('transaksi', 'summary', 'totalBulan', 'bulan', 'jenisPs', 'sumber', 'jenisConfig'));
    }

    public function create(Request $request)
    {
        $jenisConfig = DB::table('jenis_ps_config')->where('aktif', true)->orderBy('urutan')->get();
        $sumber = $request->get('sumber', 'kantor');

        return view('keuangan.ps.create', compact('jenisConfig', 'sumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.jemaat_id' => 'required|integer',
            'items.*.rows' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal);
            $bulan = $tanggal->format('Y-m');

            foreach ($request->items as $item) {
                $jemaatId = $item['jemaat_id'];

                foreach ($item['rows'] as $row) {
                    if (empty($row['jenis_ps']) || empty($row['jumlah']) || $row['jumlah'] <= 0) continue;

                    // Look up kode akun from config
                    $config = DB::table('jenis_ps_config')->where('nama', $row['jenis_ps'])->first();
                    $kodeAkun = $config ? KodeAkun::where('kode', $config->kode_akun)->first() : null;
                    $kantongDana = $config->kantong_dana ?? 'rutin';

                    // Generate kuitansi
                    $lastKuitansi = TransaksiPS::where('bulan', $bulan)->max('no_kuitansi');
                    $nextNum = 1;
                    if ($lastKuitansi) {
                        $parts = explode('-', $lastKuitansi);
                        $nextNum = ((int)end($parts)) + 1;
                    }
                    $noKuitansi = sprintf('PS-%s-%04d', str_replace('-', '-', $bulan), $nextNum);

                    // Create transaksi_ps
                    $ps = TransaksiPS::create([
                        'jemaat_id' => $jemaatId,
                        'tanggal' => $tanggal,
                        'bulan' => $bulan,
                        'jenis_ps' => $row['jenis_ps'],
                        'kode_akun_id' => $kodeAkun?->id,
                        'kantong_dana' => $kantongDana,
                        'jumlah' => $row['jumlah'],
                        'diterima_oleh' => $request->diterima_oleh ?? auth()->user()->nama,
                        'no_kuitansi' => $noKuitansi,
                        'status' => 'draft',
                        'sumber' => $request->sumber ?? 'kantor',
                        'jadwal_ibadah_id' => $request->jadwal_ibadah_id,
                        'created_by' => auth()->id(),
                    ]);

                    // Sync to main transaksi table
                    $trx = Transaksi::create([
                        'tanggal' => $tanggal,
                        'kode_akun_id' => $kodeAkun?->id ?? 1,
                        'tipe' => 'penerimaan',
                        'jenis_ibadah' => $request->sumber === 'jadwal' ? 'Ibadah' : null,
                        'nama_penyetor' => WargaJemaat::where('id_warga', $jemaatId)->value('nama_lengkap'),
                        'jumlah' => $row['jumlah'],
                        'keterangan' => "PS: {$row['jenis_ps']} - {$noKuitansi}",
                        'bulan' => $tanggal->month,
                        'tahun' => $tanggal->year,
                        'kantong_dana' => $kantongDana,
                        'status' => 'draft',
                        'dibuat_oleh' => auth()->id(),
                    ]);
                    $ps->update(['transaksi_id' => $trx->id]);
                }
            }
        });

        return back()->with('success', 'Persembahan syukur berhasil dicatat.');
    }

    public function verify(Request $request, $id)
    {
        $ps = TransaksiPS::findOrFail($id);
        $ps->update(['status' => 'verified']);

        // Auto-archive to riwayat_jemaat
        if ($ps->jemaat_id) {
            RiwayatJemaat::create([
                'jemaat_id' => $ps->jemaat_id,
                'tahun' => $ps->tanggal->year,
                'bulan' => $ps->bulan,
                'jenis_ps' => $ps->jenis_ps,
                'kode_akun' => $ps->kodeAkun?->kode,
                'jumlah' => $ps->jumlah,
                'transaksi_ps_id' => $ps->id,
            ]);
        }

        // Also verify linked transaksi
        if ($ps->transaksi_id) {
            Transaksi::where('id', $ps->transaksi_id)->update(['status' => 'verified']);
        }

        return back()->with('success', 'Transaksi PS diverifikasi.');
    }

    public function destroy($id)
    {
        $ps = TransaksiPS::findOrFail($id);
        if ($ps->transaksi_id) {
            Transaksi::where('id', $ps->transaksi_id)->delete();
        }
        $ps->delete();
        return back()->with('success', 'Transaksi PS dihapus.');
    }

    // API: search jemaat for autocomplete
    public function searchJemaat(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $jemaat = WargaJemaat::where('status_jemaat', 'aktif')
            ->where(function ($query) use ($q) {
                $query->where('nama_lengkap', 'like', "%{$q}%")
                      ->orWhere('no_kk', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get(['id_warga', 'nama_lengkap', 'no_kk', 'lingkungan', 'keluarga_id']);

        // Add PS info for current month
        $bulanIni = date('Y-m');
        $result = $jemaat->map(function ($j) use ($bulanIni) {
            $psThisMonth = TransaksiPS::where('jemaat_id', $j->id_warga)
                ->where('bulan', $bulanIni)
                ->select('jenis_ps', DB::raw('SUM(jumlah) as total'))
                ->groupBy('jenis_ps')->pluck('total', 'jenis_ps');

            $lastSetor = TransaksiPS::where('jemaat_id', $j->id_warga)
                ->orderByDesc('tanggal')->first(['tanggal', 'jumlah']);

            return [
                'id' => $j->id_warga,
                'nama' => $j->nama_lengkap,
                'no_kk' => $j->no_kk,
                'lingkungan' => $j->lingkungan,
                'ps_bulan_ini' => $psThisMonth,
                'terakhir_setor' => $lastSetor ? [
                    'tanggal' => $lastSetor->tanggal->format('d M Y'),
                    'jumlah' => $lastSetor->jumlah,
                ] : null,
            ];
        });

        return response()->json($result);
    }
}
