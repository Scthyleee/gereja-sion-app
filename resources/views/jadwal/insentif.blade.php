@extends('layouts.app')
@section('title', 'Rekap Insentif')

@section('content')
{{-- Tabs --}}
<ul class="nav nav-pills mb-4" style="gap:8px">
    <li class="nav-item"><a href="{{ route('jadwal.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-calendar-alt me-1"></i>Jadwal</a></li>
    <li class="nav-item"><a href="{{ route('insentif.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="nav-link active" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none"><i class="fas fa-money-bill me-1"></i>Insentif</a></li>
    <li class="nav-item"><a href="{{ route('petugas.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-users-cog me-1"></i>Petugas</a></li>
    <li class="nav-item"><a href="{{ route('insentif.setting') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-cog me-1"></i>Setting Insentif</a></li>
</ul>

{{-- Filter --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
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
</div>

{{-- Summary --}}
@php
    $grandTotal = $data->sum('total_insentif');
    $totalTugas = $data->sum('jumlah_tugas');
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card--orange">
            <div class="stat-card__icon"><i class="fas fa-money-bill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Insentif</div>
                <div class="stat-card__value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-users"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Petugas</div>
                <div class="stat-card__value">{{ $data->count() }} orang</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-tasks"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Penugasan</div>
                <div class="stat-card__value">{{ $totalTugas }} tugas</div>
            </div>
        </div>
    </div>
</div>

{{-- Rekap Table --}}
<div class="content-card">
    <div class="content-card__header">
        <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Rekap Insentif — {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</h6>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Petugas</th><th>Jenis Tugas</th><th class="text-center">Jumlah Tugas</th><th class="text-end">Total Insentif</th></tr></thead>
                <tbody>
                    @forelse($data as $d)
                    <tr>
                        <td>{{ $d['nama'] }}</td>
                        <td>{{ $d['jadwal']->map(fn($j) => $j['tugas'])->unique()->join(', ') }}</td>
                        <td class="text-center"><span class="badge bg-primary">{{ $d['jumlah_tugas'] }}x</span></td>
                        <td class="text-end fw-bold">Rp {{ number_format($d['total_insentif'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada data insentif</td></tr>
                    @endforelse
                </tbody>
                @if($data->count())
                <tfoot>
                    <tr class="fw-bold" style="background:rgba(230,126,34,.1)">
                        <td colspan="2">TOTAL</td>
                        <td class="text-center">{{ $totalTugas }}x</td>
                        <td class="text-end">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
