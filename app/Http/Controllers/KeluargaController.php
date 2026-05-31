<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\WargaJemaat;
use App\Models\LingkunganSion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $sionId = $request->get('sion', '');

        $query = Keluarga::with('lingkunganSion', 'anggota');
        if ($sionId) $query->where('lingkungan_sion_id', $sionId);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $keluarga = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $sionList = LingkunganSion::where('aktif', true)->orderBy('id')->get();

        $stats = [
            'total_kk' => Keluarga::count(),
            'total_jiwa' => WargaJemaat::where('status_jemaat', 'aktif')->count(),
        ];

        return view('pendaftaran.keluarga.index', compact('keluarga', 'sionList', 'search', 'sionId', 'stats'));
    }

    public function create()
    {
        $sionList = LingkunganSion::where('aktif', true)->orderBy('id')->get();
        return view('pendaftaran.keluarga.create', compact('sionList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kepala_keluarga' => 'required|string|max:200',
            'anggota' => 'required|array|min:1',
            'anggota.*.nama_lengkap' => 'required|string|max:200',
            'anggota.*.jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        DB::transaction(function () use ($request) {
            $keluarga = Keluarga::create([
                'no_kk' => $request->no_kk,
                'no_kk_gereja' => $request->no_kk_gereja,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'lingkungan_sion_id' => $request->lingkungan_sion_id,
                'no_telepon' => $request->no_telepon,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->anggota as $a) {
                if (empty($a['nama_lengkap'])) continue;
                WargaJemaat::create([
                    'keluarga_id' => $keluarga->id,
                    'nama_lengkap' => $a['nama_lengkap'],
                    'hubungan_keluarga' => $a['hubungan_keluarga'] ?? 'Lainnya',
                    'jenis_kelamin' => $a['jenis_kelamin'],
                    'tempat_lahir' => $a['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $a['tanggal_lahir'] ?? null,
                    'pekerjaan' => $a['pekerjaan'] ?? null,
                    'golongan_darah' => $a['golongan_darah'] ?? null,
                    'status_pernikahan' => $a['status_pernikahan'] ?? null,
                    'status_sidi' => $a['status_sidi'] ?? 'Belum Sidi',
                    'status_baptis' => $a['status_baptis'] ?? 'Belum Baptis',
                    'no_kk' => $request->no_kk,
                    'lingkungan' => $request->lingkungan_sion_id ? LingkunganSion::find($request->lingkungan_sion_id)?->nama : null,
                    'lingkungan_sion_id' => $request->lingkungan_sion_id,
                    'alamat' => $request->alamat,
                    'no_telepon' => $a['no_telepon'] ?? null,
                    'status_jemaat' => 'aktif',
                    'tanggal_bergabung' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil didaftarkan.');
    }

    public function show(Keluarga $keluarga)
    {
        $keluarga->load('lingkunganSion', 'anggota', 'creator');
        return view('pendaftaran.keluarga.show', compact('keluarga'));
    }

    public function edit(Keluarga $keluarga)
    {
        $keluarga->load('anggota');
        $sionList = LingkunganSion::where('aktif', true)->orderBy('id')->get();
        return view('pendaftaran.keluarga.edit', compact('keluarga', 'sionList'));
    }

    public function update(Request $request, Keluarga $keluarga)
    {
        DB::transaction(function () use ($request, $keluarga) {
            $keluarga->update($request->only([
                'no_kk', 'no_kk_gereja', 'nama_kepala_keluarga', 'alamat', 'rt', 'rw',
                'kelurahan', 'kecamatan', 'kota', 'lingkungan_sion_id', 'no_telepon', 'keterangan',
            ]));

            // Sync alamat & lingkungan to all anggota
            $lingkunganNama = $request->lingkungan_sion_id
                ? LingkunganSion::find($request->lingkungan_sion_id)?->nama
                : null;
            $keluarga->anggota()->update([
                'no_kk' => $request->no_kk,
                'alamat' => $request->alamat,
                'lingkungan' => $lingkunganNama,
                'lingkungan_sion_id' => $request->lingkungan_sion_id,
            ]);
        });

        return redirect()->route('keluarga.show', $keluarga)->with('success', 'Data keluarga diperbarui.');
    }

    // === CRUD ANGGOTA ===

    public function storeAnggota(Request $request, Keluarga $keluarga)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        $lingkunganNama = $keluarga->lingkungan_sion_id
            ? LingkunganSion::find($keluarga->lingkungan_sion_id)?->nama
            : null;

        WargaJemaat::create([
            'keluarga_id' => $keluarga->id,
            'nama_lengkap' => $request->nama_lengkap,
            'hubungan_keluarga' => $request->hubungan_keluarga ?? 'Lainnya',
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'pekerjaan' => $request->pekerjaan,
            'golongan_darah' => $request->golongan_darah,
            'status_pernikahan' => $request->status_pernikahan,
            'status_sidi' => $request->status_sidi ?? 'Belum Sidi',
            'status_baptis' => $request->status_baptis ?? 'Belum Baptis',
            'no_kk' => $keluarga->no_kk,
            'lingkungan' => $lingkunganNama,
            'lingkungan_sion_id' => $keluarga->lingkungan_sion_id,
            'alamat' => $keluarga->alamat,
            'no_telepon' => $request->no_telepon,
            'status_jemaat' => 'aktif',
            'tanggal_bergabung' => now()->toDateString(),
        ]);

        return redirect()->route('keluarga.edit', $keluarga)->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    public function updateAnggota(Request $request, Keluarga $keluarga, WargaJemaat $anggota)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        $anggota->update($request->only([
            'nama_lengkap', 'hubungan_keluarga', 'jenis_kelamin', 'tempat_lahir',
            'tanggal_lahir', 'pekerjaan', 'golongan_darah', 'status_pernikahan',
            'status_sidi', 'status_baptis', 'no_telepon',
        ]));

        return redirect()->route('keluarga.edit', $keluarga)->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroyAnggota(Keluarga $keluarga, WargaJemaat $anggota)
    {
        $anggota->update(['keluarga_id' => null]);
        return redirect()->route('keluarga.edit', $keluarga)->with('success', 'Anggota dihapus dari keluarga.');
    }

    public function destroy(Keluarga $keluarga)
    {
        $keluarga->anggota()->update(['keluarga_id' => null]);
        $keluarga->delete();
        return redirect()->route('keluarga.index')->with('success', 'Data keluarga dihapus.');
    }
}
