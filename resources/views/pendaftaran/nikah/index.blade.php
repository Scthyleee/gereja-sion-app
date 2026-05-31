@extends('layouts.app')
@section('title', 'Pendaftaran Pernikahan')

@section('content')
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
                            <option value="divalidasi" @selected($status==='divalidasi')>Divalidasi</option>
                            <option value="selesai" @selected($status==='selesai')>Selesai</option>
                        </select>
                    </div>
                    <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('nikah.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Pendaftaran Baru</a>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-ring me-2"></i>Daftar Pendaftaran Nikah</h6></div>
    <div class="content-card__body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tanggal</th><th>Mempelai Pria</th><th>Mempelai Wanita</th><th>Rencana Nikah</th><th>Total Biaya</th><th>Status</th><th style="width:120px">Aksi</th></tr></thead>
            <tbody>
                @forelse($data as $n)
                <tr>
                    <td class="small">{{ $n->created_at->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $n->pria_nama }}</td>
                    <td class="fw-semibold">{{ $n->wanita_nama }}</td>
                    <td class="small">{{ $n->tanggal_nikah_rencana?->format('d/m/Y') ?? '-' }}</td>
                    <td class="small">Rp {{ number_format($n->total_biaya + $n->total_insentif, 0, ',', '.') }}</td>
                    <td>
                        @php $colors = ['pending'=>'warning','terdaftar'=>'info','dijadwalkan'=>'secondary','divalidasi'=>'success','selesai'=>'success','ditolak'=>'danger']; @endphp
                        <span class="badge bg-{{ $colors[$n->status] ?? 'secondary' }}">{{ ucfirst($n->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('nikah.show', $n) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                        @if($n->status === 'pending')
                        <form action="{{ route('nikah.destroy', $n) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-ring fa-2x mb-2 d-block"></i>Belum ada pendaftaran nikah</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    @if($data->hasPages())<div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3"><small class="text-muted">Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results</small>{{ $data->links('pagination::bootstrap-5') }}</div>@endif
</div>
@endsection
