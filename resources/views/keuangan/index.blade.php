@extends('layouts.app')
@section('title', 'Manajemen Keuangan')

@section('content')
{{-- Stats Cards --}}
<div class="row g-3 mb-4">
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
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-wallet"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Saldo</div>
                <div class="stat-card__value">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card--orange">
            <div class="stat-card__icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Selisih</div>
                @php $selisih = $totalPenerimaan - $totalPengeluaran @endphp
                <div class="stat-card__value {{ $selisih >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $selisih >= 0 ? '+' : '' }}Rp {{ number_format($selisih, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tab Navigation --}}
<ul class="nav nav-pills mb-4" style="gap:6px">
    @foreach(['ringkasan' => ['fas fa-chart-pie', 'Ringkasan'], 'realisasi' => ['fas fa-tasks', 'Realisasi RAPP'], 'grafik' => ['fas fa-chart-bar', 'Grafik']] as $t => $info)
    <li class="nav-item">
        <a href="{{ route('keuangan.index', ['tab' => $t, 'tahun' => $tahun]) }}"
           class="nav-link {{ $tab === $t ? 'active' : '' }}">
            <i class="{{ $info[0] }} me-1"></i>{{ $info[1] }}
        </a>
    </li>
    @endforeach
    <li class="ms-auto">
        <form class="d-flex align-items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="tahun" class="form-select form-select-sm" style="width:110px" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </li>
</ul>

@if($tab === 'ringkasan')
{{-- RINGKASAN SALDO PER KANTONG --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-piggy-bank me-2"></i>Saldo per Kantong Dana — {{ $tahun }}</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            @foreach($saldoRingkas as $s)
            <div class="col-md-4">
                <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border)">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-semibold">{{ $s['label'] }}</span>
                        <span class="badge {{ $s['akhir'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $s['akhir'] >= 0 ? 'Positif' : 'Defisit' }}
                        </span>
                    </div>
                    <div class="fs-5 fw-bold" style="color:var(--primary)">
                        Rp {{ number_format($s['akhir'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Quick Links --}}
<div class="row g-3 mt-1">
    <div class="col-md-4">
        <a href="{{ route('keuangan.transaksi') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-4">
                <i class="fas fa-exchange-alt fa-2x mb-2" style="color:var(--primary)"></i>
                <div class="fw-semibold">Transaksi</div>
                <div class="small text-muted">Input & kelola transaksi</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('keuangan.saldo') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-4">
                <i class="fas fa-wallet fa-2x mb-2" style="color:var(--accent)"></i>
                <div class="fw-semibold">Detail Saldo</div>
                <div class="small text-muted">Saldo lengkap per kantong</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('keuangan.master') }}" class="content-card d-block text-decoration-none h-100">
            <div class="content-card__body text-center py-4">
                <i class="fas fa-book fa-2x mb-2" style="color:var(--info)"></i>
                <div class="fw-semibold">Master Akun ZA6</div>
                <div class="small text-muted">Daftar kode akun</div>
            </div>
        </a>
    </div>
</div>

@elseif($tab === 'realisasi')
{{-- REALISASI RAPP --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Realisasi RAPP {{ $tahun }}</h6>
        @if($rappApproved)
        <span class="badge bg-success"><i class="fas fa-check me-1"></i>{{ $rappApproved->judul }}</span>
        @endif
    </div>
    <div class="content-card__body">
        @if(count($rappRealisasi) > 0)
        {{-- Penerimaan --}}
        <h6 class="fw-bold text-success mb-3"><i class="fas fa-arrow-down me-1"></i>Penerimaan</h6>
        @foreach($rappRealisasi as $r)
            @if($r['tipe'] === 'penerimaan')
            <div class="mb-3 p-3 rounded" style="background:var(--bg);border:1px solid var(--border)">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <code style="color:var(--primary-light)">{{ $r['kode'] }}</code>
                        <span class="ms-2 small fw-semibold">{{ $r['nama'] }}</span>
                    </div>
                    <span class="badge bg-{{ $r['barColor'] }}">{{ $r['status'] }} — {{ $r['persen'] }}%</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Anggaran: Rp {{ number_format($r['anggaran'], 0, ',', '.') }}</span>
                    <span>Aktual: Rp {{ number_format($r['aktual'], 0, ',', '.') }}</span>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar bg-{{ $r['barColor'] }}" style="width:{{ min($r['persen'], 100) }}%"></div>
                </div>
            </div>
            @endif
        @endforeach

        <hr style="border-color:var(--border)">

        {{-- Pengeluaran --}}
        <h6 class="fw-bold text-danger mb-3 mt-4"><i class="fas fa-arrow-up me-1"></i>Pengeluaran</h6>
        @foreach($rappRealisasi as $r)
            @if($r['tipe'] === 'pengeluaran')
            <div class="mb-3 p-3 rounded" style="background:var(--bg);border:1px solid var(--border)">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <code style="color:var(--primary-light)">{{ $r['kode'] }}</code>
                        <span class="ms-2 small fw-semibold">{{ $r['nama'] }}</span>
                    </div>
                    <span class="badge bg-{{ $r['barColor'] }}">{{ $r['status'] }} — {{ $r['persen'] }}%</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Anggaran: Rp {{ number_format($r['anggaran'], 0, ',', '.') }}</span>
                    <span>Aktual: Rp {{ number_format($r['aktual'], 0, ',', '.') }}</span>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar bg-{{ $r['barColor'] }}" style="width:{{ min($r['persen'], 100) }}%"></div>
                </div>
            </div>
            @endif
        @endforeach
        @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-clipboard-list fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p>Belum ada RAPP yang disetujui untuk tahun {{ $tahun }}.</p>
            <a href="{{ route('pedoman.index') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Buat RAPP</a>
        </div>
        @endif
    </div>
</div>

@elseif($tab === 'grafik')
{{-- GRAFIK KEUANGAN --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Grafik Keuangan {{ $tahun }}</h6></div>
    <div class="content-card__body">
        <canvas id="chartKeuangan" height="350"></canvas>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-md-6">
        <div class="content-card">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Komposisi Penerimaan</h6></div>
            <div class="content-card__body"><canvas id="chartPenerimaan" height="250"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Komposisi Pengeluaran</h6></div>
            <div class="content-card__body"><canvas id="chartPengeluaran" height="250"></canvas></div>
        </div>
    </div>
</div>

@endif
@endsection

@if($tab === 'grafik')
@push('scripts')
<script>
const cd = @json($chartData);
const bl = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.08)';
const textColor = isDark ? '#b5b3ae' : '#666';

new Chart(document.getElementById('chartKeuangan'), {
    type: 'bar',
    data: {
        labels: bl,
        datasets: [
            { label:'Penerimaan', data:cd.map(d=>d.penerimaan), backgroundColor:'rgba(106,173,118,.75)', borderRadius:6 },
            { label:'Pengeluaran', data:cd.map(d=>d.pengeluaran), backgroundColor:'rgba(224,96,96,.75)', borderRadius:6 },
        ]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ position:'bottom', labels:{ color:textColor } } },
        scales:{
            y:{ beginAtZero:true, grid:{ color:gridColor }, ticks:{ color:textColor, callback:v=>'Rp '+(v/1e6).toFixed(0)+'jt' } },
            x:{ grid:{ display:false }, ticks:{ color:textColor } }
        }
    }
});

// Pie charts - Penerimaan by kantong
const kantongData = @json($saldoRingkas);
const pieColors = ['#c9a84c','#6aad76','#6b8fc0','#e06060','#ff9800','#9c27b0'];
new Chart(document.getElementById('chartPenerimaan'), {
    type: 'doughnut',
    data: {
        labels: kantongData.map(k=>k.label),
        datasets: [{ data: kantongData.map(k=>Math.max(k.akhir,0)), backgroundColor: pieColors, borderWidth:0 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{ color:textColor, font:{size:11} } } } }
});
new Chart(document.getElementById('chartPengeluaran'), {
    type: 'doughnut',
    data: {
        labels: bl,
        datasets: [{ data: cd.map(d=>d.pengeluaran), backgroundColor: pieColors.concat(pieColors), borderWidth:0 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{ color:textColor, font:{size:11} } } } }
});
</script>
@endpush
@endif
