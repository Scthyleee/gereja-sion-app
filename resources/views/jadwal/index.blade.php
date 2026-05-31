@extends('layouts.app')
@section('title', 'Jadwal & Pelayanan')

@section('content')
{{-- Tabs --}}
<ul class="nav nav-pills mb-4" style="gap:8px">
    <li class="nav-item">
        <a href="{{ route('jadwal.index', ['tab' => 'jadwal', 'bulan' => $bulan, 'tahun' => $tahun]) }}"
           class="nav-link {{ $tab === 'jadwal' ? 'active' : '' }}" style="{{ $tab === 'jadwal' ? 'background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none' : 'color:var(--text)' }}">
            <i class="fas fa-calendar-alt me-1"></i>Jadwal
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('insentif.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
           class="nav-link" style="color:var(--text)">
            <i class="fas fa-money-bill me-1"></i>Insentif
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('petugas.index') }}"
           class="nav-link" style="color:var(--text)">
            <i class="fas fa-users-cog me-1"></i>Petugas
        </a>
    </li>
</ul>

{{-- Filter --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <form method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="jadwal">
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Bulan</label>
                        <select name="bulan" class="form-select" style="width:130px">
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" @selected($bulan==$m)>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Tahun</label>
                        <input type="number" name="tahun" class="form-control" style="width:100px" value="{{ $tahun }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end d-flex justify-content-end gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-print me-1"></i>Cetak Mingguan</button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:280px;background:var(--bg-card);border-color:var(--border)">
                        <form action="{{ route('print.jadwal-mingguan') }}" method="GET" target="_blank">
                            <div class="mb-2"><label class="form-label small fw-semibold">Dari</label><input type="date" name="tanggal_awal" class="form-control form-control-sm" value="{{ now()->startOfWeek()->format('Y-m-d') }}"></div>
                            <div class="mb-2"><label class="form-label small fw-semibold">Sampai</label><input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="{{ now()->endOfWeek()->format('Y-m-d') }}"></div>
                            <button class="btn btn-sm btn-primary w-100"><i class="fas fa-file-pdf me-1"></i>Cetak PDF</button>
                        </form>
                    </div>
                </div>
                @if(auth()->user()->canEdit('jadwal'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuat">
                    <i class="fas fa-plus me-1"></i>Buat Jadwal
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Jadwal Cards --}}
<div class="row g-3">
    @forelse($jadwal as $jd)
    <div class="col-md-6 col-lg-4">
        <div class="content-card h-100">
            <div class="content-card__header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $jd->display_name }}</h6>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>{{ $jd->tanggal->translatedFormat('d M Y') }}
                        @if($jd->waktu) · {{ substr($jd->waktu, 0, 5) }}@if($jd->jam_selesai)–{{ substr($jd->jam_selesai, 0, 5) }}@endif @endif
                    </small>
                    <div class="mt-1">
                        @if($jd->kategori && $jd->kategori !== 'ibadah_rutin')<span class="badge bg-secondary me-1" style="font-size:.65rem">{{ ucwords(str_replace('_',' ',$jd->kategori)) }}</span>@endif
                        @if($jd->lokasi_tipe === 'rumah_jemaat')<span class="badge bg-warning text-dark" style="font-size:.65rem"><i class="fas fa-house-user me-1"></i>Rumah Jemaat</span>@elseif($jd->lokasi_tipe === 'gedung_lain')<span class="badge bg-info" style="font-size:.65rem"><i class="fas fa-building me-1"></i>Gedung Lain</span>@endif
                    </div>
                </div>
                <span class="badge bg-{{ $jd->status === 'completed' ? 'success' : ($jd->status === 'cancelled' ? 'danger' : 'warning') }}">
                    {{ $jd->status === 'completed' ? 'Selesai' : ($jd->status === 'cancelled' ? 'Batal' : 'Terjadwal') }}
                </span>
            </div>
            <div class="content-card__body py-2">
                <div class="row g-2 small">
                    <div class="col-6">
                        <div class="text-muted"><i class="fas fa-users me-1"></i>Petugas</div>
                        <div class="fw-semibold">{{ $jd->petugas->count() }} orang</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted"><i class="fas fa-box me-1"></i>Kantong</div>
                        <div class="fw-semibold">Rp {{ number_format($jd->total_kantong, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted"><i class="fas fa-envelope me-1"></i>Amplop</div>
                        <div class="fw-semibold">{{ $jd->amplop->count() }} KK</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted"><i class="fas fa-hand-holding-usd me-1"></i>Total</div>
                        <div class="fw-semibold text-success">Rp {{ number_format($jd->total_persembahan, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="content-card__body border-top pt-2 d-flex gap-2">
                <a href="{{ route('jadwal.show', $jd) }}" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="fas fa-eye me-1"></i>Detail
                </a>
                @if($jd->status !== 'completed')
                <form action="{{ route('jadwal.destroy', $jd) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="content-card">
            <div class="content-card__body text-center py-5 text-muted">
                <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                <h5>Belum ada jadwal</h5>
                <p class="small">Klik "Buat Jadwal" untuk membuat jadwal ibadah baru.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Buat Jadwal --}}
<div class="modal fade" id="modalBuat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Buat Jadwal Baru</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jenis Ibadah</label>
                            <select name="jenis_ibadah" class="form-select select-search" required id="selJenisIbadah">
                                @foreach($jenisIbadah as $j)<option>{{ $j->nama_jenis }}</option>@endforeach
                            </select>
                            <div class="mt-1 d-flex gap-1">
                                <input type="text" class="form-control form-control-sm" id="inputNewJenis" placeholder="Tambah jenis baru..." style="font-size:.8rem">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addJenisIbadah()" style="white-space:nowrap"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Judul Ibadah <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="judul_ibadah" class="form-control" placeholder="e.g. Ibadah Tutup Tahun di Gereja Sion">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="ibadah_rutin">Ibadah Rutin</option>
                                <option value="ibadah_khusus">Ibadah Khusus</option>
                                <option value="pernikahan">Pernikahan</option>
                                <option value="baptis">Baptisan</option>
                                <option value="pemakaman">Pemakaman</option>
                                <option value="acara_keluarga">Acara Keluarga</option>
                                <option value="ulang_tahun">Ulang Tahun</option>
                                <option value="acara_gereja">Acara Gereja</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Jam Mulai</label>
                            <input type="time" name="waktu" class="form-control" value="09:00">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="11:00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Lokasi Tipe</label>
                            <select name="lokasi_tipe" class="form-select" id="selLokasiTipe" onchange="toggleLokasiAlamat()">
                                <option value="gereja">Gereja</option>
                                <option value="rumah_jemaat">Rumah Jemaat</option>
                                <option value="gedung_lain">Gedung Lain</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Gereja SION" value="Gereja SION">
                        </div>
                        <div class="col-12" id="divLokasiAlamat" style="display:none">
                            <label class="form-label fw-semibold">Alamat Lengkap Lokasi</label>
                            <input type="text" name="lokasi_alamat" class="form-control" placeholder="Jl. ... (alamat lengkap)">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleLokasiAlamat() {
    const val = document.getElementById('selLokasiTipe').value;
    document.getElementById('divLokasiAlamat').style.display = val !== 'gereja' ? '' : 'none';
}

function addJenisIbadah() {
    const input = document.getElementById('inputNewJenis');
    const nama = input.value.trim();
    if (!nama) return;
    fetch('{{ route("jadwal.jenis-ibadah.store") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ nama_jenis: nama })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            // Reload the select with TomSelect
            const sel = document.getElementById('selJenisIbadah');
            const ts = sel.tomselect;
            if (ts) {
                ts.addOption({ value: nama, text: nama });
                ts.setValue(nama);
            } else {
                const opt = new Option(nama, nama, true, true);
                sel.add(opt);
            }
            input.value = '';
            // Show toast
            if (window.showToast) showToast('Jenis ibadah "' + nama + '" berhasil ditambahkan!', 'success');
        }
    }).catch(() => alert('Gagal menambahkan jenis ibadah'));
}
</script>
@endpush
