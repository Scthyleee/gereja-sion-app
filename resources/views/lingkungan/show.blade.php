@extends('layouts.app')
@section('title', 'Detail Lingkungan — ' . $lingkungan->nama)

@section('content')
<div class="mb-3"><a href="{{ route('lingkungan.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a></div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body text-center py-3">
            <i class="fas fa-home fa-2x mb-2" style="color:var(--primary)"></i>
            <div class="fs-3 fw-bold">{{ $totalKK }}</div><div class="small text-muted">Kartu Keluarga</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body text-center py-3">
            <i class="fas fa-users fa-2x mb-2" style="color:var(--primary)"></i>
            <div class="fs-3 fw-bold">{{ $totalJiwa }}</div><div class="small text-muted">Total Jiwa</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body text-center py-3">
            <i class="fas fa-water fa-2x mb-2" style="color:#4caf50"></i>
            <div class="fs-3 fw-bold text-success">{{ $baptis }}</div><div class="small text-muted">Sudah Baptis</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body text-center py-3">
            <i class="fas fa-exclamation-circle fa-2x mb-2" style="color:#ff9800"></i>
            <div class="fs-3 fw-bold text-warning">{{ $belumBaptis }}</div><div class="small text-muted">Belum Baptis</div>
        </div></div>
    </div>
</div>

{{-- Pengurus Lingkungan --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Pengurus Lingkungan</h6></div>
    <div class="content-card__body">
        @forelse($pengurus as $p)
        <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded" style="background:var(--primary-glow)">
            <div class="avatar-circle">{{ strtoupper(substr($p->nama, 0, 1)) }}</div>
            <div>
                <div class="fw-semibold">{{ $p->nama }}</div>
                <span class="badge bg-primary">{{ $p->nama_role }}</span>
            </div>
        </div>
        @empty
        <div class="text-muted text-center py-3">Belum ada pengurus terdaftar</div>
        @endforelse
    </div>
</div>

{{-- Daftar Keluarga --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-home me-2"></i>Daftar Keluarga ({{ $totalKK }})</h6></div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>No KK</th><th>Kepala Keluarga</th><th>Anggota</th><th>Alamat</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($lingkungan->keluarga as $i => $kk)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td class="small">{{ $kk->no_kk ?? '-' }}</td>
                    <td class="fw-semibold">{{ $kk->nama_kepala_keluarga }}</td>
                    <td><span class="badge bg-info">{{ $kk->anggota->count() }} jiwa</span></td>
                    <td class="small">{{ $kk->alamat ?? '-' }}</td>
                    <td><a href="{{ route('keluarga.show', $kk) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div></div>
</div>
@endsection
