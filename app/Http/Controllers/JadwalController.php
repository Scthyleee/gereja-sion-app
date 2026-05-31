<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\JadwalPetugas;
use App\Models\JadwalKantong;
use App\Models\JadwalAmplop;
use App\Models\JadwalAmplopDetail;
use App\Models\Petugas;
use App\Models\KodeAkun;
use App\Models\PetugasRole;
use App\Models\Transaksi;
use App\Models\TransaksiPS;
use App\Models\WargaJemaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    // --- JADWAL TAB ---
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'jadwal');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $jadwal = JadwalIbadah::with('petugas.petugas', 'kantong', 'amplop.detail')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderByDesc('tanggal')
            ->get();

        $jenisIbadah = DB::table('jenis_ibadah_master')->orderBy('nama_jenis')->get();
        $petugasList = Petugas::where('status', 'active')->orderBy('nama_petugas')->get();
        $akunKolekte = KodeAkun::kolekte()->where('aktif', true)->orderBy('kode')->get();
        $akunPersembahan = KodeAkun::persembahanSyukur()->where('aktif', true)->orderBy('kode')->get();
        $wargaList = WargaJemaat::where('status_jemaat', 'aktif')->orderBy('nama_lengkap')->get();

        return view('jadwal.index', compact(
            'tab', 'jadwal', 'bulan', 'tahun', 'jenisIbadah', 'petugasList',
            'akunKolekte', 'akunPersembahan', 'wargaList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_ibadah' => 'required|string',
            'waktu' => 'nullable',
            'lokasi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string|max:500',
        ]);

        JadwalIbadah::create($request->only('tanggal', 'jenis_ibadah', 'judul_ibadah', 'waktu', 'lokasi', 'keterangan'));
        return back()->with('success', 'Jadwal berhasil dibuat.');
    }

    public function show(JadwalIbadah $jadwal)
    {
        $jadwal->load('petugas.petugas', 'kantong.kodeAkun', 'amplop.detail.kodeAkun', 'amplop.warga');
        $petugasList = Petugas::where('status', 'active')->orderBy('nama_petugas')->get();
        $akunKolekte = KodeAkun::kolekte()->where('aktif', true)->orderBy('kode')->get();

        // PS jenis from jenis_ps_config instead of Domain UP
        $jenisPS = DB::table('jenis_ps_config')->where('aktif', true)->orderBy('urutan')->get();

        $wargaList = WargaJemaat::where('status_jemaat', 'aktif')->orderBy('nama_lengkap')->get();
        $slotTemplates = DB::table('petugas_slot_template')->where('aktif', true)->orderBy('urutan')->get();

        // Diakon & Penetua list for pelayanan assignment
        $diakonPenetuaList = WargaJemaat::where('status_jemaat', 'aktif')
            ->whereIn('jabatan_gereja', ['Diakon', 'Penetua'])
            ->orderBy('nama_lengkap')
            ->get();

        // Insentif config for this event type
        $tipeEvent = $this->detectTipeEvent($jadwal->jenis_ibadah);
        $insentifConfig = DB::table('insentif_config')
            ->where('tipe_event', $tipeEvent)
            ->where('aktif', true)
            ->get();

        // Quota check
        $quota = DB::table('system_settings')->where('setting_key', 'petugas_quota_per_bulan')->value('setting_value') ?? 4;
        $bulan = $jadwal->tanggal->month;
        $tahun = $jadwal->tanggal->year;
        $petugasCount = JadwalPetugas::whereHas('jadwal', fn($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun))
            ->select('petugas_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('petugas_id')
            ->pluck('cnt', 'petugas_id');

        return view('jadwal.show', compact(
            'jadwal', 'petugasList', 'akunKolekte', 'jenisPS', 'wargaList',
            'slotTemplates', 'quota', 'petugasCount', 'diakonPenetuaList',
            'insentifConfig', 'tipeEvent'
        ));
    }

    private function detectTipeEvent(string $jenisIbadah): string
    {
        $lower = strtolower($jenisIbadah);
        if (str_contains($lower, 'nikah') || str_contains($lower, 'pernikahan') || str_contains($lower, 'pemberkatan')) {
            return 'pernikahan';
        }
        if (str_contains($lower, 'perayaan') || str_contains($lower, 'natal') || str_contains($lower, 'paskah') ||
            str_contains($lower, 'hut') || str_contains($lower, 'syukuran') || str_contains($lower, 'tahun baru')) {
            return 'perayaan';
        }
        return 'ibadah_biasa';
    }

    public function update(Request $request, JadwalIbadah $jadwal)
    {
        $jadwal->update($request->only('tanggal', 'jenis_ibadah', 'judul_ibadah', 'waktu', 'lokasi', 'keterangan', 'jumlah_hadir', 'tema_ibadah', 'ayat_khotbah'));
        return back()->with('success', 'Jadwal berhasil diupdate.');
    }

    public function updateHadir(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate(['jumlah_hadir' => 'required|integer|min:0']);
        $jadwal->update(['jumlah_hadir' => $request->jumlah_hadir]);
        return back()->with('success', 'Jumlah hadir berhasil diupdate.');
    }

    public function uploadTtd(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate(['foto_ttd' => 'required|image|mimes:jpg,jpeg,png|max:5120']);

        $path = $request->file('foto_ttd')->store('foto-ttd', 'public');
        $jadwal->update(['foto_ttd_path' => $path]);

        return back()->with('success', 'Foto tanda tangan berhasil diupload.');
    }

    public function destroy(JadwalIbadah $jadwal)
    {
        $jadwal->petugas()->delete();
        $jadwal->kantong()->delete();
        foreach ($jadwal->amplop as $a) { $a->detail()->delete(); }
        $jadwal->amplop()->delete();
        $jadwal->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    // --- PETUGAS per Jadwal (insentif) ---
    public function storePetugas(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate(['petugas_id' => 'required|exists:petugas,id_petugas']);
        $petugas = Petugas::findOrFail($request->petugas_id);

        // Quota check
        $quota = DB::table('system_settings')->where('setting_key', 'petugas_quota_per_bulan')->value('setting_value') ?? 4;
        $bulan = $jadwal->tanggal->month;
        $tahun = $jadwal->tanggal->year;
        $current = JadwalPetugas::where('petugas_id', $petugas->id_petugas)
            ->whereHas('jadwal', fn($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun))
            ->count();

        if ($current >= (int)$quota) {
            return back()->with('error', "{$petugas->nama_petugas} sudah mencapai batas {$quota}x tugas bulan ini.");
        }

        // Determine insentif from InsentifConfig
        $tipeEvent = $this->detectTipeEvent($jadwal->jenis_ibadah);
        $jenisTugas = $request->jenis_tugas ?? 'Umum';
        $nominal = $request->nominal_insentif ?? \App\Models\InsentifConfig::getNominal($tipeEvent, $jenisTugas);
        if ($petugas->is_pendeta_sion) {
            $nominal = 0;
        }

        JadwalPetugas::create([
            'jadwal_id' => $jadwal->id_jadwal,
            'petugas_id' => $petugas->id_petugas,
            'jenis_tugas' => $jenisTugas,
            'slot_nama' => $request->slot_nama ?? null,
            'nominal_insentif' => $nominal,
            'is_override' => $request->has('nominal_insentif'),
            'kategori' => 'insentif',
        ]);

        return back()->with('success', "Petugas {$petugas->nama_petugas} ditambahkan.");
    }

    // --- PETUGAS EXTERNAL (pendeta/pianis luar) ---
    public function storePetugasExternal(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate([
            'nama_external' => 'required|string|max:200',
            'jenis_tugas' => 'required|string|max:100',
            'nominal_insentif' => 'required|numeric|min:0',
        ]);

        JadwalPetugas::create([
            'jadwal_id' => $jadwal->id_jadwal,
            'petugas_id' => null,
            'jenis_tugas' => $request->jenis_tugas,
            'slot_nama' => $request->jenis_tugas,
            'nominal_insentif' => $request->nominal_insentif,
            'is_override' => true,
            'is_external' => true,
            'nama_external' => $request->nama_external,
            'kategori' => 'insentif',
        ]);

        return back()->with('success', "Petugas luar ({$request->nama_external}) ditambahkan.");
    }

    // --- PETUGAS PELAYANAN (Diakon/Penetua tanpa insentif) ---
    public function storePetugasPelayanan(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate([
            'warga_id' => 'required|exists:warga_jemaat,id_warga',
            'jenis_tugas' => 'required|string|max:100',
        ]);

        $warga = WargaJemaat::findOrFail($request->warga_id);

        JadwalPetugas::create([
            'jadwal_id' => $jadwal->id_jadwal,
            'petugas_id' => null,
            'jenis_tugas' => $request->jenis_tugas,
            'slot_nama' => $request->jenis_tugas,
            'nominal_insentif' => 0,
            'is_override' => false,
            'is_external' => false,
            'nama_external' => $warga->nama_lengkap,
            'kategori' => 'pelayanan',
        ]);

        return back()->with('success', "{$warga->nama_lengkap} ditambahkan sebagai petugas pelayanan.");
    }

    // --- RANDOM ASSIGNMENT ---
    public function randomPetugas(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate([
            'jenis_tugas' => 'required|string',
            'jumlah' => 'required|integer|min:1|max:10',
            'mode' => 'required|in:all,lingkungan',
        ]);

        $query = WargaJemaat::where('status_jemaat', 'aktif')
            ->whereIn('jabatan_gereja', ['Diakon', 'Penetua']);

        if ($request->mode === 'lingkungan' && $request->lingkungan) {
            $query->where('lingkungan', $request->lingkungan);
        }

        // Exclude already assigned
        $assigned = $jadwal->petugas()->pluck('nama_external')->toArray();
        $query->whereNotIn('nama_lengkap', $assigned);

        $candidates = $query->inRandomOrder()->limit($request->jumlah)->get();

        foreach ($candidates as $warga) {
            JadwalPetugas::create([
                'jadwal_id' => $jadwal->id_jadwal,
                'petugas_id' => null,
                'jenis_tugas' => $request->jenis_tugas,
                'slot_nama' => $request->jenis_tugas,
                'nominal_insentif' => 0,
                'is_override' => false,
                'is_external' => false,
                'nama_external' => $warga->nama_lengkap,
                'kategori' => 'pelayanan',
            ]);
        }

        return back()->with('success', "{$candidates->count()} petugas pelayanan ditambahkan secara random.");
    }

    public function destroyPetugas(JadwalPetugas $jp)
    {
        $jp->delete();
        return back()->with('success', 'Petugas dihapus dari jadwal.');
    }

    // --- KANTONG ---
    public function storeKantong(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate([
            'kode_akun_id' => 'required|exists:kode_akun,id',
            'total' => 'required|numeric|min:0',
        ]);

        $akun = KodeAkun::findOrFail($request->kode_akun_id);
        JadwalKantong::create([
            'jadwal_id' => $jadwal->id_jadwal,
            'kode_akun_id' => $akun->id,
            'label' => $akun->keterangan ?: $akun->nama,
            'total' => $request->total,
        ]);

        return back()->with('success', 'Kantong persembahan ditambahkan.');
    }

    public function destroyKantong(JadwalKantong $kantong)
    {
        $kantong->delete();
        return back()->with('success', 'Kantong dihapus.');
    }

    // --- AMPLOP PS (now using jenis_ps_config) ---
    public function storeAmplop(Request $request, JadwalIbadah $jadwal)
    {
        $request->validate([
            'nama_pemberi' => 'required|string',
            'jenis_ps_ids' => 'required|array',
            'jenis_ps_ids.*' => 'exists:jenis_ps_config,id',
            'nominals' => 'required|array',
            'nominals.*' => 'numeric|min:0',
        ]);

        $amplop = JadwalAmplop::create([
            'jadwal_id' => $jadwal->id_jadwal,
            'warga_id' => $request->warga_id,
            'nama_pemberi' => $request->nama_pemberi,
            'catatan' => $request->catatan,
        ]);

        foreach ($request->jenis_ps_ids as $i => $jenisPsId) {
            $nominal = $request->nominals[$i] ?? 0;
            if ($nominal > 0) {
                // Get kode_akun_id from jenis_ps_config
                $config = DB::table('jenis_ps_config')->find($jenisPsId);
                if ($config) {
                    $akunId = DB::table('kode_akun')->where('kode', $config->kode_akun)->value('id');
                    if ($akunId) {
                        JadwalAmplopDetail::create([
                            'amplop_id' => $amplop->id,
                            'kode_akun_id' => $akunId,
                            'nominal' => $nominal,
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Amplop persembahan ditambahkan.');
    }

    public function destroyAmplop(JadwalAmplop $amplop)
    {
        $amplop->detail()->delete();
        $amplop->delete();
        return back()->with('success', 'Amplop dihapus.');
    }

    // --- FINALIZE (insert transaksi) ---
    public function finalize(JadwalIbadah $jadwal)
    {
        if ($jadwal->status === 'completed') {
            return back()->with('error', 'Jadwal sudah difinalisasi.');
        }

        DB::transaction(function () use ($jadwal) {
            // SAFETY: Delete any existing auto-generated transactions for this jadwal
            // This prevents duplicates if user unfinalizes and re-finalizes
            Transaksi::where('jadwal_ibadah_id', $jadwal->id)->delete();

            // Kantong → transaksi
            foreach ($jadwal->kantong as $k) {
                if ($k->total > 0) {
                    $this->createTransaksiFromJadwal($jadwal, $k->kode_akun_id, $k->total, "Kantong: {$k->label}");
                }
            }

            // Amplop PS → transaksi + transaksi_ps
            $amplopByAkun = [];
            foreach ($jadwal->amplop as $a) {
                foreach ($a->detail as $d) {
                    $key = $d->kode_akun_id;
                    if (!isset($amplopByAkun[$key])) $amplopByAkun[$key] = 0;
                    $amplopByAkun[$key] += $d->nominal;

                    // Also create transaksi_ps per amplop detail
                    $psConfig = DB::table('jenis_ps_config')->where('kode_akun', $d->kodeAkun->kode ?? '')->first();
                    $jenisPs = $psConfig->nama ?? ($d->kodeAkun->keterangan ?? $d->kodeAkun->nama ?? 'PS');
                    $kantongDana = $psConfig->kantong_dana ?? 'rutin';

                    $bulanStr = $jadwal->tanggal->format('Y-m');
                    $lastKuitansi = TransaksiPS::where('bulan', $bulanStr)->max('no_kuitansi');
                    $nextNum = 1;
                    if ($lastKuitansi) {
                        $parts = explode('-', $lastKuitansi);
                        $nextNum = ((int)end($parts)) + 1;
                    }
                    $noKuitansi = sprintf('PS-%s-%04d', str_replace('-', '-', $bulanStr), $nextNum);

                    TransaksiPS::create([
                        'jemaat_id' => $a->warga_id,
                        'tanggal' => $jadwal->tanggal,
                        'bulan' => $bulanStr,
                        'jenis_ps' => $jenisPs,
                        'kode_akun_id' => $d->kode_akun_id,
                        'kantong_dana' => $kantongDana,
                        'jumlah' => $d->nominal,
                        'diterima_oleh' => auth()->user()->nama ?? 'Sistem',
                        'no_kuitansi' => $noKuitansi,
                        'status' => 'verified',
                        'sumber' => 'jadwal',
                        'jadwal_ibadah_id' => $jadwal->id_jadwal,
                        'created_by' => auth()->id(),
                    ]);
                }
            }
            foreach ($amplopByAkun as $akunId => $total) {
                if ($total > 0) {
                    $this->createTransaksiFromJadwal($jadwal, $akunId, $total, "Amplop persembahan ({$jadwal->amplop->count()} amplop)");
                }
            }

            // Insentif → transaksi pengeluaran (only petugas with insentif > 0)
            $totalInsentif = $jadwal->petugas->where('kategori', 'insentif')->sum('nominal_insentif');
            if ($totalInsentif > 0) {
                // Always use default II(APg).01.01
                $akunInsentifId = KodeAkun::where('kode', 'II(APg).01.01')->value('id');
                if ($akunInsentifId) {
                    Transaksi::create([
                        'tanggal' => $jadwal->tanggal,
                        'kode_akun_id' => $akunInsentifId,
                        'tipe' => 'pengeluaran',
                        'jumlah' => $totalInsentif,
                        'keterangan' => "Insentif petugas {$jadwal->jenis_ibadah} ({$jadwal->petugas->where('kategori','insentif')->count()} orang)",
                        'jenis_ibadah' => $jadwal->jenis_ibadah,
                        'jadwal_ibadah_id' => $jadwal->id,
                        'bulan' => $jadwal->tanggal->month,
                        'tahun' => $jadwal->tanggal->year,
                        'kantong_dana' => 'rutin',
                        'status' => 'verified',
                        'dibuat_oleh' => auth()->id(),
                    ]);
                }
            }

            $jadwal->update(['status' => 'completed']);
        });

        return back()->with('success', 'Jadwal difinalisasi. Transaksi telah dicatat otomatis.');
    }

    public function unfinalize(JadwalIbadah $jadwal)
    {
        if ($jadwal->status !== 'completed') {
            return back()->with('error', 'Jadwal belum difinalisasi.');
        }

        DB::transaction(function () use ($jadwal) {
            // Delete ALL auto-generated transactions linked to this jadwal
            Transaksi::where('jadwal_ibadah_id', $jadwal->id)->delete();
            // Also delete PS records generated from jadwal finalize
            TransaksiPS::where('jadwal_ibadah_id', $jadwal->id_jadwal)->where('sumber', 'jadwal')->delete();

            $jadwal->update(['status' => 'scheduled']);
        });

        return back()->with('success', 'Finalisasi dibatalkan. Transaksi otomatis telah dihapus. Anda bisa mengedit kembali.');
    }

    private function createTransaksiFromJadwal(JadwalIbadah $jadwal, int $akunId, float $jumlah, string $ket)
    {
        $akun = KodeAkun::find($akunId);
        if (!$akun) return;

        $kantong = 'rutin';
        if ($akun->nomor_up == 8) $kantong = 'phbg';
        elseif ($akun->nomor_up == 9) $kantong = 'pembangunan';
        elseif ($akun->nomor_up == 10) $kantong = 'diakonia_sosial';
        elseif ($akun->nomor_up == 11) $kantong = 'diakonia_kedukaan';
        if ($akun->kode === 'I(APn).01.03') $kantong = 'cadangan';

        Transaksi::create([
            'tanggal' => $jadwal->tanggal,
            'kode_akun_id' => $akunId,
            'tipe' => $akun->tipe,
            'jumlah' => $jumlah,
            'keterangan' => $ket . " — {$jadwal->jenis_ibadah}",
            'jenis_ibadah' => $jadwal->jenis_ibadah,
            'jadwal_ibadah_id' => $jadwal->id,
            'bulan' => $jadwal->tanggal->month,
            'tahun' => $jadwal->tanggal->year,
            'kantong_dana' => $kantong,
            'status' => 'verified',
            'dibuat_oleh' => auth()->id(),
        ]);
    }

    // --- PETUGAS MASTER (Tab 3) ---
    public function petugasMaster()
    {
        $petugas = Petugas::with('warga', 'roles')->orderBy('nama_petugas')->get();
        $wargaList = WargaJemaat::where('status_jemaat', 'aktif')->orderBy('nama_lengkap')->get();
        $insentifConfigs = \App\Models\InsentifConfig::where('aktif', true)->orderBy('jenis_tugas')->get();
        return view('jadwal.petugas', compact('petugas', 'wargaList', 'insentifConfigs'));
    }

    public function storePetugasMaster(Request $request)
    {
        $request->validate([
            'nama_petugas' => 'required|string|max:100',
        ]);

        $petugas = Petugas::create([
            'nama_petugas' => $request->nama_petugas,
            'warga_id' => $request->warga_id,
            'no_telepon' => $request->no_telepon,
            'kategori_petugas' => $request->kategori_petugas ?? 'insentif',
            'is_pendeta_sion' => $request->boolean('is_pendeta_sion'),
        ]);

        // Add initial role(s)
        if ($request->jenis_tugas) {
            PetugasRole::create([
                'petugas_id' => $petugas->id_petugas,
                'jenis_tugas' => $request->jenis_tugas,
                'berlaku_mulai' => now()->toDateString(),
            ]);
        }

        return back()->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function updatePetugasMaster(Request $request, Petugas $petugas)
    {
        $petugas->update([
            'nama_petugas' => $request->nama_petugas ?? $petugas->nama_petugas,
            'no_telepon' => $request->no_telepon ?? $petugas->no_telepon,
            'status' => $request->status ?? $petugas->status,
            'kategori_petugas' => $request->kategori_petugas ?? $petugas->kategori_petugas,
            'is_pendeta_sion' => $request->boolean('is_pendeta_sion'),
        ]);
        return back()->with('success', 'Petugas berhasil diupdate.');
    }

    public function destroyPetugasMaster(Petugas $petugas)
    {
        $petugas->delete();
        return back()->with('success', 'Petugas berhasil dihapus.');
    }

    // --- PETUGAS ROLE MANAGEMENT ---
    public function addPetugasRole(Request $request, Petugas $petugas)
    {
        $request->validate([
            'jenis_tugas' => 'required|string|max:50',
            'insentif' => 'required|numeric|min:0',
        ]);

        PetugasRole::create([
            'petugas_id' => $petugas->id_petugas,
            'jenis_tugas' => $request->jenis_tugas,
            'berlaku_mulai' => now()->toDateString(),
        ]);

        return back()->with('success', "Role '{$request->jenis_tugas}' berhasil ditambahkan.");
    }

    public function deletePetugasRole(PetugasRole $role)
    {
        $role->delete();
        return back()->with('success', 'Role berhasil dihapus.');
    }

    // --- INSENTIF TAB ---
    public function insentif(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $data = JadwalPetugas::with('petugas', 'jadwal')
            ->whereHas('jadwal', fn($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun))
            ->where('kategori', 'insentif')
            ->where('nominal_insentif', '>', 0)
            ->get()
            ->groupBy(fn($jp) => $jp->is_external ? 'ext_'.$jp->nama_external : 'pet_'.$jp->petugas_id)
            ->map(function ($items) {
                $first = $items->first();
                $name = $first->is_external ? $first->nama_external : ($first->petugas->nama_petugas ?? 'Unknown');
                return [
                    'nama' => $name,
                    'is_external' => $first->is_external,
                    'petugas' => $first->petugas,
                    'jumlah_tugas' => $items->count(),
                    'total_insentif' => $items->sum('nominal_insentif'),
                    'jadwal' => $items->map(fn($jp) => [
                        'tanggal' => $jp->jadwal->tanggal->format('d/m/Y'),
                        'jenis' => $jp->jadwal->jenis_ibadah,
                        'tugas' => $jp->jenis_tugas,
                        'nominal' => $jp->nominal_insentif,
                    ]),
                ];
            })
            ->sortByDesc('total_insentif');

        $quota = DB::table('system_settings')->where('setting_key', 'petugas_quota_per_bulan')->value('setting_value') ?? 4;

        return view('jadwal.insentif', compact('data', 'bulan', 'tahun', 'quota'));
    }

    // --- CUSTOM JENIS IBADAH ---
    public function storeJenisIbadah(Request $request)
    {
        $request->validate(['nama_jenis' => 'required|string|max:100']);

        $exists = DB::table('jenis_ibadah_master')->where('nama_jenis', $request->nama_jenis)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Sudah ada']);
        }

        DB::table('jenis_ibadah_master')->insert([
            'nama_jenis' => $request->nama_jenis,
            'keterangan' => 'Custom',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // --- INSENTIF CONFIG SETTING ---
    public function insentifSetting()
    {
        $configs = \App\Models\InsentifConfig::orderBy('tipe_event')->orderBy('jenis_tugas')->get();
        $jenisIbadah = DB::table('jenis_ibadah_master')->orderBy('nama_jenis')->get();
        return view('jadwal.insentif-setting', compact('configs', 'jenisIbadah'));
    }

    public function storeInsentifConfig(Request $request)
    {
        $request->validate([
            'tipe_event' => 'required|string|max:50',
            'jenis_tugas' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:0',
        ]);

        \App\Models\InsentifConfig::create($request->only('tipe_event', 'jenis_tugas', 'nominal'));
        return back()->with('success', 'Konfigurasi insentif berhasil ditambahkan.');
    }

    public function updateInsentifConfig(Request $request, \App\Models\InsentifConfig $config)
    {
        $config->update($request->only('tipe_event', 'jenis_tugas', 'nominal', 'aktif'));
        return back()->with('success', 'Konfigurasi insentif berhasil diupdate.');
    }

    public function destroyInsentifConfig(\App\Models\InsentifConfig $config)
    {
        $config->delete();
        return back()->with('success', 'Konfigurasi insentif berhasil dihapus.');
    }
}
