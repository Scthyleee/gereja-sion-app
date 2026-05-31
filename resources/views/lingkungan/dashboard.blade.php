@extends('layouts.app')
@section('title', 'Dashboard Lingkungan — ' . $lingkungan->nama)

@section('content')
<div class="mb-4">
    <h4 class="fw-bold"><i class="fas fa-map-marker-alt me-2" style="color:var(--primary)"></i>{{ $lingkungan->nama }}</h4>
    <p class="text-muted mb-0">Dashboard Lingkungan Sion — {{ auth()->user()->role_name }}</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
            <div class="rounded p-2" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white"><i class="fas fa-home fa-lg"></i></div>
            <div><div class="small text-muted">Total KK</div><div class="fs-4 fw-bold">{{ $totalKK }}</div></div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
            <div class="rounded p-2" style="background:linear-gradient(135deg,#4caf50,#388e3c);color:white"><i class="fas fa-users fa-lg"></i></div>
            <div><div class="small text-muted">Total Jiwa</div><div class="fs-4 fw-bold">{{ $totalJiwa }}</div></div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
            <div class="rounded p-2" style="background:linear-gradient(135deg,#2196f3,#1565c0);color:white"><i class="fas fa-water fa-lg"></i></div>
            <div><div class="small text-muted">Sudah Baptis</div><div class="fs-4 fw-bold">{{ $baptis }}</div></div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
            <div class="rounded p-2" style="background:linear-gradient(135deg,#ff9800,#ef6c00);color:white"><i class="fas fa-money-bill fa-lg"></i></div>
            <div><div class="small text-muted">Keuangan {{ $tahun }}</div>
                <div class="small">
                    <span class="text-success">+{{ number_format($laporanKeuangan->total_masuk ?? 0, 0, ',', '.') }}</span>
                    / <span class="text-danger">-{{ number_format($laporanKeuangan->total_keluar ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div></div>
    </div>
</div>

{{-- Pengurus --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Pengurus</h6></div>
            <div class="content-card__body">
                @foreach($pengurus as $p)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-circle" style="width:35px;height:35px;font-size:.8rem">{{ strtoupper(substr($p->nama, 0, 1)) }}</div>
                    <div><div class="fw-semibold small">{{ $p->nama }}</div><span class="badge bg-primary" style="font-size:.6rem">{{ $p->nama_role }}</span></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="content-card h-100">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-home me-2"></i>Daftar Keluarga</h6></div>
            <div class="content-card__body p-0"><div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Kepala Keluarga</th><th>Anggota</th><th>Alamat</th></tr></thead>
                    <tbody>
                        @foreach($lingkungan->keluarga as $kk)
                        <tr>
                            <td class="fw-semibold small">{{ $kk->nama_kepala_keluarga }}</td>
                            <td><span class="badge bg-info">{{ $kk->anggota->count() }}</span></td>
                            <td class="small text-muted">{{ Str::limit($kk->alamat, 30) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>
@endsection
