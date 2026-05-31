<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranBaptis;
use App\Models\WargaJemaat;
use App\Models\LingkunganSion;
use App\Models\JadwalIbadah;
use App\Models\PenandatanganDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranBaptisController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $query = PendaftaranBaptis::with('warga', 'lingkunganSion', 'jadwal', 'creator')
            ->orderByDesc('created_at');
        if ($status !== 'semua') $query->where('status', $status);

        $data = $query->paginate(20)->withQueryString();
        $stats = [
            'total' => PendaftaranBaptis::count(),
            'pending' => PendaftaranBaptis::where('status', 'pending')->count(),
            'selesai' => PendaftaranBaptis::where('status', 'selesai')->count(),
        ];

        return view('pendaftaran.baptis.index', compact('data', 'status', 'stats'));
    }

    public function create()
    {
        $wargaList = WargaJemaat::where('status_jemaat', 'aktif')
            ->where('status_baptis', 'Belum Baptis')
            ->orderBy('nama_lengkap')->get();
        $sionList = LingkunganSion::where('aktif', true)->orderBy('id')->get();
        return view('pendaftaran.baptis.create', compact('wargaList', 'sionList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:200',
        ]);

        $data = $request->only([
            'warga_id', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'alamat', 'no_telepon', 'pekerjaan',
            'lingkungan_sion_id', 'nama_ayah', 'nama_ibu',
            'saksi_1', 'saksi_2', 'agama_sebelum', 'biaya_baptis',
            'katekisasi_mulai', 'katekisasi_selesai', 'nilai_ujian',
            'tanggal_baptis_rencana', 'catatan',
        ]);
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();

        // Handle scan formulir upload
        if ($request->hasFile('scan_formulir')) {
            $data['scan_formulir_path'] = $request->file('scan_formulir')
                ->store('Pendaftaran/Baptis', 'public');
        }

        PendaftaranBaptis::create($data);
        return redirect()->route('baptis.index')->with('success', 'Pendaftaran baptis berhasil disimpan.');
    }

    public function show(PendaftaranBaptis $bapti)
    {
        $bapti->load('warga', 'lingkunganSion', 'jadwal', 'approver', 'creator');
        $penandatangan = PenandatanganDokumen::where('module', 'baptis')
            ->where('aktif', true)->orderBy('urutan')->get();
        return view('pendaftaran.baptis.show', compact('bapti', 'penandatangan'));
    }

    public function updateStatus(Request $request, PendaftaranBaptis $bapti)
    {
        $newStatus = $request->status;

        if ($newStatus === 'ditolak') {
            $bapti->update([
                'status' => 'ditolak',
                'catatan_penolakan' => $request->catatan_penolakan,
            ]);
            return back()->with('success', 'Pendaftaran ditolak.');
        }

        if ($newStatus === 'terdaftar') {
            $bapti->update([
                'status' => 'terdaftar',
                'disetujui_oleh' => auth()->id(),
                'disetujui_at' => now(),
            ]);
            return back()->with('success', 'Pendaftaran disetujui.');
        }

        if ($newStatus === 'dijadwalkan') {
            // Create jadwal ibadah baptis
            $jadwal = JadwalIbadah::create([
                'tanggal' => $request->tanggal_baptis ?? now()->addMonth()->toDateString(),
                'jenis_ibadah' => 'Ibadah Baptisan Kudus',
                'kategori' => 'baptis',
                'lokasi' => 'Gereja Sion',
                'lokasi_tipe' => 'gereja',
                'keterangan' => "Baptisan: {$bapti->nama_lengkap}",
                'pendaftaran_baptis_id' => $bapti->id,
                'status' => 'scheduled',
            ]);

            $bapti->update([
                'status' => 'dijadwalkan',
                'jadwal_ibadah_id' => $jadwal->id_jadwal,
                'tanggal_baptis_rencana' => $request->tanggal_baptis,
            ]);
            return back()->with('success', 'Jadwal baptis dibuat.');
        }

        if ($newStatus === 'selesai') {
            DB::transaction(function () use ($bapti) {
                $bapti->update(['status' => 'selesai']);

                // Auto-update warga_jemaat
                if ($bapti->warga_id) {
                    WargaJemaat::where('id_warga', $bapti->warga_id)->update([
                        'status_baptis' => 'Sudah Baptis',
                        'tanggal_baptis' => $bapti->tanggal_baptis_rencana ?? now()->toDateString(),
                    ]);
                }

                // Mark jadwal completed
                if ($bapti->jadwal_ibadah_id) {
                    JadwalIbadah::where('id_jadwal', $bapti->jadwal_ibadah_id)
                        ->update(['status' => 'completed']);
                }
            });
            return back()->with('success', 'Baptisan selesai! Status jemaat telah diperbarui otomatis.');
        }

        $bapti->update(['status' => $newStatus]);
        return back()->with('success', 'Status diperbarui.');
    }

    public function destroy(PendaftaranBaptis $bapti)
    {
        $bapti->delete();
        return redirect()->route('baptis.index')->with('success', 'Pendaftaran baptis dihapus.');
    }
}
