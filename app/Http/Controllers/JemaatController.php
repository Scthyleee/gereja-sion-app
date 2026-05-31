<?php

namespace App\Http\Controllers;

use App\Models\WargaJemaat;
use Illuminate\Http\Request;

class JemaatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $status = $request->get('status', 'aktif');

        $query = WargaJemaat::query();
        if ($status !== 'semua') $query->where('status_jemaat', $status);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('lingkungan', 'like', "%{$search}%");
            });
        }

        $jemaat = $query->orderBy('nama_lengkap')->paginate(20)->withQueryString();

        $stats = [
            'total' => WargaJemaat::where('status_jemaat', 'aktif')->count(),
            'pria' => WargaJemaat::where('status_jemaat', 'aktif')->where('jenis_kelamin', 'Laki-laki')->count(),
            'wanita' => WargaJemaat::where('status_jemaat', 'aktif')->where('jenis_kelamin', 'Perempuan')->count(),
            'sidi' => WargaJemaat::where('status_jemaat', 'aktif')->where('status_sidi', 'Sudah Sidi')->count(),
        ];

        $lingkungan = \App\Models\LingkunganSion::where('aktif', true)->orderBy('nama')->get();

        return view('jemaat.index', compact('jemaat', 'stats', 'search', 'status', 'lingkungan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_kk' => 'nullable|string|max:50',
            'lingkungan' => 'nullable|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:500',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'status_pernikahan' => 'nullable|string|max:50',
            'status_sidi' => 'nullable|string|max:50',
            'tanggal_bergabung' => 'nullable|date',
        ]);

        try {
            $data = $request->only([
                'nama_lengkap', 'no_kk', 'lingkungan', 'jenis_kelamin', 'tempat_lahir',
                'tanggal_lahir', 'alamat', 'no_telepon', 'email', 'pekerjaan',
                'status_pernikahan', 'status_sidi', 'tanggal_bergabung',
            ]);

            // Set defaults for empty fields
            $data['status_jemaat'] = 'aktif';
            if (empty($data['tanggal_bergabung'])) {
                $data['tanggal_bergabung'] = now()->toDateString();
            }

            // Auto-link to existing keluarga if no_kk matches
            if (!empty($data['no_kk'])) {
                $keluarga = \App\Models\Keluarga::where('no_kk', $data['no_kk'])->first();
                if ($keluarga) {
                    $data['keluarga_id'] = $keluarga->id;
                    $data['lingkungan_sion_id'] = $keluarga->lingkungan_sion_id;
                    if (empty($data['alamat'])) {
                        $data['alamat'] = $keluarga->alamat;
                    }
                }
            }

            WargaJemaat::create($data);

            return back()->with('success', 'Data jemaat berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan jemaat: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, WargaJemaat $jemaat)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
            'email' => 'nullable|email|max:100',
            'tanggal_bergabung' => 'nullable|date',
        ]);

        try {
            $jemaat->update($request->only([
                'nama_lengkap', 'no_kk', 'lingkungan', 'jenis_kelamin', 'tempat_lahir',
                'tanggal_lahir', 'alamat', 'no_telepon', 'email', 'pekerjaan',
                'status_pernikahan', 'status_sidi', 'status_jemaat', 'tanggal_bergabung',
                'jabatan_gereja', 'status_baptis',
            ]));

            return back()->with('success', 'Data jemaat berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update jemaat: ' . $e->getMessage());
        }
    }

    public function destroy(WargaJemaat $jemaat)
    {
        $jemaat->delete();
        return back()->with('success', 'Data jemaat berhasil dihapus.');
    }
}
