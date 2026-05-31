@extends('layouts.app')
@section('title', 'Pedoman & RAPP')

@section('content')
<ul class="nav nav-pills mb-4" style="gap:8px">
    @foreach(['pedoman' => ['fas fa-book', 'Pedoman'], 'rapp' => ['fas fa-file-invoice', 'RAPP']] as $t => $info)
    <li class="nav-item"><a href="{{ route('pedoman.index', ['tab'=>$t]) }}" class="nav-link {{ $tab===$t ? 'active' : '' }}" style="{{ $tab===$t ? 'background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none' : 'color:var(--text)' }}"><i class="{{ $info[0] }} me-1"></i>{{ $info[1] }}</a></li>
    @endforeach
</ul>

@if($tab === 'pedoman')
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-book me-2"></i>Pedoman Umum</h6>
        @if(auth()->user()->canEdit('pedoman'))
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPedoman"><i class="fas fa-plus me-1"></i>Tambah</button>
        @endif
    </div>
    <div class="content-card__body">
        <div class="row g-3">
            @forelse($pedoman as $p)
            <div class="col-md-4">
                <div class="content-card h-100">
                    <div class="content-card__body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--primary-light);flex-shrink:0"><i class="fas fa-file-alt text-primary fa-lg"></i></div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">{{ $p->judul }}</h6>
                                @if($p->kategori)<span class="badge bg-info">{{ $p->kategori }}</span>@endif
                                @if($p->tanggal_sidang ?? false)<span class="text-muted small"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($p->tanggal_sidang)->format('d/m/Y') }}</span>@endif
                                @if($p->deskripsi)<p class="text-muted small mt-1 mb-0">{{ Str::limit($p->deskripsi, 80) }}</p>@endif
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            @if($p->file_path)
                            <a href="{{ asset('storage/' . $p->file_path) }}" class="btn btn-sm btn-outline-primary" download><i class="fas fa-download me-1"></i>{{ $p->file_name ?? 'Download' }}</a>
                            @else <span></span> @endif
                            @if(auth()->user()->canEdit('pedoman'))
                            <form action="{{ route('pedoman.destroy', $p->id_pedoman) }}" method="POST" onsubmit="return confirm('Hapus pedoman ini?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">Belum ada pedoman</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Pedoman --}}
<div class="modal fade" id="modalPedoman" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Pedoman</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('pedoman.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Judul</label><input type="text" name="judul" class="form-control" required></div>
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select"><option>Keuangan</option><option>Organisasi</option><option>Lainnya</option></select>
                        </div>
                        <div class="col-3"><label class="form-label fw-semibold">Tahun</label><input type="number" name="tahun" class="form-control" value="{{ date('Y') }}"></div>
                        <div class="col-3"><label class="form-label fw-semibold">Versi</label><input type="text" name="versi" class="form-control" value="1.0"></div>
                    </div>
                    <div class="mt-3"><label class="form-label fw-semibold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
                    <div class="mt-3"><label class="form-label fw-semibold">File</label><input type="file" name="file" class="form-control"></div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

@else {{-- RAPP Tab --}}

{{-- Filter by UP --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="rapp">
            <div class="col-auto">
                <label class="form-label small fw-semibold">Unit Pelayanan</label>
                <select name="up" class="form-select" style="width:200px">
                    <option value="">Semua UP</option>
                    @foreach($unitList as $u)
                        <option value="{{ $u->nomor_up }}" @selected($filterUp==$u->nomor_up)>{{ $u->inisial }} — {{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
            <div class="col-auto ms-auto">
                @if(auth()->user()->canEdit('pedoman'))
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRapp"><i class="fas fa-plus me-1"></i>Buat RAPP</button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Merge Button --}}
@php
    $tahunList = $rapp->where('is_gabungan', false)->pluck('tahun')->unique()->sort();
@endphp
@foreach($tahunList as $th)
@php
    $rappTahun = $rapp->where('tahun', $th)->where('is_gabungan', false);
    $allApproved = $rappTahun->every(fn($r) => $r->status === 'disetujui');
    $gabunganExists = $rapp->where('tahun', $th)->where('is_gabungan', true)->first();
@endphp
@if($rappTahun->count() > 0 && $allApproved && !$gabunganExists)
<div class="alert alert-success d-flex justify-content-between align-items-center mb-3">
    <span><i class="fas fa-check-circle me-2"></i>Semua RAPP tahun {{ $th }} sudah disetujui ({{ $rappTahun->count() }} UP). Siap digabungkan!</span>
    <form action="{{ route('pedoman.rapp.merge') }}" method="POST">@csrf
        <input type="hidden" name="tahun" value="{{ $th }}">
        <button class="btn btn-sm btn-success"><i class="fas fa-layer-group me-1"></i>Gabungkan RAPP {{ $th }}</button>
    </form>
</div>
@endif
@endforeach

{{-- RAPP Cards --}}
<div class="row g-3">
    @forelse($rapp as $r)
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $r->judul }}</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($r->is_gabungan)
                                <span class="badge bg-primary"><i class="fas fa-layer-group me-1"></i>Gabungan</span>
                            @elseif($r->up_inisial)
                                <span class="badge bg-info">{{ $r->up_inisial }}</span>
                            @endif
                            <span class="badge bg-secondary">{{ $r->tahun }}</span>
                            <span class="badge bg-{{ $r->status==='disetujui' ? 'success' : ($r->status==='ditolak' ? 'danger' : ($r->status==='diajukan' ? 'info' : 'warning')) }}">{{ ucfirst($r->status) }}</span>
                        </div>
                    </div>
                </div>
                @if($r->catatan)<p class="text-muted small mb-2">{{ Str::limit($r->catatan, 60) }}</p>@endif
                <div class="d-flex gap-2">
                    <a href="{{ route('pedoman.rapp.show', $r->id) }}" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-eye me-1"></i>Lihat Detail</a>
                    @if(auth()->user()->canEdit('pedoman'))
                    <form action="{{ route('pedoman.rapp.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus RAPP ini?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-4"><i class="fas fa-file-invoice fa-3x mb-3 d-block opacity-25"></i>Belum ada RAPP. Klik "Buat RAPP" untuk memulai.</div>
    @endforelse
</div>

{{-- Modal Create RAPP --}}
<div class="modal fade" id="modalRapp" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Buat RAPP per Unit Pelayanan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('pedoman.rapp.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit Pelayanan <span class="text-danger">*</span></label>
                        <select name="unit_pelayanan_id" class="form-select" required>
                            <option value="">Pilih UP...</option>
                            @foreach($unitList as $u)
                                <option value="{{ $u->nomor_up }}">{{ $u->inisial }} — {{ $u->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-8"><label class="form-label fw-semibold">Judul</label><input type="text" name="judul" class="form-control" required placeholder="RAPP SPA 2027"></div>
                        <div class="col-4"><label class="form-label fw-semibold">Tahun</label><input type="number" name="tahun" class="form-control" value="{{ date('Y')+1 }}" required></div>
                    </div>
                    <div class="mt-3"><label class="form-label fw-semibold">Catatan</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Buat RAPP</button></div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
