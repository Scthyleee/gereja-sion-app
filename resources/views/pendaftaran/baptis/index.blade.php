@extends('layouts.app')
@section('title', 'Pendaftaran Baptis')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:linear-gradient(135deg,#4a90d9,#6bb5f0)"><i class="fas fa-water fa-lg text-white"></i></div>
        <div><div class="small text-muted">Total Pendaftaran</div><div class="fs-4 fw-bold">{{ $stats['total'] }}</div></div>
    </div></div></div>
    <div class="col-md-4"><div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:linear-gradient(135deg,#e8a838,#f0c860)"><i class="fas fa-clock fa-lg text-white"></i></div>
        <div><div class="small text-muted">Menunggu Proses</div><div class="fs-4 fw-bold">{{ $stats['pending'] }}</div></div>
    </div></div></div>
    <div class="col-md-4"><div class="content-card"><div class="content-card__body d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:linear-gradient(135deg,#6b8f71,#8bb896)"><i class="fas fa-check-circle fa-lg text-white"></i></div>
        <div><div class="small text-muted">Selesai Baptis</div><div class="fs-4 fw-bold">{{ $stats['selesai'] }}</div></div>
    </div></div></div>
</div>

<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-auto"><label class="form-label small">Status</label>
                        <select name="status" class="form-select form-select-sm" style="width:160px">
                            <option value="semua" @selected($status==='semua')>Semua</option>
                            <option value="pending" @selected($status==='pending')>Pending</option>
                            <option value="terdaftar" @selected($status==='terdaftar')>Terdaftar</option>
                            <option value="dijadwalkan" @selected($status==='dijadwalkan')>Dijadwalkan</option>
                            <option value="selesai" @selected($status==='selesai')>Selesai</option>
                            <option value="ditolak" @selected($status==='ditolak')>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('baptis.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Pendaftaran Baru</a>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-water me-2"></i>Daftar Pendaftaran Baptis</h6></div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tanggal</th><th>Nama</th><th>Lingkungan</th><th>Jadwal Baptis</th><th>Status</th><th style="width:120px">Aksi</th></tr></thead>
            <tbody>
                @forelse($data as $b)
                <tr>
                    <td class="small">{{ $b->created_at->format('d/m/Y') }}</td>
                    <td class="fw-semibold"><a href="{{ route('baptis.show', $b) }}" class="text-decoration-none">{{ $b->nama_lengkap }}</a></td>
                    <td><span class="badge bg-info">{{ $b->lingkunganSion?->nama ?? '-' }}</span></td>
                    <td class="small">{{ $b->tanggal_baptis_rencana?->format('d/m/Y') ?? 'Belum dijadwalkan' }}</td>
                    <td>
                        @php $colors = ['pending'=>'warning','terdaftar'=>'info','lulus_katekisasi'=>'primary','dijadwalkan'=>'secondary','selesai'=>'success','ditolak'=>'danger']; @endphp
                        <span class="badge bg-{{ $colors[$b->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('baptis.show', $b) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                        @if(in_array($b->status, ['pending','ditolak']))
                        <form action="{{ route('baptis.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-water fa-2x mb-2 d-block"></i>Belum ada pendaftaran baptis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    @if($data->hasPages())<div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3"><small class="text-muted">Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results</small>{{ $data->links('pagination::bootstrap-5') }}</div>@endif
</div>
@endsection
