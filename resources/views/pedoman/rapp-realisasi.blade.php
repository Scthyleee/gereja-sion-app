@extends('layouts.app')
@section('title', 'Realisasi RAPP — ' . $rapp->judul)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('pedoman.rapp.show', $rapp->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali ke Detail RAPP</a>
    <div class="d-flex gap-2 align-items-center">
        @if($rapp->up_inisial)
        <span class="badge bg-info fs-6">{{ $rapp->up_inisial }}</span>
        @endif
        <span class="badge bg-secondary fs-6">{{ $rapp->tahun }}</span>
    </div>
</div>

{{-- Info --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row">
            <div class="col-md-4"><small class="text-muted d-block">Judul</small><strong>{{ $rapp->judul }}</strong></div>
            <div class="col-md-2"><small class="text-muted d-block">UP</small><span class="badge bg-info">{{ $rapp->up_inisial ?? 'Gabungan' }}</span></div>
            <div class="col-md-2"><small class="text-muted d-block">Tahun</small><strong>{{ $rapp->tahun }}</strong></div>
            <div class="col-md-4"><small class="text-muted d-block">Status</small><span class="badge bg-{{ $rapp->status === 'disetujui' ? 'success' : 'warning' }}">{{ ucfirst($rapp->status) }}</span></div>
        </div>
    </div>
</div>

{{-- Overall Summary --}}
@php
    $totalAnggaran = $realisasiData->sum('anggaran');
    $totalRealisasi = $realisasiData->sum('realisasi');
    $totalSelisih = $totalAnggaran - $totalRealisasi;
    $overallPersen = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0;
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Anggaran</div>
                <div class="stat-card__value">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Realisasi</div>
                <div class="stat-card__value">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--{{ $totalSelisih >= 0 ? 'green' : 'red' }}">
            <div class="stat-card__icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Selisih</div>
                <div class="stat-card__value">Rp {{ number_format($totalSelisih, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-percentage"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">% Realisasi</div>
                <div class="stat-card__value">{{ $overallPersen }}%</div>
            </div>
        </div>
    </div>
</div>

{{-- Detail Table --}}
<div class="content-card">
    <div class="content-card__header">
        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Perbandingan Anggaran vs Realisasi</h6>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:130px">Kode Akun</th>
                        <th>Nama</th>
                        <th class="text-end" style="width:140px">Anggaran</th>
                        <th class="text-end" style="width:140px">Realisasi</th>
                        <th class="text-end" style="width:140px">Selisih</th>
                        <th style="width:200px">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($realisasiData->groupBy('tipe') as $tipe => $items)
                    <tr style="background:rgba(230,126,34,.1)">
                        <td colspan="6" class="fw-bold text-uppercase">{{ $tipe === 'penerimaan' ? 'I. PENERIMAAN' : 'II. PENGELUARAN' }}</td>
                    </tr>
                    @foreach($items as $d)
                    @php
                        $persen = $d->persentase;
                        $barColor = $persen <= 50 ? '#e74c3c' : ($persen <= 80 ? '#f39c12' : ($persen <= 100 ? '#2ecc71' : '#e74c3c'));
                    @endphp
                    <tr>
                        <td><code class="text-primary small">{{ $d->kode }}</code></td>
                        <td class="small">{{ $d->nama_akun }}</td>
                        <td class="text-end small">Rp {{ number_format($d->anggaran, 0, ',', '.') }}</td>
                        <td class="text-end small fw-semibold">Rp {{ number_format($d->realisasi, 0, ',', '.') }}</td>
                        <td class="text-end small {{ $d->selisih >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $d->selisih >= 0 ? '' : '-' }}Rp {{ number_format(abs($d->selisih), 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px;background:var(--border)">
                                    <div class="progress-bar" style="width:{{ min($persen, 100) }}%;background:{{ $barColor }}"></div>
                                </div>
                                <small class="fw-semibold" style="width:45px;text-align:right">{{ $d->anggaran > 0 ? $persen.'%' : '-' }}</small>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @php
                        $subAnggaran = $items->sum('anggaran');
                        $subRealisasi = $items->sum('realisasi');
                    @endphp
                    <tr class="fw-bold">
                        <td colspan="2">Subtotal {{ ucfirst($tipe) }}</td>
                        <td class="text-end">Rp {{ number_format($subAnggaran, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($subRealisasi, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($subAnggaran - $subRealisasi, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
