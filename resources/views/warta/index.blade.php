@extends('layouts.app')
@section('title', 'Warta Jemaat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-newspaper me-2"></i>Warta Jemaat</h5>
        <p class="text-muted mb-0 small">Laporan penerimaan & pengeluaran keuangan gereja per periode</p>
    </div>
</div>

{{-- Filter Tanggal --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal', now()->startOfMonth()->format('Y-m-d')) }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir', now()->endOfWeek()->format('Y-m-d')) }}">
            </div>
            <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
            <div class="col-auto">
                <button type="submit" name="action" value="simpan" class="btn btn-success" onclick="return confirm('Simpan periode ini sebagai Warta baru?')"><i class="fas fa-save me-1"></i>Simpan sebagai Warta</button>
            </div>
        </form>
    </div>
</div>

{{-- Preview Keuangan (jika ada filter) --}}
@if(request('tanggal_awal'))
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Periode: {{ \Carbon\Carbon::parse(request('tanggal_awal'))->format('d M Y') }} — {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d M Y') }}</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-4"><div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div><div class="stat-card__body"><div class="stat-card__label">Penerimaan</div><div class="stat-card__value">Rp {{ number_format($previewPenerimaan ?? 0, 0, ',', '.') }}</div></div></div></div>
            <div class="col-md-4"><div class="stat-card stat-card--red"><div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div><div class="stat-card__body"><div class="stat-card__label">Pengeluaran</div><div class="stat-card__value">Rp {{ number_format($previewPengeluaran ?? 0, 0, ',', '.') }}</div></div></div></div>
            <div class="col-md-4"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-balance-scale"></i></div><div class="stat-card__body"><div class="stat-card__label">Selisih</div><div class="stat-card__value">Rp {{ number_format(($previewPenerimaan ?? 0) - ($previewPengeluaran ?? 0), 0, ',', '.') }}</div></div></div></div>
        </div>
    </div>
</div>
@endif

{{-- Daftar Warta Tersimpan --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Warta Tersimpan</h6></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Periode</th><th class="text-end">Saldo Awal</th><th class="text-end">Penerimaan</th><th class="text-end">Pengeluaran</th><th class="text-end">Saldo Akhir</th><th style="width:200px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($wartas as $w)
                    <tr>
                        <td class="small text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $w->tanggal_awal->format('d M') }} — {{ $w->tanggal_akhir->format('d M Y') }}</strong>
                            @if($w->minggu_ke)<br><span class="badge bg-secondary small">Minggu ke-{{ $w->minggu_ke }}</span>@endif
                        </td>
                        <td class="text-end">Rp {{ number_format($w->saldo_awal_minggu, 0, ',', '.') }}</td>
                        <td class="text-end text-success">+ Rp {{ number_format($w->total_penerimaan, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">- Rp {{ number_format($w->total_pengeluaran, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($w->saldo_akhir_minggu, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('warta.show', $w->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye me-1"></i>Lihat</a>
                                <a href="{{ route('warta.print', $w->id) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-print"></i></a>
                                <form action="{{ route('warta.refresh', $w->id) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-success" title="Refresh data"><i class="fas fa-sync-alt"></i></button></form>
                                <form action="{{ route('warta.destroy', $w->id) }}" method="POST" onsubmit="return confirm('Hapus warta ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada warta tersimpan. Pilih tanggal lalu klik "Simpan sebagai Warta".</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
