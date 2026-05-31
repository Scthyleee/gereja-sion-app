<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PublicController extends Controller
{
    public function index()
    {
        $settings = DB::table('cms_settings')->pluck('value', 'key');

        // Check maintenance
        if (($settings['maintenance_public'] ?? '0') === '1') {
            // Super admin bypass
            if (auth()->check() && auth()->user()->role_id === 1) {
                // bypass
            } else {
                return response()->view('public.maintenance', ['settings' => $settings], 503);
            }
        }

        $profil = DB::table('profil_gereja')->first();

        // Upcoming schedules (next 5)
        $jadwal = collect();
        if (($settings['section_jadwal'] ?? '1') === '1') {
            $jadwal = DB::table('jadwal_ibadah')
                ->where('tanggal', '>=', Carbon::today())
                ->orderBy('tanggal')
                ->orderBy('waktu')
                ->limit(5)
                ->get();
        }

        // Latest warta (last 3)
        $warta = collect();
        if (($settings['section_warta'] ?? '1') === '1') {
            $warta = DB::table('warta_mingguan')
                ->orderByDesc('tanggal_akhir')
                ->limit(3)
                ->get();
        }

        // Active announcements
        $pengumuman = collect();
        if (($settings['section_pengumuman'] ?? '1') === '1') {
            $pengumuman = DB::table('cms_announcements')
                ->where('aktif', true)
                ->where(function ($q) {
                    $q->whereNull('tanggal_selesai')
                      ->orWhere('tanggal_selesai', '>=', Carbon::today());
                })
                ->where(function ($q) {
                    $q->whereNull('tanggal_mulai')
                      ->orWhere('tanggal_mulai', '<=', Carbon::today());
                })
                ->orderByDesc('prioritas')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        // Today's verse
        $ayat = null;
        if (($settings['section_ayat'] ?? '1') === '1') {
            $ayat = DB::table('cms_verses')
                ->where('tanggal', Carbon::today()->toDateString())
                ->first();
            if (!$ayat) {
                $ayat = DB::table('cms_verses')->orderByDesc('tanggal')->first();
            }
        }

        // Gallery
        $galeri = collect();
        if (($settings['section_galeri'] ?? '1') === '1') {
            $galeri = DB::table('cms_gallery')
                ->where('aktif', true)
                ->orderBy('urutan')
                ->limit(12)
                ->get();
        }

        // Stats
        $stats = null;
        if (($settings['section_statistik'] ?? '1') === '1') {
            $stats = (object) [
                'jemaat' => DB::table('warga_jemaat')->count(),
                'keluarga' => DB::table('keluarga')->count(),
                'kegiatan_bulan_ini' => DB::table('jadwal_ibadah')
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereYear('tanggal', Carbon::now()->year)
                    ->count(),
            ];
        }

        // Ibadah Minggu terdekat (with petugas info)
        $ibadahMinggu = null;
        if (($settings['section_ibadah_minggu'] ?? '1') === '1') {
            $nextSunday = Carbon::now()->next(Carbon::SUNDAY);
            $ibm = DB::table('jadwal_ibadah')
                ->where('tanggal', '>=', Carbon::today())
                ->where('tanggal', '<=', $nextSunday)
                ->where(function ($q) {
                    $q->where('jenis_ibadah', 'like', '%Minggu%')
                      ->orWhere('jenis_ibadah', 'like', '%minggu%');
                })
                ->orderBy('tanggal')
                ->first();

            if ($ibm) {
                // Get pendeta pelayanan
                $pendeta = DB::table('jadwal_petugas')
                    ->leftJoin('petugas', 'jadwal_petugas.petugas_id', '=', 'petugas.id_petugas')
                    ->where('jadwal_petugas.jadwal_id', $ibm->id_jadwal)
                    ->where('jadwal_petugas.jenis_tugas', 'like', '%Pendeta%')
                    ->select(DB::raw('COALESCE(jadwal_petugas.nama_external, petugas.nama_petugas) as nama'))
                    ->first();

                // Get pembacaan alkitab
                $pembacaan = DB::table('jadwal_petugas')
                    ->leftJoin('petugas', 'jadwal_petugas.petugas_id', '=', 'petugas.id_petugas')
                    ->where('jadwal_petugas.jadwal_id', $ibm->id_jadwal)
                    ->where('jadwal_petugas.jenis_tugas', 'like', '%Pembacaan%')
                    ->select(DB::raw('COALESCE(jadwal_petugas.nama_external, petugas.nama_petugas) as nama'))
                    ->first();

                $ibadahMinggu = (object) [
                    'tanggal' => $ibm->tanggal,
                    'waktu' => $ibm->waktu,
                    'lokasi' => $ibm->lokasi,
                    'tema' => $ibm->tema_ibadah,
                    'ayat_khotbah' => $ibm->ayat_khotbah,
                    'pendeta' => $pendeta->nama ?? null,
                    'pembacaan' => $pembacaan->nama ?? null,
                ];
            }
        }
        // Ulang tahun jemaat minggu ini
        $ultahJemaat = collect();
        if (($settings['section_ultah'] ?? '1') === '1') {
            $today = Carbon::today();
            $weekLater = Carbon::today()->addDays(7);

            $ultahJemaat = DB::table('warga_jemaat')
                ->whereNotNull('tanggal_lahir')
                ->where('status_jemaat', 'aktif')
                ->get(['nama_lengkap', 'tanggal_lahir'])
                ->filter(function ($w) use ($today, $weekLater) {
                    $lahir = Carbon::parse($w->tanggal_lahir);
                    // Set birthday to this year
                    $bday = $lahir->copy()->year($today->year);
                    // Handle year boundary (Dec → Jan)
                    if ($bday->lt($today->copy()->subDay())) {
                        $bday->addYear();
                    }
                    return $bday->between($today->copy()->subDay(), $weekLater);
                })
                ->map(function ($w) use ($today) {
                    $lahir = Carbon::parse($w->tanggal_lahir);
                    $bday = $lahir->copy()->year($today->year);
                    if ($bday->lt($today->copy()->subDay())) $bday->addYear();
                    $w->usia = $bday->year - $lahir->year;
                    $w->tanggal_ultah = $bday->translatedFormat('d F');
                    $w->is_today = $bday->isSameDay($today);
                    return $w;
                })
                ->sortBy(function ($w) use ($today) {
                    $lahir = Carbon::parse($w->tanggal_lahir);
                    $bday = $lahir->copy()->year($today->year);
                    if ($bday->lt($today->copy()->subDay())) $bday->addYear();
                    return $bday->timestamp;
                })
                ->values();
        }
        // Video archive (filter by month)
        $videoArchive = collect();
        if (($settings['section_livestream'] ?? '1') === '1') {
            $videoBulan = request('video_bulan', now()->month);
            $videoArchive = DB::table('video_ibadah')
                ->where('aktif', true)
                ->whereMonth('tanggal', $videoBulan)
                ->whereYear('tanggal', now()->year)
                ->orderBy('tanggal', 'desc')
                ->limit(12)
                ->get();
        }

        return view('public.index', compact('settings', 'profil', 'jadwal', 'warta', 'pengumuman', 'ayat', 'galeri', 'stats', 'ibadahMinggu', 'ultahJemaat', 'videoArchive'));
    }
}
