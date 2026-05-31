@extends('layouts.app')
@section('title', 'Input Kategorial')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-clipboard-list me-2" style="color:var(--primary)"></i>Input Kategorial</h5>
        <p class="text-muted mb-0 small">Input data keuangan dari setiap Unit Pelayanan</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        @if($pendingCount > 0 && (auth()->user()->canValidate() || auth()->user()->hasRole('Super Admin')))
        <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>{{ $pendingCount }} Menunggu Validasi</span>
        @endif
        <a href="{{ route('input-kategorial.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Buat Input</a>
    </div>
</div>

{{-- Filters --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select form-select-sm" style="width:130px">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($bulan==$m)>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm" value="{{ $tahun }}" style="width:90px">
            </div>
            @if(auth()->user()->hasRole(['Super Admin','Admin','Bendahara','Ketua MPH','Staf']))
            <div class="col-auto">
                <label class="form-label">Unit Pelayanan</label>
                <select name="up" class="form-select form-select-sm" style="width:180px">
                    <option value="">Semua UP</option>
                    @foreach($unitList as $u)
                    <option value="{{ $u->nomor_up }}" @selected($filterUp==$u->nomor_up)>{{ $u->inisial }} — {{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-auto">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm" style="width:130px">
                    <option value="">Semua</option>
                    <option value="draft" @selected($status==='draft')>Draft</option>
                    <option value="diajukan" @selected($status==='diajukan')>Diajukan</option>
                    <option value="disetujui" @selected($status==='disetujui')>Disetujui</option>
                    <option value="ditolak" @selected($status==='ditolak')>Ditolak</option>
                </select>
            </div>
            <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filter</button></div>
        </form>
    </div>
</div>

{{-- Input List --}}
<div class="content-card">
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:110px">Tanggal</th>
                        <th>Unit Pelayanan</th>
                        <th>Jenis</th>
                        <th>Ibadah</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th>Pembuat</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inputs as $inp)
                    <tr>
                        <td class="small">{{ $inp->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-info">{{ $inp->unitPelayanan->inisial ?? '-' }}</span>
                            <small class="text-muted">{{ $inp->unitPelayanan->nama ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ match($inp->jenis_input) { 'kolekte_ibadah' => 'primary', 'amplop_ps' => 'success', 'amplop_kk' => 'warning', 'pengeluaran' => 'danger' } }}">
                                {{ match($inp->jenis_input) { 'kolekte_ibadah' => 'Kolekte', 'amplop_ps' => 'PS', 'amplop_kk' => 'KK', 'pengeluaran' => 'Pengeluaran' } }}
                            </span>
                        </td>
                        <td class="small">{{ $inp->jenis_ibadah ?? '-' }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($inp->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ match($inp->status) { 'draft' => 'secondary', 'diajukan' => 'info', 'disetujui' => 'success', 'ditolak' => 'danger' } }}">
                                {{ ucfirst($inp->status) }}
                            </span>
                        </td>
                        <td class="small">{{ $inp->pembuat->nama ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('input-kategorial.show', $inp->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                                @if(in_array($inp->status, ['draft','ditolak']) && $inp->dibuat_oleh === auth()->id())
                                <a href="{{ route('input-kategorial.edit', $inp->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('input-kategorial.destroy', $inp->id) }}" method="POST" onsubmit="return confirm('Hapus input ini?')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x d-block mb-2 opacity-25"></i>Belum ada input untuk periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $inputs->links() }}</div>
@endsection
