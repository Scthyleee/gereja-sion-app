@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
{{-- Welcome --}}
<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:var(--text)"><i class="fas fa-cross me-2" style="color:var(--primary)"></i>Selamat Datang, {{ auth()->user()->nama }}</h4>
    <p class="text-muted mb-0">Sistem Informasi Gereja Kalimantan Evangelis — Resort Palangka Raya Tengah</p>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-wallet"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Saldo Berjalan</div>
                <div class="stat-card__value">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Penerimaan {{ $tahun }}</div>
                <div class="stat-card__value">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--red">
            <div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Pengeluaran {{ $tahun }}</div>
                <div class="stat-card__value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--orange">
            <div class="stat-card__icon"><i class="fas fa-users"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Jemaat Aktif</div>
                <div class="stat-card__value">{{ $totalJemaat }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Jadwal Mendatang --}}
    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="content-card__header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>Jadwal Ibadah Mendatang</h6>
                <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="content-card__body p-0">
                @forelse($jadwalMendatang as $jd)
                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center" style="border-color:var(--border)!important">
                    <div>
                        <div class="fw-semibold small">{{ $jd->jenis_ibadah }}</div>
                        <div class="text-muted" style="font-size:.75rem">
                            <i class="fas fa-calendar-day me-1"></i>{{ $jd->tanggal->translatedFormat('l, d M Y') }}
                            @if($jd->waktu) · <i class="fas fa-clock ms-1 me-1"></i>{{ substr($jd->waktu, 0, 5) }} @endif
                        </div>
                    </div>
                    <span class="badge bg-{{ $jd->status === 'completed' ? 'success' : ($jd->status === 'cancelled' ? 'danger' : 'warning') }}">
                        {{ $jd->status === 'completed' ? 'Selesai' : ($jd->status === 'cancelled' ? 'Batal' : 'Terjadwal') }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-times fa-2x mb-2 d-block" style="opacity:.3"></i>Belum ada jadwal mendatang
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Warta Jemaat --}}
    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="content-card__header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-newspaper me-2"></i>Warta Jemaat Terbaru</h6>
                <a href="{{ route('warta.index') }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="content-card__body p-0">
                @forelse($wartaTerbaru as $w)
                <a href="{{ route('warta.show', $w->id) }}" class="d-block px-3 py-2 border-bottom text-decoration-none" style="border-color:var(--border)!important">
                    <div class="fw-semibold small" style="color:var(--text)">Warta Minggu {{ $w->minggu_ke }} — {{ \Carbon\Carbon::createFromDate($w->tahun, $w->bulan, 1)->translatedFormat('F Y') }}</div>
                    <div class="text-muted" style="font-size:.75rem">
                        <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($w->tanggal_awal)->format('d M Y') }} — {{ \Carbon\Carbon::parse($w->tanggal_akhir)->format('d M Y') }}
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-newspaper fa-2x mb-2 d-block" style="opacity:.3"></i>Belum ada warta
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-3 mt-1">
    @if(auth()->user()->canAccess('keuangan'))
    <div class="col-md-2">
        <a href="{{ route('keuangan.index') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-3">
                <i class="fas fa-chart-line fa-lg mb-2 d-block" style="color:var(--primary)"></i>
                <div class="small fw-semibold">Keuangan</div>
            </div>
        </a>
    </div>
    @endif
    @if(auth()->user()->canAccess('pendaftaran'))
    <div class="col-md-2">
        <a href="{{ route('baptis.index') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-3">
                <i class="fas fa-water fa-lg mb-2 d-block" style="color:#4caf50"></i>
                <div class="small fw-semibold">Baptis</div>
            </div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('nikah.index') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-3">
                <i class="fas fa-ring fa-lg mb-2 d-block" style="color:#e91e63"></i>
                <div class="small fw-semibold">Nikah</div>
            </div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('keluarga.index') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-3">
                <i class="fas fa-home fa-lg mb-2 d-block" style="color:#ff9800"></i>
                <div class="small fw-semibold">Keluarga</div>
            </div>
        </a>
    </div>
    @endif
    @if(auth()->user()->canAccess('jemaat'))
    <div class="col-md-2">
        <a href="{{ route('jemaat.index') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-3">
                <i class="fas fa-users fa-lg mb-2 d-block" style="color:#2196f3"></i>
                <div class="small fw-semibold">Jemaat</div>
            </div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('lingkungan.index') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-3">
                <i class="fas fa-map-marker-alt fa-lg mb-2 d-block" style="color:#9c27b0"></i>
                <div class="small fw-semibold">Lingkungan</div>
            </div>
        </a>
    </div>
    @endif
</div>
@endsection
