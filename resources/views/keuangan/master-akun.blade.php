@extends('layouts.app')
@section('title', 'Master Akun ZA6')

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-book"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Kode Akun</div>
                <div class="stat-card__value">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Penerimaan</div>
                <div class="stat-card__value">{{ $stats['penerimaan'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--red">
            <div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Pengeluaran</div>
                <div class="stat-card__value">{{ $stats['pengeluaran'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tipe</label>
                <select name="tipe" class="form-select">
                    <option value="semua" @selected($tipe==='semua')>Semua</option>
                    <option value="penerimaan" @selected($tipe==='penerimaan')>Penerimaan</option>
                    <option value="pengeluaran" @selected($tipe==='pengeluaran')>Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Unit Pelayanan</label>
                <select name="up" class="form-select">
                    <option value="">Semua UP</option>
                    @foreach($units as $u)
                        <option value="{{ $u->nomor_up }}" @selected($up==$u->nomor_up)>
                            {{ sprintf('%02d', $u->nomor_up) }} — {{ $u->inisial }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Cari</label>
                <input type="text" name="q" class="form-control" placeholder="Kode, nama, atau sumber..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Kode Akun</h6>
        <span class="badge bg-secondary">{{ $akun->total() }} akun</span>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:180px">Kode</th>
                        <th>Nama</th>
                        <th style="width:100px">Tipe</th>
                        <th style="width:100px">UP</th>
                        <th style="width:100px">Kategori</th>
                        <th>Sumber Lapangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($akun as $a)
                    <tr class="{{ $a->parent_kode ? 'table-row-child' : '' }}">
                        <td>
                            @if($a->parent_kode)
                                <code class="text-info ms-3">↳ {{ $a->kode }}</code>
                            @else
                                <code class="text-primary fw-bold">{{ $a->kode }}</code>
                            @endif
                        </td>
                        <td class="{{ $a->parent_kode ? 'ps-4 small' : '' }}">{{ $a->nama }}</td>
                        <td>
                            <span class="badge bg-{{ $a->tipe === 'penerimaan' ? 'success' : 'danger' }}">
                                {{ ucfirst($a->tipe) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $a->unitPelayanan->inisial ?? '-' }}</span>
                        </td>
                        <td>
                            @if($a->kategori_akun === 'kategorial')
                                <span class="badge bg-warning text-dark">Kategorial</span>
                            @else
                                <span class="badge bg-info">MPH</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ Str::limit($a->sumber_lapangan, 60) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($akun->hasPages())
    <div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <small class="text-muted">Showing {{ $akun->firstItem() }} to {{ $akun->lastItem() }} of {{ $akun->total() }} results</small>
        {{ $akun->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
