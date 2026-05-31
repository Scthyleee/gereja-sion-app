@extends('layouts.app')
@section('title', 'Detail Input — ' . ($input->unitPelayanan->inisial ?? ''))

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('input-kategorial.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    <div class="d-flex gap-2 align-items-center">
        {{-- Status Badge --}}
        <span class="badge fs-6 bg-{{ match($input->status) { 'draft' => 'secondary', 'diajukan' => 'info', 'disetujui' => 'success', 'ditolak' => 'danger' } }}">
            <i class="fas fa-{{ match($input->status) { 'draft' => 'pencil-alt', 'diajukan' => 'clock', 'disetujui' => 'check-circle', 'ditolak' => 'times-circle' } }} me-1"></i>
            {{ ucfirst($input->status) }}
        </span>

        {{-- Action Buttons --}}
        @if($canEdit)
        <a href="{{ route('input-kategorial.edit', $input->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-pencil-alt me-1"></i>Edit</a>
        @endif

        @if($input->status === 'draft' && $input->dibuat_oleh === auth()->id())
        <form action="{{ route('input-kategorial.ajukan', $input->id) }}" method="POST" onsubmit="return confirm('Ajukan input ini untuk validasi?')">
            @csrf @method('PUT')
            <button class="btn btn-sm btn-info"><i class="fas fa-paper-plane me-1"></i>Ajukan</button>
        </form>
        @endif

        @if($input->status === 'diajukan' && $canValidate)
        <form action="{{ route('input-kategorial.approve', $input->id) }}" method="POST" onsubmit="return confirm('Setujui input ini? Transaksi akan dibuat otomatis.')">
            @csrf @method('PUT')
            <button class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Setujui</button>
        </form>
        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolak"><i class="fas fa-times me-1"></i>Tolak</button>
        @endif

        @if($canDelete)
        <form action="{{ route('input-kategorial.destroy', $input->id) }}" method="POST" onsubmit="return confirm('Hapus input ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i>Hapus</button>
        </form>
        @endif
    </div>
</div>

{{-- Rejection Notice --}}
@if($input->status === 'ditolak' && $input->catatan_penolakan)
<div class="alert alert-danger mb-3">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Alasan Penolakan:</strong> {{ $input->catatan_penolakan }}
    <br><small class="text-muted">Ditolak oleh {{ $input->validator->nama ?? '-' }} pada {{ $input->validated_at?->format('d/m/Y H:i') }}</small>
</div>
@endif

{{-- Input Info --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row">
            <div class="col-md-2">
                <small class="text-muted d-block">Unit Pelayanan</small>
                <span class="badge bg-info">{{ $input->unitPelayanan->inisial ?? '-' }}</span>
                <small>{{ $input->unitPelayanan->nama ?? '' }}</small>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Jenis Input</small>
                <span class="badge bg-{{ match($input->jenis_input) { 'kolekte_ibadah' => 'primary', 'amplop_ps' => 'success', 'amplop_kk' => 'warning', 'pengeluaran' => 'danger' } }}">
                    {{ match($input->jenis_input) { 'kolekte_ibadah' => 'Kantong Kolekte', 'amplop_ps' => 'Amplop PS', 'amplop_kk' => 'Amplop KK', 'pengeluaran' => 'Pengeluaran' } }}
                </span>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Tanggal</small>
                <strong>{{ $input->tanggal->format('d/m/Y') }}</strong>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Jenis Ibadah</small>
                <span>{{ $input->jenis_ibadah ?? '-' }}</span>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Pembuat</small>
                <span>{{ $input->pembuat->nama ?? '-' }}</span>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Catatan</small>
                <span>{{ $input->catatan ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-receipt"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Jumlah Item</div>
                <div class="stat-card__value">{{ $input->detail->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-coins"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Nominal</div>
                <div class="stat-card__value">Rp {{ number_format($input->total, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--{{ $input->status === 'disetujui' ? 'green' : ($input->status === 'ditolak' ? 'red' : 'blue') }}">
            <div class="stat-card__icon"><i class="fas fa-{{ $input->status === 'disetujui' ? 'check-double' : ($input->status === 'ditolak' ? 'ban' : 'hourglass-half') }}"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Validasi</div>
                <div class="stat-card__value">{{ $input->validator ? $input->validator->nama : 'Belum' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Detail Table --}}
<div class="content-card">
    <div class="content-card__header">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detail Item ({{ $input->detail->count() }} baris)</h6>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th style="width:150px">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Penyetor</th>
                        <th>Keterangan</th>
                        <th class="text-end" style="width:160px">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($input->detail as $i => $d)
                    <tr>
                        <td class="small text-muted">{{ $i + 1 }}</td>
                        <td><code class="text-primary small">{{ $d->kodeAkun->kode ?? '-' }}</code></td>
                        <td class="small">{{ $d->kodeAkun->nama ?? '-' }}</td>
                        <td class="small">{{ $d->nama_penyetor ?? '-' }}</td>
                        <td class="small text-muted">{{ $d->keterangan ?? '-' }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($d->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold" style="background:rgba(46,204,113,.1)">
                        <td colspan="5" class="text-end">TOTAL</td>
                        <td class="text-end">Rp {{ number_format($input->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
@if($canValidate && $input->status === 'diajukan')
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title fw-bold"><i class="fas fa-times-circle text-danger me-2"></i>Tolak Input</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('input-kategorial.reject', $input->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <p class="text-muted">Input dari <strong>{{ $input->pembuat->nama ?? '-' }}</strong> ({{ $input->unitPelayanan->inisial ?? '' }}) sebesar <strong>Rp {{ number_format($input->total, 0, ',', '.') }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_penolakan" class="form-control" rows="3" required placeholder="Tuliskan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger"><i class="fas fa-times me-1"></i>Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
