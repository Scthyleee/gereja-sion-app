@extends('layouts.app')
@section('title', 'Pendaftaran Keluarga')

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--primary),#c9a84c)"><i class="fas fa-home fa-lg text-white"></i></div>
            <div><div class="small text-muted">Total Keluarga (KK)</div><div class="fs-4 fw-bold">{{ $stats['total_kk'] }}</div></div>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:linear-gradient(135deg,#6b8f71,#8bb896)"><i class="fas fa-users fa-lg text-white"></i></div>
            <div><div class="small text-muted">Total Jiwa Aktif</div><div class="fs-4 fw-bold">{{ $stats['total_jiwa'] }}</div></div>
        </div></div>
    </div>
</div>

{{-- Filter --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-auto"><label class="form-label small">Lingkungan</label>
                        <select name="sion" class="form-select form-select-sm" style="width:150px">
                            <option value="">Semua</option>
                            @foreach($sionList as $s)<option value="{{ $s->id }}" @selected($sionId==$s->id)>{{ $s->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-auto"><label class="form-label small">Cari</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Nama / No KK" value="{{ $search }}" style="width:200px">
                    </div>
                    <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="fas fa-search me-1"></i>Cari</button></div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('keluarga.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Daftarkan Keluarga</a>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-home me-2"></i>Daftar Keluarga</h6></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>No KK</th><th>Kepala Keluarga</th><th>Lingkungan</th><th>Anggota</th><th>Alamat</th><th style="width:100px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($keluarga as $k)
                    <tr>
                        <td class="small fw-semibold">{{ $k->no_kk ?: '-' }}</td>
                        <td><a href="{{ route('keluarga.show', $k) }}" class="fw-semibold text-decoration-none">{{ $k->nama_kepala_keluarga }}</a></td>
                        <td><span class="badge bg-info">{{ $k->lingkunganSion?->nama ?? '-' }}</span></td>
                        <td>{{ $k->anggota->count() }} jiwa</td>
                        <td class="small">{{ Str::limit($k->alamat, 40) }}</td>
                        <td>
                            <a href="{{ route('keluarga.show', $k) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="fas fa-eye"></i></a>
                            <form action="{{ route('keluarga.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus keluarga ini?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-home fa-2x mb-2 d-block"></i>Belum ada data keluarga</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($keluarga->hasPages())<div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3"><small class="text-muted">Showing {{ $keluarga->firstItem() }} to {{ $keluarga->lastItem() }} of {{ $keluarga->total() }} results</small>{{ $keluarga->links('pagination::bootstrap-5') }}</div>@endif
</div>
@endsection
