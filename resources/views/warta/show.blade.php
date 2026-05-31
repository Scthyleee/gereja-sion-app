@extends('layouts.app')
@section('title', 'Warta ' . $warta->tanggal_awal->format('d M') . ' — ' . $warta->tanggal_akhir->format('d M Y'))

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2">
        <a href="{{ route('warta.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
        <span class="badge bg-primary fs-6 d-flex align-items-center">{{ $warta->tanggal_awal->format('d M') }} — {{ $warta->tanggal_akhir->format('d M Y') }}</span>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('warta.refresh', $warta->id) }}" method="POST">@csrf<button class="btn btn-sm btn-success"><i class="fas fa-sync-alt me-1"></i>Refresh Data</button></form>
        <a href="{{ route('warta.print', $warta->id) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i>Cetak PDF</a>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-wallet"></i></div><div class="stat-card__body"><div class="stat-card__label">Saldo Awal</div><div class="stat-card__value">Rp {{ number_format($warta->saldo_awal_minggu, 0, ',', '.') }}</div></div></div></div>
    <div class="col-md-3"><div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div><div class="stat-card__body"><div class="stat-card__label">Penerimaan</div><div class="stat-card__value">Rp {{ number_format($warta->total_penerimaan, 0, ',', '.') }}</div></div></div></div>
    <div class="col-md-3"><div class="stat-card stat-card--red"><div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div><div class="stat-card__body"><div class="stat-card__label">Pengeluaran</div><div class="stat-card__value">Rp {{ number_format($warta->total_pengeluaran, 0, ',', '.') }}</div></div></div></div>
    <div class="col-md-3"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-balance-scale"></i></div><div class="stat-card__body"><div class="stat-card__label">Saldo Akhir</div><div class="stat-card__value">Rp {{ number_format($warta->saldo_akhir_minggu, 0, ',', '.') }}</div></div></div></div>
</div>

{{-- Penerimaan --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-arrow-down text-success me-2"></i>A. PENERIMAAN ({{ $penerimaan->count() }} transaksi)</h6></div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:100px">Tanggal</th><th style="width:130px">Kode Akun</th><th>Keterangan</th><th class="text-end" style="width:140px">Total (Rp)</th></tr></thead>
            <tbody>
                @forelse($penerimaan as $p)
                <tr>
                    <td class="small text-muted">{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td><code class="text-primary small">{{ $p->kodeAkun->kode ?? '-' }}</code></td>
                    <td>{{ $p->keterangan ?: ($p->kodeAkun->keterangan ?? $p->kodeAkun->nama ?? '-') }}</td>
                    <td class="text-end fw-semibold text-success">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada penerimaan</td></tr>
                @endforelse
            </tbody>
            <tfoot><tr class="fw-bold" style="background:rgba(46,204,113,.1)"><td colspan="3" class="text-end">TOTAL PENERIMAAN</td><td class="text-end">Rp {{ number_format($warta->total_penerimaan, 0, ',', '.') }}</td></tr></tfoot>
        </table>
    </div></div>
</div>

{{-- Pengeluaran --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-arrow-up text-danger me-2"></i>B. PENGELUARAN ({{ $pengeluaran->count() }} transaksi)</h6></div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:100px">Tanggal</th><th style="width:130px">Kode Akun</th><th>Keterangan</th><th class="text-end" style="width:140px">Total (Rp)</th></tr></thead>
            <tbody>
                @forelse($pengeluaran as $p)
                <tr>
                    <td class="small text-muted">{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td><code class="text-primary small">{{ $p->kodeAkun->kode ?? '-' }}</code></td>
                    <td>{{ $p->keterangan ?: ($p->kodeAkun->keterangan ?? $p->kodeAkun->nama ?? '-') }}</td>
                    <td class="text-end fw-semibold text-danger">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada pengeluaran</td></tr>
                @endforelse
            </tbody>
            <tfoot><tr class="fw-bold" style="background:rgba(231,76,60,.1)"><td colspan="3" class="text-end">TOTAL PENGELUARAN</td><td class="text-end">Rp {{ number_format($warta->total_pengeluaran, 0, ',', '.') }}</td></tr></tfoot>
        </table>
    </div></div>
</div>

{{-- Insentif per Jadwal --}}
@if(isset($insentifPerJadwal) && $insentifPerJadwal->count())
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-hand-holding-usd text-warning me-2"></i>B2. RINCIAN INSENTIF PER IBADAH</h6></div>
    <div class="content-card__body">
        @foreach($insentifPerJadwal as $jadwalId => $petugasList)
        @php
            $first = $petugasList->first();
            $tgl = \Carbon\Carbon::parse($first->tanggal);
            $subtotal = $petugasList->sum('nominal_insentif');
        @endphp
        <div class="mb-3 p-2 rounded" style="border:1px solid var(--border);background:rgba(231,76,60,.05)">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong>Insentif {{ $first->jenis_ibadah }} {{ $first->waktu ? '('.$first->waktu.')' : '' }} — {{ $tgl->translatedFormat('d M Y') }}</strong>
                <span class="fw-bold text-danger">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @foreach($petugasList as $pt)
            <div class="small text-muted ms-3">• {{ $pt->jenis_tugas }}: <strong>{{ $pt->nama_petugas }}</strong> ({{ $pt->asal }}) — Rp {{ number_format($pt->nominal_insentif, 0, ',', '.') }}</div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Amplop PS --}}
@if(count($amplopPS) > 0)
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-envelope-open text-warning me-2"></i>C. AMPLOP PERSEMBAHAN SYUKUR</h6></div>
    <div class="content-card__body">
        @foreach($amplopPS as $jemaatId => $items)
        @php $familyTotal = $items->sum('total'); @endphp
        <div class="mb-3 p-2 rounded" style="border:1px solid var(--border);background:rgba(201,168,76,.05)">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong>{{ $loop->iteration }}. {{ $items->first()->nama_lengkap ?? 'N/A' }}</strong>
                <span class="fw-bold text-primary">Rp {{ number_format($familyTotal, 0, ',', '.') }}</span>
            </div>
            @foreach($items as $item)
            <div class="small text-muted ms-3">• {{ $item->jenis_ps }}: <strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong></div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
