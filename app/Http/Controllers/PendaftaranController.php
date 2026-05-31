<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use App\Models\WargaJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'semua');
        $query = DB::table('pendaftaran')->orderByDesc('created_at');
        if ($jenis !== 'semua') $query->where('jenis', $jenis);

        $pendaftaran = $query->paginate(20)->withQueryString();
        $wargaList = WargaJemaat::where('status_jemaat', 'aktif')->orderBy('nama_lengkap')->get();
        $akunPenerimaan = KodeAkun::penerimaan()->where('aktif', true)->orderBy('kode')->get();

        return view('pendaftaran.index', compact('pendaftaran', 'jenis', 'wargaList', 'akunPenerimaan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:warga,baptis,sidi,pernikahan,pelayanan',
            'nama_lengkap' => 'required|string|max:200',
        ]);

        $data = $request->only([
            'is_jemaat', 'warga_id', 'pasangan_id', 'nama_pasangan', 'jenis',
            'nama_lengkap', 'no_kk', 'lingkungan', 'jenis_kelamin', 'tempat_lahir',
            'tanggal_lahir', 'alamat', 'no_telepon', 'email', 'pekerjaan',
            'status_sidi', 'status_pernikahan', 'tanggal_bergabung',
            'nama_ayah', 'nama_ibu', 'keterangan', 'biaya',
            'kode_akun_penerimaan_id', 'kode_akun_pengeluaran_id',
        ]);
        $data['tanggal_daftar'] = now()->toDateString();
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('pendaftaran')->insert($data);
        return back()->with('success', 'Pendaftaran berhasil dicatat.');
    }

    public function updateStatus(Request $request, int $id)
    {
        DB::table('pendaftaran')->where('id_pendaftaran', $id)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Status pendaftaran diupdate.');
    }

    public function destroy(int $id)
    {
        DB::table('pendaftaran')->where('id_pendaftaran', $id)->delete();
        return back()->with('success', 'Pendaftaran dihapus.');
    }
}
