@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div class="row g-4">
    {{-- ZA-14 --}}
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__body text-center">
                <div style="width:60px;height:60px;border-radius:15px;background:linear-gradient(135deg,#2ecc71,#27ae60);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:12px">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <h5 class="fw-bold">Laporan ZA-14</h5>
                <p class="text-muted small">Laporan Penerimaan Bulanan per kode akun</p>
                <form action="{{ route('print.za14') }}" method="GET" target="_blank" class="mt-3">
                    <div class="row g-2 justify-content-center mb-2">
                        <div class="col-5"><select name="bulan" class="form-select form-select-sm">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m==date('n'))>{{ DateTime::createFromFormat('!m',$m)->format('M') }}</option>@endfor</select></div>
                        <div class="col-4"><input type="number" name="tahun" class="form-control form-control-sm" value="{{ date('Y') }}"></div>
                    </div>
                    <button class="btn btn-success btn-sm"><i class="fas fa-file-pdf me-1"></i>Cetak PDF</button>
                </form>
            </div>
        </div>
    </div>
    {{-- ZA-15 --}}
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__body text-center">
                <div style="width:60px;height:60px;border-radius:15px;background:linear-gradient(135deg,#e74c3c,#c0392b);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:12px">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <h5 class="fw-bold">Laporan ZA-15</h5>
                <p class="text-muted small">Laporan Pengeluaran Bulanan per kode akun</p>
                <form action="{{ route('print.za15') }}" method="GET" target="_blank" class="mt-3">
                    <div class="row g-2 justify-content-center mb-2">
                        <div class="col-5"><select name="bulan" class="form-select form-select-sm">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m==date('n'))>{{ DateTime::createFromFormat('!m',$m)->format('M') }}</option>@endfor</select></div>
                        <div class="col-4"><input type="number" name="tahun" class="form-control form-control-sm" value="{{ date('Y') }}"></div>
                    </div>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-file-pdf me-1"></i>Cetak PDF</button>
                </form>
            </div>
        </div>
    </div>
    {{-- ZA-16 --}}
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__body text-center">
                <div style="width:60px;height:60px;border-radius:15px;background:linear-gradient(135deg,#3498db,#2980b9);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:12px">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h5 class="fw-bold">Laporan ZA-16</h5>
                <p class="text-muted small">Neraca Saldo / Laporan Keuangan Tahunan</p>
                <form action="{{ route('print.za16') }}" method="GET" target="_blank" class="mt-3">
                    <div class="row g-2 justify-content-center mb-2">
                        <div class="col-5"><input type="number" name="tahun" class="form-control form-control-sm" value="{{ date('Y') }}"></div>
                    </div>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-file-pdf me-1"></i>Cetak PDF</button>
                </form>
            </div>
        </div>
    </div>
    {{-- Rekap Insentif --}}
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__body text-center">
                <div style="width:60px;height:60px;border-radius:15px;background:linear-gradient(135deg,#e67e22,#d35400);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:12px">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h5 class="fw-bold">Rekap Insentif</h5>
                <p class="text-muted small">Rekap insentif petugas bulanan</p>
                <form action="{{ route('print.rekap-insentif') }}" method="GET" target="_blank" class="mt-3">
                    <div class="row g-2 justify-content-center mb-2">
                        <div class="col-5"><select name="bulan" class="form-select form-select-sm">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m==date('n'))>{{ DateTime::createFromFormat('!m',$m)->format('M') }}</option>@endfor</select></div>
                        <div class="col-4"><input type="number" name="tahun" class="form-control form-control-sm" value="{{ date('Y') }}"></div>
                    </div>
                    <button class="btn btn-warning btn-sm text-white"><i class="fas fa-file-pdf me-1"></i>Cetak PDF</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
