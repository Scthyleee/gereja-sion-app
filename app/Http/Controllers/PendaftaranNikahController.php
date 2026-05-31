<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranNikah;
use App\Models\PendaftaranNikahInsentif;
use App\Models\PendaftaranNikahLampiran;
use App\Models\WargaJemaat;
use App\Models\JadwalIbadah;
use App\Models\JadwalPetugas;
use App\Models\KodeAkun;
use App\Models\Transaksi;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranNikahController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $query = PendaftaranNikah::with('jadwal', 'creator', 'insentif')
            ->orderByDesc('created_at');
        if ($status !== 'semua') $query->where('status', $status);

        $data = $query->paginate(20)->withQueryString();
        return view('pendaftaran.nikah.index', compact('data', 'status'));
    }

    public function create()
    {
        $wargaList = WargaJemaat::where('status_jemaat', 'aktif')->orderBy('nama_lengkap')->get();
        $akunPenerimaan = KodeAkun::penerimaan()->where('aktif', true)->orderBy('kode')->get();

        // Load default costs from settings
        $defaults = [];
        $settings = DB::table('system_settings')->where('setting_key', 'like', 'nikah_%')->pluck('setting_value', 'setting_key');
        $defaults['biaya_administrasi'] = $settings['nikah_biaya_administrasi'] ?? 500000;
        $defaults['biaya_fasilitas'] = $settings['nikah_biaya_fasilitas'] ?? 300000;
        $defaults['insentif_pianis'] = $settings['nikah_insentif_pianis'] ?? 200000;
        $defaults['insentif_lcd'] = $settings['nikah_insentif_lcd'] ?? 100000;
        $defaults['insentif_sound'] = $settings['nikah_insentif_sound'] ?? 150000;
        $defaults['insentif_kebersihan'] = $settings['nikah_insentif_kebersihan'] ?? 100000;

        return view('pendaftaran.nikah.create', compact('wargaList', 'akunPenerimaan', 'defaults'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pria_nama' => 'required|string|max:200',
            'wanita_nama' => 'required|string|max:200',
        ]);

        DB::transaction(function () use ($request) {
            // Create pendaftaran nikah
            $nikah = PendaftaranNikah::create(array_merge(
                $request->only([
                    'pria_warga_id', 'pria_nama', 'pria_tempat_lahir', 'pria_tanggal_lahir',
                    'pria_alamat', 'pria_pekerjaan', 'pria_agama', 'pria_status_sidi',
                    'pria_nama_ayah', 'pria_nama_ibu', 'pria_no_telepon',
                    'wanita_warga_id', 'wanita_nama', 'wanita_tempat_lahir', 'wanita_tanggal_lahir',
                    'wanita_alamat', 'wanita_pekerjaan', 'wanita_agama', 'wanita_status_sidi',
                    'wanita_nama_ayah', 'wanita_nama_ibu', 'wanita_no_telepon',
                    'saksi_pria_1', 'saksi_pria_2', 'saksi_wanita_1', 'saksi_wanita_2',
                    'biaya_administrasi', 'kode_akun_admin_id',
                    'biaya_fasilitas', 'kode_akun_fasilitas_id',
                    'tanggal_nikah_rencana', 'catatan',
                ]),
                [
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                    'scan_formulir_path' => $request->hasFile('scan_formulir')
                        ? $request->file('scan_formulir')->store('Pendaftaran/Nikah', 'public')
                        : null,
                ]
            ));

            // Create insentif records (dynamic rows)
            if ($request->has('insentif_jenis')) {
                foreach ($request->insentif_jenis as $i => $jenis) {
                    if (empty($jenis)) continue;
                    PendaftaranNikahInsentif::create([
                        'pendaftaran_nikah_id' => $nikah->id,
                        'jenis_insentif' => $jenis,
                        'nominal' => $request->insentif_nominal[$i] ?? 0,
                    ]);
                }
            }

            // Auto-create jadwal ibadah pernikahan (tanggal TBD)
            $jadwal = JadwalIbadah::create([
                'tanggal' => $request->tanggal_nikah_rencana ?? now()->addMonth()->toDateString(),
                'jenis_ibadah' => $nikah->judul_jadwal,
                'kategori' => 'pernikahan',
                'lokasi' => 'Gereja Sion',
                'lokasi_tipe' => 'gereja',
                'keterangan' => "Pernikahan {$nikah->pria_nama} & {$nikah->wanita_nama}",
                'pendaftaran_nikah_id' => $nikah->id,
                'status' => 'scheduled',
            ]);

            $nikah->update(['jadwal_ibadah_id' => $jadwal->id_jadwal]);

            // Auto-create folder di File Manager
            $folderId = DB::table('file_manager')->insertGetId([
                'nama_file' => "Pernikahan - {$nikah->pria_nama} & {$nikah->wanita_nama}",
                'tipe' => 'folder',
                'parent_id' => null,
                'uploaded_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $nikah->update(['folder_lampiran_id' => $folderId]);
        });

        return redirect()->route('nikah.index')->with('success', 'Pendaftaran pernikahan berhasil. Jadwal otomatis dibuat.');
    }

    public function show(PendaftaranNikah $nikah)
    {
        $nikah->load('priaWarga', 'wanitaWarga', 'jadwal', 'validator', 'creator', 'insentif', 'lampiran');
        return view('pendaftaran.nikah.show', compact('nikah'));
    }

    public function uploadLampiran(Request $request, PendaftaranNikah $nikah)
    {
        $request->validate(['file' => 'required|file|max:10240']);
        $file = $request->file('file');
        $path = $file->store('pendaftaran/nikah/' . $nikah->id, 'public');

        // Save to file_manager
        $fmId = DB::table('file_manager')->insertGetId([
            'nama_file' => $file->getClientOriginalName(),
            'tipe' => 'file',
            'parent_id' => $nikah->folder_lampiran_id,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'ukuran' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        PendaftaranNikahLampiran::create([
            'pendaftaran_nikah_id' => $nikah->id,
            'jenis_lampiran' => $request->jenis_lampiran ?? 'Lainnya',
            'file_manager_id' => $fmId,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Lampiran berhasil diupload.');
    }

    public function validate_nikah(PendaftaranNikah $nikah)
    {
        if ($nikah->status === 'divalidasi') {
            return back()->with('error', 'Sudah divalidasi.');
        }

        DB::transaction(function () use ($nikah) {
            // Create transaksi for biaya administrasi
            if ($nikah->biaya_administrasi > 0 && $nikah->kode_akun_admin_id) {
                Transaksi::create([
                    'tanggal' => now()->toDateString(),
                    'kode_akun_id' => $nikah->kode_akun_admin_id,
                    'tipe' => 'penerimaan',
                    'jumlah' => $nikah->biaya_administrasi,
                    'keterangan' => "Administrasi Nikah: {$nikah->pria_nama} & {$nikah->wanita_nama}",
                    'bulan' => now()->month,
                    'tahun' => now()->year,
                    'kantong_dana' => 'rutin',
                    'status' => 'draft',
                    'dibuat_oleh' => auth()->id(),
                ]);
            }

            // Create transaksi for biaya fasilitas
            if ($nikah->biaya_fasilitas > 0 && $nikah->kode_akun_fasilitas_id) {
                Transaksi::create([
                    'tanggal' => now()->toDateString(),
                    'kode_akun_id' => $nikah->kode_akun_fasilitas_id,
                    'tipe' => 'penerimaan',
                    'jumlah' => $nikah->biaya_fasilitas,
                    'keterangan' => "Fasilitas Nikah: {$nikah->pria_nama} & {$nikah->wanita_nama}",
                    'bulan' => now()->month,
                    'tahun' => now()->year,
                    'kantong_dana' => 'rutin',
                    'status' => 'draft',
                    'dibuat_oleh' => auth()->id(),
                ]);
            }

            // Auto-create insentif as petugas assignments on the jadwal
            if ($nikah->jadwal_ibadah_id) {
                foreach ($nikah->insentif as $ins) {
                    JadwalPetugas::create([
                        'jadwal_id' => $nikah->jadwal_ibadah_id,
                        'petugas_id' => Petugas::first()?->id_petugas ?? 1,
                        'jenis_tugas' => $ins->jenis_insentif,
                        'nominal_insentif' => $ins->nominal,
                        'kategori_insentif' => 'fasilitas',
                        'sumber' => 'pernikahan',
                        'is_override' => true,
                    ]);
                }
            }

            $nikah->update([
                'status' => 'divalidasi',
                'divalidasi_oleh' => auth()->id(),
                'divalidasi_at' => now(),
            ]);
        });

        return back()->with('success', 'Pernikahan divalidasi! Transaksi keuangan & insentif telah dibuat otomatis.');
    }

    public function updateStatus(Request $request, PendaftaranNikah $nikah)
    {
        $nikah->update([
            'status' => $request->status,
            'catatan_penolakan' => $request->catatan_penolakan,
        ]);
        return back()->with('success', 'Status diperbarui.');
    }

    public function destroy(PendaftaranNikah $nikah)
    {
        $nikah->insentif()->delete();
        $nikah->lampiran()->delete();
        $nikah->delete();
        return redirect()->route('nikah.index')->with('success', 'Pendaftaran nikah dihapus.');
    }

    public function storeInsentif(Request $request, PendaftaranNikah $nikah)
    {
        $request->validate([
            'jenis_insentif' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:0',
        ]);

        PendaftaranNikahInsentif::create([
            'pendaftaran_nikah_id' => $nikah->id,
            'jenis_insentif' => $request->jenis_insentif,
            'nominal' => $request->nominal,
        ]);

        return back()->with('success', 'Petugas/insentif ditambahkan.');
    }

    public function destroyInsentif(PendaftaranNikah $nikah, PendaftaranNikahInsentif $insentif)
    {
        $insentif->delete();
        return back()->with('success', 'Petugas/insentif dihapus.');
    }
}
