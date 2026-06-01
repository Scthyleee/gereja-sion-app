@extends('layouts.app')
@section('title', 'CMS Public Page')

@section('content')
<ul class="nav nav-pills mb-4" style="gap:8px">
    @foreach(['hero' => ['fas fa-home', 'Hero & Tampilan'], 'pengumuman' => ['fas fa-bullhorn', 'Pengumuman'], 'ayat' => ['fas fa-book-open', 'Ayat Harian'], 'galeri' => ['fas fa-images', 'Galeri'], 'livestream' => ['fas fa-video', 'Livestream'], 'seo' => ['fas fa-search', 'SEO & Footer'], 'visibility' => ['fas fa-eye', 'Visibilitas']] as $t => $info)
    <li class="nav-item"><a href="{{ route('cms.index', ['tab'=>$t]) }}" class="nav-link {{ $tab===$t ? 'active' : '' }}" style="{{ $tab===$t ? 'background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none' : 'color:var(--text)' }}"><i class="{{ $info[0] }} me-1"></i>{{ $info[1] }}</a></li>
    @endforeach
</ul>

<div class="mb-3">
    <a href="{{ route('public.home') }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt me-1"></i>Lihat Public Page</a>
</div>

@if($tab === 'hero')
{{-- Hero & Tampilan --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-home me-2"></i>Hero Section & Warna</h6></div>
    <div class="content-card__body">
        <form action="{{ route('cms.settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Judul Utama</label>
                    <input type="text" name="hero_title" class="form-control" value="{{ $settings['hero_title'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Subtitle</label>
                    <input type="text" name="hero_subtitle" class="form-control" value="{{ $settings['hero_subtitle'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Warna Utama</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#c9a84c' }}" style="width:50px;height:38px;border:none;cursor:pointer">
                        <input type="text" class="form-control" value="{{ $settings['primary_color'] ?? '#c9a84c' }}" readonly style="max-width:120px">
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>

@elseif($tab === 'pengumuman')
{{-- Pengumuman --}}
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Pengumuman ({{ $announcements->count() }})</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPengumuman"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Judul</th><th>Periode</th><th>Prioritas</th><th>Status</th><th style="width:80px">Aksi</th></tr></thead>
            <tbody>
                @forelse($announcements as $a)
                <tr>
                    <td class="fw-semibold">{{ $a->judul }}</td>
                    <td class="small text-muted">{{ $a->tanggal_mulai ?? '-' }} — {{ $a->tanggal_selesai ?? '∞' }}</td>
                    <td><span class="badge bg-info">{{ $a->prioritas }}</span></td>
                    <td>@if($a->aktif)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Nonaktif</span>@endif</td>
                    <td>
                        <form action="{{ route('cms.announcements.destroy', $a->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="if(!confirm('Hapus?'))event.preventDefault()"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pengumuman</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div>

{{-- Modal Pengumuman --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Pengumuman</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('cms.announcements.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Judul</label><input type="text" name="judul" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-semibold">Isi Pengumuman</label><textarea name="isi" class="form-control" rows="5" required></textarea></div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-semibold">Tanggal Mulai</label><input type="date" name="tanggal_mulai" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Prioritas</label><input type="number" name="prioritas" class="form-control" value="1" min="1" max="10"></div>
                </div>
                <div class="mt-3"><label class="form-label fw-semibold">Gambar (opsional)</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
            </div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
        </form>
    </div></div>
</div>

@elseif($tab === 'ayat')
{{-- Ayat Harian --}}
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-book-open me-2"></i>Ayat Alkitab ({{ $verses->count() }})</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAyat"><i class="fas fa-plus me-1"></i>Tambah Ayat</button>
    </div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tanggal</th><th>Referensi</th><th>Teks</th><th>Versi</th><th style="width:60px"></th></tr></thead>
            <tbody>
                @forelse($verses as $v)
                <tr>
                    <td class="small">{{ $v->tanggal }}</td>
                    <td class="fw-semibold small text-primary">{{ $v->referensi }}</td>
                    <td class="small text-muted">{{ Str::limit($v->teks, 80) }}</td>
                    <td><span class="badge bg-secondary">{{ $v->versi }}</span></td>
                    <td>
                        <form action="{{ route('cms.verses.destroy', $v->id) }}" method="POST">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada ayat</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div>

<div class="modal fade" id="modalAyat" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Ayat</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('cms.verses.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Tanggal Tampil</label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Referensi</label><input type="text" name="referensi" class="form-control" placeholder="Contoh: Yohanes 3:16" required></div>
                <div class="mb-3"><label class="form-label fw-semibold">Teks Ayat</label><textarea name="teks" class="form-control" rows="4" required></textarea></div>
                <div class="mb-3"><label class="form-label fw-semibold">Versi</label><select name="versi" class="form-select"><option value="TB">TB (Terjemahan Baru)</option><option value="BIS">BIS</option></select></div>
            </div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
        </form>
    </div></div>
</div>

@elseif($tab === 'galeri')
{{-- Galeri --}}
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-images me-2"></i>Galeri Foto ({{ $gallery->count() }})</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalGaleri"><i class="fas fa-plus me-1"></i>Upload Foto</button>
    </div>
    <div class="content-card__body">
        <div class="row g-3">
            @forelse($gallery as $g)
            <div class="col-md-3">
                <div class="position-relative" style="border-radius:12px;overflow:hidden;border:1px solid var(--border)">
                    <img src="{{ asset('storage/' . $g->foto_url) }}" style="width:100%;aspect-ratio:4/3;object-fit:cover">
                    <div class="p-2">
                        <div class="small fw-semibold">{{ $g->keterangan ?: '(Tanpa keterangan)' }}</div>
                        <div class="text-muted" style="font-size:.7rem">{{ $g->tanggal_kegiatan }}</div>
                    </div>
                    <form action="{{ route('cms.gallery.destroy', $g->id) }}" method="POST" class="position-absolute" style="top:8px;right:8px">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" style="width:28px;height:28px;padding:0;border-radius:50%"><i class="fas fa-times" style="font-size:.7rem"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4"><i class="fas fa-images fa-3x mb-2 d-block" style="opacity:.3"></i>Belum ada foto di galeri</div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="modalGaleri" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Upload Foto</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('cms.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Foto (max 5MB)</label><input type="file" name="foto" class="form-control" accept="image/*" required></div>
                <div class="mb-3"><label class="form-label fw-semibold">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="Contoh: Ibadah Natal 2025"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Tanggal Kegiatan</label><input type="date" name="tanggal_kegiatan" class="form-control"></div>
            </div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button></div>
        </form>
    </div></div>
</div>

@elseif($tab === 'livestream')
{{-- Livestream --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-video me-2"></i>Pengaturan Livestream</h6></div>
    <div class="content-card__body">
        <form action="{{ route('cms.settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">URL YouTube Live</label>
                    <input type="url" name="youtube_live_url" class="form-control" value="{{ $settings['youtube_live_url'] ?? '' }}" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">URL Rekaman Terakhir (fallback)</label>
                    <input type="url" name="youtube_rekaman_url" class="form-control" value="{{ $settings['youtube_rekaman_url'] ?? '' }}" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="is_live" class="form-select">
                        <option value="0" {{ ($settings['is_live'] ?? '0') === '0' ? 'selected' : '' }}>Tidak Sedang Live</option>
                        <option value="1" {{ ($settings['is_live'] ?? '0') === '1' ? 'selected' : '' }}>🔴 Sedang Live</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>

{{-- Video Archive --}}
<div class="content-card mt-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-film me-2"></i>Arsip Video Ibadah</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalVideo"><i class="fas fa-plus me-1"></i>Tambah Video</button>
    </div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tanggal</th><th>Judul</th><th>URL</th><th>Aktif</th><th style="width:60px"></th></tr></thead>
            <tbody>
                @forelse($videos ?? [] as $vid)
                <tr>
                    <td class="small">{{ $vid->tanggal }}</td>
                    <td class="fw-semibold small">{{ $vid->judul }}</td>
                    <td class="small"><a href="{{ $vid->youtube_url }}" target="_blank">{{ Str::limit($vid->youtube_url, 40) }}</a></td>
                    <td><span class="badge bg-{{ $vid->aktif ? 'success' : 'secondary' }}">{{ $vid->aktif ? 'Ya' : 'Tidak' }}</span></td>
                    <td>
                        <form action="{{ route('cms.video.destroy', $vid->id) }}" method="POST">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada video. Tambahkan link YouTube ibadah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div>

<div class="modal fade" id="modalVideo" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Video Ibadah</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('cms.video.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Judul</label><input type="text" name="judul" class="form-control" required placeholder="e.g. Ibadah Minggu 5 Jan 2026"></div>
                <div class="mb-3"><label class="form-label fw-semibold">URL YouTube</label><input type="url" name="youtube_url" class="form-control" required placeholder="https://youtube.com/live/... atau /watch?v=..."></div>
                <div class="row g-3">
                    <div class="col-6"><label class="form-label fw-semibold">Tanggal</label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="col-6"><label class="form-label fw-semibold">Jenis Ibadah</label><input type="text" name="jenis_ibadah" class="form-control" placeholder="opsional"></div>
                </div>
            </div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
        </form>
    </div></div>
</div>

@elseif($tab === 'seo')
{{-- SEO & Footer --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-search me-2"></i>SEO & Kontak</h6></div>
    <div class="content-card__body">
        <form action="{{ route('cms.settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">Meta Title</label><input type="text" name="meta_title" class="form-control" value="{{ $settings['meta_title'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Meta Description</label><input type="text" name="meta_description" class="form-control" value="{{ $settings['meta_description'] ?? '' }}"></div>
                <div class="col-12"><hr style="border-color:var(--border)"><h6 class="fw-bold">Informasi Kontak</h6></div>
                <div class="col-md-8"><label class="form-label fw-semibold">Alamat Gereja</label><input type="text" name="alamat" class="form-control" value="{{ $settings['alamat'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Telepon</label><input type="text" name="telepon" class="form-control" value="{{ $settings['telepon'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Email Kontak</label><input type="email" name="email_kontak" class="form-control" value="{{ $settings['email_kontak'] ?? '' }}"></div>
                <div class="col-md-8"><label class="form-label fw-semibold">Jam Pelayanan</label><input type="text" name="jam_pelayanan" class="form-control" value="{{ $settings['jam_pelayanan'] ?? '' }}"></div>
                <div class="col-12"><hr style="border-color:var(--border)"><h6 class="fw-bold">Media Sosial</h6></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Facebook</label><input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Instagram</label><input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">YouTube</label><input type="url" name="youtube_url" class="form-control" value="{{ $settings['youtube_url'] ?? '' }}"></div>
                <div class="col-12"><label class="form-label fw-semibold">Google Maps Embed URL</label><input type="text" name="google_maps_embed" class="form-control" value="{{ $settings['google_maps_embed'] ?? '' }}" placeholder="Paste src URL dari Google Maps → Share → Embed a map"><small class="text-muted">Buka Google Maps → klik Share → Embed a map → copy URL dari atribut <code>src="..."</code></small></div>
                <div class="col-12"><hr style="border-color:var(--border)"><h6 class="fw-bold"><i class="fas fa-hand-holding-heart me-2"></i>Rekening Sumbangan</h6></div>
                <div class="col-md-3"><label class="form-label fw-semibold">Nama Bank</label><input type="text" name="rekening_bank" class="form-control" value="{{ $settings['rekening_bank'] ?? 'BRI' }}" placeholder="BRI"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Nomor Rekening</label><input type="text" name="rekening_nomor" class="form-control" value="{{ $settings['rekening_nomor'] ?? '' }}" placeholder="1234-5678-9012"></div>
                <div class="col-md-5"><label class="form-label fw-semibold">Atas Nama</label><input type="text" name="rekening_atas_nama" class="form-control" value="{{ $settings['rekening_atas_nama'] ?? '' }}" placeholder="Majelis Jemaat GKE..."></div>
                <div class="col-md-6"><label class="form-label fw-semibold">WhatsApp Kirim Bukti Transfer</label><input type="text" name="whatsapp_sumbangan" class="form-control" value="{{ $settings['whatsapp_sumbangan'] ?? '' }}" placeholder="6281234567890"><small class="text-muted">Format: 6281234567890 (tanpa + atau spasi)</small></div>
                <div class="col-12"><hr style="border-color:var(--border)"><h6 class="fw-bold"><i class="fas fa-file-signature me-2"></i>Pejabat Penandatangan Dokumen</h6><small class="text-muted">Nama & jabatan ini akan tampil di semua PDF (Kwitansi, Surat Tugas, Rekap, dll)</small></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Nama Ketua MPH</label><input type="text" name="nama_ketua" class="form-control" value="{{ $settings['nama_ketua'] ?? '' }}" placeholder="Pdt. Ayang Setiawan, M.Th"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Jabatan Ketua</label><input type="text" name="jabatan_ketua" class="form-control" value="{{ $settings['jabatan_ketua'] ?? 'Ketua MPH' }}" placeholder="Ketua MPH"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Nama Sekretaris</label><input type="text" name="nama_sekretaris" class="form-control" value="{{ $settings['nama_sekretaris'] ?? '' }}" placeholder="Pnt. ..."></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Jabatan Sekretaris</label><input type="text" name="jabatan_sekretaris" class="form-control" value="{{ $settings['jabatan_sekretaris'] ?? 'Sekretaris MPH' }}" placeholder="Sekretaris MPH"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Nama Bendahara</label><input type="text" name="nama_bendahara" class="form-control" value="{{ $settings['nama_bendahara'] ?? '' }}" placeholder="Pnt. Yuawanku"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Jabatan Bendahara</label><input type="text" name="jabatan_bendahara" class="form-control" value="{{ $settings['jabatan_bendahara'] ?? 'Bendahara MPH' }}" placeholder="Bendahara MPH"></div>
            </div>
            <button class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>

@elseif($tab === 'visibility')
{{-- Visibilitas Section --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-eye me-2"></i>Toggle Visibilitas Section</h6></div>
    <div class="content-card__body">
        <form action="{{ route('cms.settings.update') }}" method="POST">
            @csrf
            <p class="text-muted small mb-3">Aktifkan atau nonaktifkan section yang tampil di halaman public.</p>
            @foreach(['section_jadwal' => 'Jadwal Ibadah', 'section_warta' => 'Warta Jemaat', 'section_pengumuman' => 'Pengumuman', 'section_ayat' => 'Ayat Harian', 'section_ibadah_minggu' => 'Info Ibadah Minggu (Pendeta, Tema, Ayat)', 'section_ultah' => 'Ulang Tahun Jemaat', 'section_sumbangan' => 'Rekening Sumbangan', 'section_livestream' => 'Livestream', 'section_statistik' => 'Statistik Jemaat', 'section_galeri' => 'Galeri Kegiatan'] as $key => $label)
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="{{ $key }}" value="0">
                <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}" {{ ($settings[$key] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="{{ $key }}">{{ $label }}</label>
            </div>
            @endforeach
            <button class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>

@elseif($tab === 'maintenance')
{{-- Maintenance Mode --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-tools me-2"></i>Mode Maintenance</h6></div>
    <div class="content-card__body">
        <form action="{{ route('cms.settings.update') }}" method="POST">
            @csrf
            <div class="alert" style="background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);color:var(--text)">
                <i class="fas fa-info-circle text-warning me-1"></i>
                <strong>Super Admin</strong> selalu bisa mengakses admin panel dan public page meskipun maintenance aktif.
            </div>
            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="p-4 rounded" style="border:1px solid var(--border);background:rgba(0,0,0,.2)">
                        <h6 class="fw-bold mb-3"><i class="fas fa-globe me-2"></i>Public Page (gkepalangka.id)</h6>
                        <div class="form-check form-switch">
                            <input type="hidden" name="maintenance_public" value="0">
                            <input class="form-check-input" type="checkbox" name="maintenance_public" value="1" id="maintPub" {{ ($settings['maintenance_public'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="maintPub">
                                Maintenance Public
                                @if(($settings['maintenance_public'] ?? '0') === '1')
                                <span class="badge bg-danger ms-1">AKTIF</span>
                                @else
                                <span class="badge bg-success ms-1">NORMAL</span>
                                @endif
                            </label>
                        </div>
                        <small class="text-muted">Jemaat akan melihat halaman maintenance.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 rounded" style="border:1px solid var(--border);background:rgba(0,0,0,.2)">
                        <h6 class="fw-bold mb-3"><i class="fas fa-lock me-2"></i>Admin Panel (/admin)</h6>
                        <div class="form-check form-switch">
                            <input type="hidden" name="maintenance_admin" value="0">
                            <input class="form-check-input" type="checkbox" name="maintenance_admin" value="1" id="maintAdmin" {{ ($settings['maintenance_admin'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="maintAdmin">
                                Maintenance Admin
                                @if(($settings['maintenance_admin'] ?? '0') === '1')
                                <span class="badge bg-danger ms-1">AKTIF</span>
                                @else
                                <span class="badge bg-success ms-1">NORMAL</span>
                                @endif
                            </label>
                        </div>
                        <small class="text-muted">Staf non-Super Admin tidak bisa masuk.</small>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>
@endif
@endsection
