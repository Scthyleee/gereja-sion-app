@extends('layouts.app')
@section('title', 'Kantong Kolekte (KK)')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-church"></i></div><div class="stat-card__body"><div class="stat-card__label">Total KK {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</div><div class="stat-card__value">Rp {{ number_format($totalBulan, 0, ',', '.') }}</div></div></div></div>
    <div class="col-md-6"><div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-list-alt"></i></div><div class="stat-card__body"><div class="stat-card__label">Jumlah Ibadah</div><div class="stat-card__value">{{ $summary->count() }}</div></div></div></div>
</div>

{{-- Filter --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small fw-semibold">Bulan</label><input type="month" name="bulan" class="form-control" value="{{ $bulan }}"></div>
            <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
            <div class="col-auto"><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahKK"><i class="fas fa-plus me-1"></i>Input KK</button></div>
        </form>
    </div>
</div>

{{-- Summary per jenis ibadah --}}
@if($summary->count())
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Rekap Per Jenis Ibadah</h6></div>
    <div class="content-card__body">
        <div class="row g-2">@foreach($summary as $s)
            <div class="col-md-4"><div class="d-flex justify-content-between p-2 rounded" style="background:var(--bg-body)"><span class="small fw-semibold">{{ $s->jenis_ibadah }}</span><span class="fw-bold text-success">Rp {{ number_format($s->total, 0, ',', '.') }}</span></div></div>
        @endforeach</div>
    </div>
</div>
@endif

{{-- Table --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar KK</h6><span class="badge bg-secondary">{{ $transaksi->total() }}</span></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Tanggal</th><th>Jenis Ibadah</th><th>Minggu Ke</th><th>Jumlah Kantong</th><th>Petugas</th><th class="text-end">Jumlah (Rp)</th><th>Status</th><th style="width:80px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($transaksi as $t)
                    <tr>
                        <td class="small">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->jenis_ibadah }}</td>
                        <td><span class="badge bg-info">Minggu {{ $t->minggu_ke }}</span></td>
                        <td>{{ $t->jml_kantong }}</td>
                        <td class="small">{{ $t->petugas_hitung ?? '-' }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $t->status === 'verified' ? 'success' : 'warning' }}">{{ ucfirst($t->status) }}</span></td>
                        <td>
                            @if($t->status === 'draft')
                            <form action="{{ route('kk.verify', $t->id) }}" method="POST" class="d-inline">@csrf @method('PUT')
                                <button class="btn btn-sm btn-outline-success" title="Verifikasi"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('kk.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada data KK bulan ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transaksi->hasPages())<div class="content-card__body border-top">{{ $transaksi->links() }}</div>@endif
</div>

{{-- Modal Tambah KK --}}
<div class="modal fade" id="modalTambahKK" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-church me-2"></i>Input Kantong Kolekte</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('kk.store') }}" method="POST">@csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">Tanggal *</label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Jenis Ibadah *</label>
                            <select name="jenis_ibadah" class="form-select" required id="jenisIbadahSelect">
                                <option>KK Minggu Gereja Sion Pukul 08.00 WIB</option>
                                <option>KK Minggu Gereja Sion Pukul 17.00 WIB</option>
                                <option>KK Maju Kedepan</option>
                                <option>KK Ibadah SPA</option>
                                <option>KK Ibadah SPPer</option>
                                <option>KK Ibadah Sidang Majelis</option>
                                <option>KK Online</option>
                                <option>KK HUT</option>
                                <option>KK Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Jumlah (Rp) *</label><input type="number" name="jumlah" class="form-control" min="0" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Jumlah Kantong</label><input type="number" name="jml_kantong" class="form-control" value="0"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Petugas Hitung</label><input type="text" name="petugas_hitung" class="form-control"></div>
                        <div class="col-md-5"><label class="form-label fw-semibold">Diterima Oleh</label><input type="text" name="diterima_oleh" class="form-control" value="{{ auth()->user()->nama }}"></div>
                        <div class="col-md-12" id="kkOnlineField" style="display:none">
                            <label class="form-label fw-semibold">Pengirim KK Online (opsional)</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Nama pengirim / No. referensi transfer">
                        </div>
                        <div class="col-md-12"><label class="form-label fw-semibold">Keterangan</label><input type="text" name="keterangan" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('jenisIbadahSelect')?.addEventListener('change', function() {
    document.getElementById('kkOnlineField').style.display = this.value === 'KK Online' ? 'block' : 'none';
});
</script>
@endpush
