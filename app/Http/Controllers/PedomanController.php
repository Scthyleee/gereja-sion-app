<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PedomanController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pedoman');
        $filterUp = $request->get('up', '');

        $pedoman = DB::table('pedoman')->orderByDesc('created_at')->get();

        $rappQuery = DB::table('pedoman_rapp')
            ->leftJoin('unit_pelayanan', 'pedoman_rapp.unit_pelayanan_id', '=', 'unit_pelayanan.nomor_up')
            ->select('pedoman_rapp.*', 'unit_pelayanan.inisial as up_inisial', 'unit_pelayanan.nama as up_nama');

        if ($filterUp) {
            $rappQuery->where('pedoman_rapp.unit_pelayanan_id', $filterUp);
        }
        $rapp = $rappQuery->orderByDesc('tahun')->orderBy('unit_pelayanan_id')->get();

        $akunList = KodeAkun::where('aktif', true)->orderBy('kode')->get();
        $unitList = DB::table('unit_pelayanan')->where('aktif', true)->orderBy('nomor_up')->get();

        return view('pedoman.index', compact('tab', 'pedoman', 'rapp', 'akunList', 'unitList', 'filterUp'));
    }

    public function storePedoman(Request $request)
    {
        $request->validate(['judul' => 'required|string|max:200']);

        $data = $request->only('judul', 'kategori', 'tahun', 'versi', 'deskripsi');
        $data['uploaded_by'] = auth()->id();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('pedoman', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
        }

        DB::table('pedoman')->insert($data);
        return back()->with('success', 'Pedoman berhasil ditambahkan.');
    }

    public function storeRapp(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'tahun' => 'required|integer|min:2020',
            'unit_pelayanan_id' => 'required|integer',
        ]);

        // Check if RAPP already exists for this UP+year
        $exists = DB::table('pedoman_rapp')
            ->where('tahun', $request->tahun)
            ->where('unit_pelayanan_id', $request->unit_pelayanan_id)
            ->where('is_gabungan', false)
            ->exists();

        if ($exists) {
            return back()->with('error', 'RAPP untuk UP ini dan tahun ini sudah ada.');
        }

        $id = DB::table('pedoman_rapp')->insertGetId([
            'judul' => $request->judul,
            'tahun' => $request->tahun,
            'unit_pelayanan_id' => $request->unit_pelayanan_id,
            'is_gabungan' => false,
            'status' => 'draft',
            'catatan' => $request->catatan,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Auto-populate ALL kode akun for this UP
        $allAkun = KodeAkun::where('nomor_up', $request->unit_pelayanan_id)
            ->where('aktif', true)->orderBy('tipe')->orderBy('kode')->get();

        $urutan = 0;
        foreach ($allAkun as $akun) {
            DB::table('pedoman_rapp_detail')->insert([
                'pedoman_id' => $id,
                'kode_akun_id' => $akun->id,
                'keterangan' => '',
                'volume' => 1,
                'nominal' => 0,
                'urutan' => $urutan++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Also insert manually selected akun with values if provided
        if ($request->has('akun_ids')) {
            foreach ($request->akun_ids as $i => $akunId) {
                // Update if already exists from auto-populate
                $existing = DB::table('pedoman_rapp_detail')
                    ->where('pedoman_id', $id)->where('kode_akun_id', $akunId)->first();
                if ($existing) {
                    DB::table('pedoman_rapp_detail')->where('id', $existing->id)->update([
                        'keterangan' => $request->keterangans[$i] ?? '',
                        'volume' => $request->volumes[$i] ?? 1,
                        'nominal' => $request->nominals[$i] ?? 0,
                    ]);
                } else {
                    DB::table('pedoman_rapp_detail')->insert([
                        'pedoman_id' => $id,
                        'kode_akun_id' => $akunId,
                        'keterangan' => $request->keterangans[$i] ?? '',
                        'volume' => $request->volumes[$i] ?? 1,
                        'nominal' => $request->nominals[$i] ?? 0,
                        'urutan' => $urutan++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('pedoman.rapp.show', $id)->with('success', 'RAPP berhasil dibuat dengan semua kode akun.');
    }

    public function showRapp(int $id)
    {
        $rapp = DB::table('pedoman_rapp')
            ->leftJoin('unit_pelayanan', 'pedoman_rapp.unit_pelayanan_id', '=', 'unit_pelayanan.nomor_up')
            ->select('pedoman_rapp.*', 'unit_pelayanan.inisial as up_inisial', 'unit_pelayanan.nama as up_nama')
            ->where('pedoman_rapp.id', $id)->first();
        if (!$rapp) abort(404);

        $details = DB::table('pedoman_rapp_detail')
            ->join('kode_akun', 'pedoman_rapp_detail.kode_akun_id', '=', 'kode_akun.id')
            ->leftJoin('unit_pelayanan', 'kode_akun.nomor_up', '=', 'unit_pelayanan.nomor_up')
            ->where('pedoman_id', $id)
            ->select('pedoman_rapp_detail.*', 'kode_akun.kode', 'kode_akun.nama as nama_akun', 'kode_akun.tipe', 'kode_akun.nomor_up', 'unit_pelayanan.inisial as detail_up_inisial', 'unit_pelayanan.nama as detail_up_nama')
            ->orderBy('kode_akun.tipe')
            ->orderBy('kode_akun.nomor_up')
            ->orderBy('urutan')
            ->get();

        // Filter akun list by UP if this RAPP belongs to a specific UP
        if ($rapp->unit_pelayanan_id) {
            $akunList = KodeAkun::where('aktif', true)
                ->where('nomor_up', $rapp->unit_pelayanan_id)
                ->orderBy('kode')->get();
        } else {
            $akunList = KodeAkun::where('aktif', true)->orderBy('kode')->get();
        }

        return view('pedoman.rapp-detail', compact('rapp', 'details', 'akunList'));
    }

    public function addRappDetail(Request $request, int $id)
    {
        $request->validate([
            'kode_akun_id' => 'required|exists:kode_akun,id',
            'nominal' => 'required|numeric|min:0',
        ]);

        $maxUrut = DB::table('pedoman_rapp_detail')->where('pedoman_id', $id)->max('urutan') ?? 0;

        DB::table('pedoman_rapp_detail')->insert([
            'pedoman_id' => $id,
            'kode_akun_id' => $request->kode_akun_id,
            'keterangan' => $request->keterangan ?? '',
            'volume' => $request->volume ?? 1,
            'nominal' => $request->nominal,
            'urutan' => $maxUrut + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Detail RAPP ditambahkan.');
    }

    public function deleteRappDetail(int $detailId)
    {
        DB::table('pedoman_rapp_detail')->where('id', $detailId)->delete();
        return back()->with('success', 'Detail dihapus.');
    }

    public function updateRappDetail(Request $request, int $detailId)
    {
        $request->validate([
            'volume' => 'required|integer|min:1',
            'nominal' => 'required|numeric|min:0',
        ]);

        DB::table('pedoman_rapp_detail')->where('id', $detailId)->update([
            'volume' => $request->volume,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan ?? '',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Detail diperbarui.');
    }

    public function approveRapp(Request $request, int $id)
    {
        DB::table('pedoman_rapp')->where('id', $id)->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        $rapp = DB::table('pedoman_rapp')->where('id', $id)->first();
        $statusLabel = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';

        return back()->with('success', "RAPP {$statusLabel}.");
    }

    // Merge all approved RAPP for a year into one gabungan
    public function mergeRapp(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Check all UP RAPP are approved
        $allRapp = DB::table('pedoman_rapp')
            ->where('tahun', $tahun)
            ->where('is_gabungan', false)
            ->get();

        $pending = $allRapp->where('status', '!=', 'disetujui');
        if ($pending->count() > 0) {
            $names = $pending->pluck('judul')->join(', ');
            return back()->with('error', "Masih ada RAPP belum disetujui: {$names}");
        }

        if ($allRapp->count() === 0) {
            return back()->with('error', "Tidak ada RAPP untuk tahun {$tahun}.");
        }

        // Check if gabungan already exists
        $existing = DB::table('pedoman_rapp')
            ->where('tahun', $tahun)->where('is_gabungan', true)->first();

        if ($existing) {
            // Delete old gabungan details and recreate
            DB::table('pedoman_rapp_detail')->where('pedoman_id', $existing->id)->delete();
            $gabunganId = $existing->id;
            DB::table('pedoman_rapp')->where('id', $gabunganId)->update([
                'judul' => "RAPP Gabungan Tahun {$tahun}",
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $gabunganId = DB::table('pedoman_rapp')->insertGetId([
                'judul' => "RAPP Gabungan Tahun {$tahun}",
                'tahun' => $tahun,
                'unit_pelayanan_id' => null,
                'is_gabungan' => true,
                'status' => 'disetujui',
                'catatan' => "Gabungan dari {$allRapp->count()} RAPP UP",
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Copy all details from each UP RAPP
        $urutan = 0;
        foreach ($allRapp as $r) {
            $details = DB::table('pedoman_rapp_detail')->where('pedoman_id', $r->id)->get();
            foreach ($details as $d) {
                DB::table('pedoman_rapp_detail')->insert([
                    'pedoman_id' => $gabunganId,
                    'kode_akun_id' => $d->kode_akun_id,
                    'keterangan' => $d->keterangan,
                    'volume' => $d->volume,
                    'nominal' => $d->nominal,
                    'urutan' => $urutan++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('pedoman.rapp.show', $gabunganId)
            ->with('success', "RAPP Gabungan Tahun {$tahun} berhasil dibuat dari {$allRapp->count()} RAPP UP.");
    }

    public function destroyRapp(int $id)
    {
        DB::table('pedoman_rapp_detail')->where('pedoman_id', $id)->delete();
        DB::table('pedoman_rapp')->where('id', $id)->delete();
        return back()->with('success', 'RAPP dihapus.');
    }

    public function destroyPedoman(int $id)
    {
        $doc = DB::table('pedoman')->where('id_pedoman', $id)->first();
        if ($doc && $doc->file_path) Storage::disk('public')->delete($doc->file_path);
        DB::table('pedoman')->where('id_pedoman', $id)->delete();
        return back()->with('success', 'Pedoman dihapus.');
    }

    // ─── RAPP REALISASI ─────────────────────────────
    public function showRealisasi(int $id)
    {
        $rapp = DB::table('pedoman_rapp')
            ->leftJoin('unit_pelayanan', 'pedoman_rapp.unit_pelayanan_id', '=', 'unit_pelayanan.nomor_up')
            ->select('pedoman_rapp.*', 'unit_pelayanan.inisial as up_inisial', 'unit_pelayanan.nama as up_nama')
            ->where('pedoman_rapp.id', $id)->first();
        if (!$rapp) abort(404);

        $details = DB::table('pedoman_rapp_detail')
            ->join('kode_akun', 'pedoman_rapp_detail.kode_akun_id', '=', 'kode_akun.id')
            ->where('pedoman_id', $id)
            ->select('pedoman_rapp_detail.*', 'kode_akun.kode', 'kode_akun.nama as nama_akun', 'kode_akun.tipe')
            ->orderBy('urutan')
            ->get();

        // Calculate realisasi for each detail from actual transaksi
        $realisasiData = [];
        foreach ($details as $d) {
            $realisasi = \App\Models\Transaksi::where('kode_akun_id', $d->kode_akun_id)
                ->where('tahun', $rapp->tahun)
                ->whereIn('status', ['verified', 'approved'])
                ->sum('jumlah');

            $anggaran = $d->volume * $d->nominal;
            $persentase = $anggaran > 0 ? round(($realisasi / $anggaran) * 100, 1) : 0;

            $realisasiData[] = (object) [
                'kode' => $d->kode,
                'nama_akun' => $d->nama_akun,
                'tipe' => $d->tipe,
                'anggaran' => $anggaran,
                'realisasi' => $realisasi,
                'selisih' => $anggaran - $realisasi,
                'persentase' => $persentase,
            ];
        }

        $realisasiData = collect($realisasiData);

        return view('pedoman.rapp-realisasi', compact('rapp', 'realisasiData'));
    }
}
