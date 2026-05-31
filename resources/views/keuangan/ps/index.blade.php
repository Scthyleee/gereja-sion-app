@extends('layouts.app')
@section('title', 'Persembahan Syukur (PS)')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-hand-holding-heart"></i></div><div class="stat-card__body"><div class="stat-card__label">Total PS {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</div><div class="stat-card__value">Rp {{ number_format($totalBulan, 0, ',', '.') }}</div></div></div></div>
    <div class="col-md-4"><div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-receipt"></i></div><div class="stat-card__body"><div class="stat-card__label">Jumlah Transaksi</div><div class="stat-card__value">{{ $transaksi->total() }}</div></div></div></div>
    <div class="col-md-4"><div class="stat-card stat-card--orange"><div class="stat-card__icon"><i class="fas fa-tags"></i></div><div class="stat-card__body"><div class="stat-card__label">Jenis PS Tercatat</div><div class="stat-card__value">{{ $summary->count() }}</div></div></div></div>
</div>

{{-- Filters --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small fw-semibold">Bulan</label><input type="month" name="bulan" class="form-control" value="{{ $bulan }}"></div>
            <div class="col-md-3"><label class="form-label small fw-semibold">Jenis PS</label>
                <select name="jenis_ps" class="form-select"><option value="semua">Semua</option>@foreach($jenisConfig as $j)<option value="{{ $j->nama }}" @selected($jenisPs==$j->nama)>{{ $j->nama }}</option>@endforeach</select>
            </div>
            <div class="col-md-2"><label class="form-label small fw-semibold">Sumber</label>
                <select name="sumber" class="form-select"><option value="semua">Semua</option><option value="kantor" @selected($sumber=='kantor')>Kantor</option><option value="jadwal" @selected($sumber=='jadwal')>Jadwal/Ibadah</option></select>
            </div>
            <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
            <div class="col-auto"><a href="{{ route('ps.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i>Input PS Baru</a></div>
            <div class="col-auto"><a href="{{ route('print.rekap-ps', ['bulan' => $bulan]) }}" class="btn btn-outline-secondary" target="_blank"><i class="fas fa-file-pdf me-1"></i>Export PDF</a></div>
        </form>
    </div>
</div>

{{-- Summary per Jenis --}}
@if($summary->count() > 0)
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Rekap {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h6></div>
    <div class="content-card__body">
        <div class="row g-2">
            @foreach($summary as $s)
            <div class="col-md-4"><div class="d-flex justify-content-between p-2 rounded" style="background:var(--bg-body)"><span class="small fw-semibold">{{ $s->jenis_ps }}</span><span class="fw-bold text-success">Rp {{ number_format($s->total, 0, ',', '.') }}</span></div></div>
            @endforeach
        </div>
        <div class="mt-3 text-end"><span class="fw-bold fs-5">Total: Rp {{ number_format($totalBulan, 0, ',', '.') }}</span></div>
    </div>
</div>
@endif

{{-- Table --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Transaksi PS</h6><span class="badge bg-secondary">{{ $transaksi->total() }}</span></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Tanggal</th><th>Nama Jemaat</th><th>Jenis PS</th><th>Kode Akun</th><th>No. Kuitansi</th><th>Sumber</th><th class="text-end">Jumlah</th><th>Status</th><th style="width:80px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($transaksi as $t)
                    <tr>
                        <td class="small">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->jemaat?->nama_lengkap ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ $t->jenis_ps }}</span></td>
                        <td class="small text-muted">{{ $t->kodeAkun?->kode ?? '-' }}</td>
                        <td class="small">{{ $t->no_kuitansi }}</td>
                        <td><span class="badge bg-{{ $t->sumber === 'jadwal' ? 'info' : 'secondary' }}">{{ ucfirst($t->sumber) }}</span></td>
                        <td class="text-end fw-semibold">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $t->status === 'verified' ? 'success' : 'warning' }}">{{ ucfirst($t->status) }}</span></td>
                        <td>
                            @if($t->status === 'draft')
                            <form action="{{ route('ps.verify', $t->id) }}" method="POST" class="d-inline">@csrf @method('PUT')
                                <button class="btn btn-sm btn-outline-success" title="Verifikasi"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('ps.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada data PS bulan ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transaksi->hasPages())<div class="content-card__body border-top">{{ $transaksi->links() }}</div>@endif
</div>
@endsection
