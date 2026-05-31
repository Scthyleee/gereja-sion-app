<?php

namespace App\Http\Controllers;

use App\Models\LingkunganSion;
use App\Models\WargaJemaat;
use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LingkunganSionController extends Controller
{
    // ===== ADMIN VIEW: LIST ALL LINGKUNGAN =====
    public function index()
    {
        $lingkungan = LingkunganSion::withCount(['keluarga', 'warga'])
            ->orderBy('nama')->get();

        return view('lingkungan.index', compact('lingkungan'));
    }

    // ===== DETAIL LINGKUNGAN =====
    public function show(LingkunganSion $lingkungan)
    {
        $lingkungan->load(['keluarga.anggota', 'warga', 'ketua.role', 'bendahara.role', 'sekretaris.role']);

        // Stats
        $totalKK = $lingkungan->keluarga->count();
        $totalJiwa = $lingkungan->warga->count();
        $baptis = $lingkungan->warga->where('status_baptis', 'Sudah Baptis')->count();
        $belumBaptis = $totalJiwa - $baptis;

        // Pengurus from lingkungan relations
        $pengurus = collect();
        if ($lingkungan->ketua) $pengurus->push((object)['nama' => $lingkungan->ketua->nama, 'nama_role' => 'Ketua Lingkungan']);
        if ($lingkungan->bendahara) $pengurus->push((object)['nama' => $lingkungan->bendahara->nama, 'nama_role' => 'Bendahara Lingkungan']);
        if ($lingkungan->sekretaris) $pengurus->push((object)['nama' => $lingkungan->sekretaris->nama, 'nama_role' => 'Sekretaris Lingkungan']);

        return view('lingkungan.show', compact('lingkungan', 'totalKK', 'totalJiwa', 'baptis', 'belumBaptis', 'pengurus'));
    }

    // ===== UPDATE LINGKUNGAN =====
    public function update(Request $request, LingkunganSion $lingkungan)
    {
        $lingkungan->update($request->only(['nama', 'keterangan', 'wilayah']));
        return back()->with('success', 'Data lingkungan berhasil diupdate.');
    }

    // ===== DASHBOARD LINGKUNGAN (for Ketua/Bendahara/Sekretaris) =====
    public function dashboard()
    {
        $user = auth()->user();

        // Find user's lingkungan via ketua/bendahara/sekretaris FK
        $lingkungan = LingkunganSion::where('ketua_id', $user->id)
            ->orWhere('bendahara_id', $user->id)
            ->orWhere('sekretaris_id', $user->id)
            ->first();

        if (!$lingkungan) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar di lingkungan manapun.');
        }

        $lingkungan->load(['keluarga.anggota', 'warga', 'ketua.role', 'bendahara.role', 'sekretaris.role']);

        $totalKK = $lingkungan->keluarga->count();
        $totalJiwa = $lingkungan->warga->count();
        $baptis = $lingkungan->warga->where('status_baptis', 'Sudah Baptis')->count();

        // Pengurus
        $pengurus = collect();
        if ($lingkungan->ketua) $pengurus->push((object)['nama' => $lingkungan->ketua->nama, 'nama_role' => 'Ketua Lingkungan']);
        if ($lingkungan->bendahara) $pengurus->push((object)['nama' => $lingkungan->bendahara->nama, 'nama_role' => 'Bendahara Lingkungan']);
        if ($lingkungan->sekretaris) $pengurus->push((object)['nama' => $lingkungan->sekretaris->nama, 'nama_role' => 'Sekretaris Lingkungan']);

        // Laporan keuangan lingkungan — skip if transaksi doesn't have lingkungan_sion_id
        $laporanKeuangan = (object)['total_masuk' => 0, 'total_keluar' => 0];
        $tahun = now()->year;

        return view('lingkungan.dashboard', compact(
            'lingkungan', 'totalKK', 'totalJiwa', 'baptis', 'pengurus',
            'laporanKeuangan', 'tahun'
        ));
    }
}
