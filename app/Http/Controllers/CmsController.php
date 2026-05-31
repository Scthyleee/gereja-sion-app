<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'hero');
        $settings = DB::table('cms_settings')->pluck('value', 'key');
        $announcements = DB::table('cms_announcements')->orderByDesc('prioritas')->orderByDesc('created_at')->get();
        $gallery = DB::table('cms_gallery')->orderBy('urutan')->get();
        $verses = DB::table('cms_verses')->orderByDesc('tanggal')->limit(30)->get();
        $videos = DB::table('video_ibadah')->orderByDesc('tanggal')->limit(50)->get();

        return view('cms.index', compact('tab', 'settings', 'announcements', 'gallery', 'verses', 'videos'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            DB::table('cms_settings')->where('key', $key)->update([
                'value' => $value ?? '',
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Pengaturan CMS berhasil disimpan.');
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
        ]);

        $data = [
            'judul' => $request->judul,
            'isi' => $request->isi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'prioritas' => $request->prioritas ?? 1,
            'aktif' => true,
            'dibuat_oleh' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('gambar')) {
            $data['gambar_url'] = $request->file('gambar')->store('cms/announcements', 'public');
        }

        DB::table('cms_announcements')->insert($data);
        return back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function updateAnnouncement(Request $request, int $id)
    {
        $data = [
            'judul' => $request->judul,
            'isi' => $request->isi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'prioritas' => $request->prioritas ?? 1,
            'aktif' => $request->has('aktif'),
            'updated_at' => now(),
        ];

        DB::table('cms_announcements')->where('id', $id)->update($data);
        return back()->with('success', 'Pengumuman diperbarui.');
    }

    public function destroyAnnouncement(int $id)
    {
        $ann = DB::table('cms_announcements')->where('id', $id)->first();
        if ($ann && $ann->gambar_url) {
            Storage::disk('public')->delete($ann->gambar_url);
        }
        DB::table('cms_announcements')->where('id', $id)->delete();
        return back()->with('success', 'Pengumuman dihapus.');
    }

    public function storeGallery(Request $request)
    {
        $request->validate(['foto' => 'required|image|max:5120']);

        $path = $request->file('foto')->store('cms/gallery', 'public');
        DB::table('cms_gallery')->insert([
            'foto_url' => $path,
            'keterangan' => $request->keterangan ?? '',
            'tanggal_kegiatan' => $request->tanggal_kegiatan,
            'urutan' => $request->urutan ?? 1,
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function destroyGallery(int $id)
    {
        $gal = DB::table('cms_gallery')->where('id', $id)->first();
        if ($gal && $gal->foto_url) {
            Storage::disk('public')->delete($gal->foto_url);
        }
        DB::table('cms_gallery')->where('id', $id)->delete();
        return back()->with('success', 'Foto dihapus.');
    }

    public function storeVerse(Request $request)
    {
        $request->validate([
            'teks' => 'required|string',
            'referensi' => 'required|string|max:100',
        ]);

        DB::table('cms_verses')->insert([
            'tanggal' => $request->tanggal ?? now()->toDateString(),
            'teks' => $request->teks,
            'referensi' => $request->referensi,
            'versi' => $request->versi ?? 'TB',
            'sumber' => 'manual',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Ayat berhasil ditambahkan.');
    }

    public function destroyVerse(int $id)
    {
        DB::table('cms_verses')->where('id', $id)->delete();
        return back()->with('success', 'Ayat dihapus.');
    }

    public function storeVideo(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'youtube_url' => 'required|url',
            'tanggal' => 'required|date',
        ]);

        DB::table('video_ibadah')->insert([
            'judul' => $request->judul,
            'youtube_url' => $request->youtube_url,
            'tanggal' => $request->tanggal,
            'jenis_ibadah' => $request->jenis_ibadah,
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Video berhasil ditambahkan.');
    }

    public function destroyVideo(int $id)
    {
        DB::table('video_ibadah')->where('id', $id)->delete();
        return back()->with('success', 'Video dihapus.');
    }
}
