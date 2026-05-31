@extends('layouts.app')
@section('title', 'Saldo')

@section('content')
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold">Tahun</label>
                <input type="number" name="tahun" class="form-control" style="width:120px" value="{{ $tahun }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-wallet me-2"></i>Saldo per Kantong Dana — {{ $tahun }}</h6>
        @if(auth()->user()->canEdit('keuangan'))
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditSaldo"><i class="fas fa-edit me-1"></i>Edit Saldo Awal</button>
        @endif
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kantong Dana</th>
                        <th class="text-end">Saldo Awal</th>
                        <th class="text-end">Penerimaan</th>
                        <th class="text-end">Pengeluaran</th>
                        <th class="text-end">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalAwal = 0; $totalMasuk = 0; $totalKeluar = 0; $totalAkhir = 0; @endphp
                    @foreach($saldoData as $s)
                    @php $totalAwal += $s['awal']; $totalMasuk += $s['masuk']; $totalKeluar += $s['keluar']; $totalAkhir += $s['akhir']; @endphp
                    <tr>
                        <td class="fw-semibold">{{ $s['label'] }}</td>
                        <td class="text-end">Rp {{ number_format($s['awal'], 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($s['masuk'], 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($s['keluar'], 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($s['akhir'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold" style="background:rgba(230,126,34,.1)">
                        <td>TOTAL</td>
                        <td class="text-end">Rp {{ number_format($totalAwal, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit Saldo Awal --}}
@if(auth()->user()->canEdit('keuangan'))
<div class="modal fade" id="modalEditSaldo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Saldo Awal — {{ $tahun }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('keuangan.saldo.update') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-body">
                    @foreach($saldoData as $s)
                    <div class="row mb-2 align-items-center">
                        <div class="col-5"><label class="form-label mb-0 fw-semibold small">{{ $s['label'] }}</label></div>
                        <div class="col-7">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="saldo[{{ $s['kantong'] }}]" class="form-control" value="{{ (int)$s['awal'] }}" min="0">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer" style="border-color:var(--border)">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
