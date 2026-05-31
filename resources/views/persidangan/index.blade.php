@extends('layouts.app')
@section('title', 'Bahan Persidangan')

@section('content')
<div class="content-card mb-4">
    <div class="content-card__body d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-gavel me-2"></i>Dokumen Persidangan</h6>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus me-1"></i>Upload Dokumen</button>
    </div>
</div>

<div class="row g-3">
    @forelse($dokumen as $d)
    <div class="col-md-6 col-lg-4">
        <div class="content-card h-100">
            <div class="content-card__body">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0">
                        <div style="width:45px;height:45px;border-radius:10px;background:linear-gradient(135deg,#3498db,#2980b9);display:flex;align-items:center;justify-content:center;color:#fff">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $d->judul }}</h6>
                        @if($d->kategori)<span class="badge bg-info me-1">{{ $d->kategori }}</span>@endif
                        @if($d->tanggal_sidang)<span class="text-muted small"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($d->tanggal_sidang)->format('d/m/Y') }}</span>@endif
                        @if($d->deskripsi)<p class="text-muted small mt-1 mb-0">{{ Str::limit($d->deskripsi, 100) }}</p>@endif
                    </div>
                </div>
            </div>
            <div class="content-card__body border-top py-2 d-flex justify-content-between">
                @if($d->file_path)
                <a href="{{ asset('storage/'.$d->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-download me-1"></i>{{ $d->file_name }}</a>
                @else
                <span class="text-muted small">Tidak ada file</span>
                @endif
                <form action="{{ route('persidangan.destroy', $d->id_dokumen) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="content-card"><div class="content-card__body text-center py-5 text-muted"><i class="fas fa-gavel fa-3x mb-3 d-block"></i><h5>Belum ada dokumen</h5></div></div>
    </div>
    @endforelse
</div>

@if($dokumen->hasPages())<div class="mt-3">{{ $dokumen->links() }}</div>@endif

{{-- Modal --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Upload Dokumen Persidangan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('persidangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Judul *</label><input type="text" name="judul" class="form-control" required></div>
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label fw-semibold">Kategori</label><input type="text" name="kategori" class="form-control" placeholder="Contoh: Sidang MJ"></div>
                        <div class="col-6"><label class="form-label fw-semibold">Tanggal Sidang</label><input type="date" name="tanggal_sidang" class="form-control"></div>
                    </div>
                    <div class="mt-3"><label class="form-label fw-semibold">File</label><input type="file" name="file" class="form-control"></div>
                    <div class="mt-3"><label class="form-label fw-semibold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
