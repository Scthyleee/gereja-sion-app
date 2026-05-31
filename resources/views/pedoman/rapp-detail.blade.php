@extends('layouts.app')
@section('title', $rapp->judul)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2">
        <a href="{{ route('pedoman.index', ['tab'=>'rapp']) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
        @if($rapp->status === 'disetujui')
        <a href="{{ route('pedoman.rapp.realisasi', $rapp->id) }}" class="btn btn-sm btn-success"><i class="fas fa-chart-bar me-1"></i>Lihat Realisasi</a>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('print.rapp', $rapp->id) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf me-1"></i>Cetak PDF</a>
        @if($rapp->status === 'draft' && auth()->user()->canEdit('pedoman'))
        <form action="{{ route('pedoman.rapp.approve', $rapp->id) }}" method="POST" onsubmit="return confirm('Ajukan RAPP ini untuk review?')">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="diajukan">
            <button class="btn btn-sm btn-info"><i class="fas fa-paper-plane me-1"></i>Ajukan</button>
        </form>
        @endif
        @if($rapp->status === 'diajukan')
        <form action="{{ route('pedoman.rapp.approve', $rapp->id) }}" method="POST" onsubmit="return confirm('Setujui RAPP ini?')">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="disetujui">
            <button class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Setujui</button>
        </form>
        <form action="{{ route('pedoman.rapp.approve', $rapp->id) }}" method="POST" onsubmit="return confirm('Tolak RAPP ini?')">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="ditolak">
            <button class="btn btn-sm btn-danger"><i class="fas fa-times me-1"></i>Tolak</button>
        </form>
        @endif
        <span class="badge bg-{{ $rapp->status==='disetujui' ? 'success' : ($rapp->status==='ditolak' ? 'danger' : ($rapp->status==='diajukan' ? 'info' : 'warning')) }} fs-6 d-flex align-items-center">{{ ucfirst($rapp->status) }}</span>
    </div>
</div>

{{-- RAPP Info --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row">
            <div class="col-md-3"><small class="text-muted d-block">Judul</small><strong>{{ $rapp->judul }}</strong></div>
            <div class="col-md-2"><small class="text-muted d-block">Unit Pelayanan</small>
                @if($rapp->is_gabungan)<span class="badge bg-primary"><i class="fas fa-layer-group me-1"></i>Gabungan</span>
                @elseif($rapp->up_inisial)<span class="badge bg-info">{{ $rapp->up_inisial }}</span> <small>{{ $rapp->up_nama }}</small>
                @else <span class="text-muted">-</span> @endif
            </div>
            <div class="col-md-2"><small class="text-muted d-block">Tahun Anggaran</small><strong>{{ $rapp->tahun }}</strong></div>
            <div class="col-md-2"><small class="text-muted d-block">Status</small><span class="badge bg-{{ $rapp->status==='disetujui' ? 'success' : 'warning' }}">{{ ucfirst($rapp->status) }}</span></div>
            <div class="col-md-3"><small class="text-muted d-block">Catatan</small><span>{{ $rapp->catatan ?: '-' }}</span></div>
        </div>
    </div>
</div>

{{-- Summary --}}
@php
    $totalPenerimaan = $details->where('tipe','penerimaan')->sum(fn($d) => $d->volume * $d->nominal);
    $totalPengeluaran = $details->where('tipe','pengeluaran')->sum(fn($d) => $d->volume * $d->nominal);
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-card__body"><div class="stat-card__label">Total Penerimaan</div><div class="stat-card__value">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</div></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--red"><div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-card__body"><div class="stat-card__label">Total Pengeluaran</div><div class="stat-card__value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-card__body"><div class="stat-card__label">Selisih</div><div class="stat-card__value">Rp {{ number_format($totalPenerimaan - $totalPengeluaran, 0, ',', '.') }}</div></div>
        </div>
    </div>
</div>

{{-- Detail Table --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detail Per Akun ({{ $details->count() }} baris)
            @if($rapp->up_inisial) <span class="badge bg-info ms-2">{{ $rapp->up_inisial }}</span> @endif
        </h6>
        @if(in_array($rapp->status, ['draft', 'ditolak']) && auth()->user()->canEdit('pedoman'))
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDetail"><i class="fas fa-plus me-1"></i>Tambah</button>
        @endif
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th style="width:150px">Kode Akun</th><th>Nama</th><th>Keterangan</th><th class="text-center" style="width:80px">Volume</th><th class="text-end" style="width:140px">Nominal</th><th class="text-end" style="width:150px">Jumlah</th><th style="width:50px"></th></tr></thead>
                <tbody>
                    @php $letters = range('A', 'Z'); @endphp
                    @foreach($details->groupBy('tipe') as $tipe => $tipeItems)
                    <tr style="background:rgba(230,126,34,.15)"><td colspan="7" class="fw-bold text-uppercase fs-6">{{ $tipe === 'penerimaan' ? 'I. PENERIMAAN' : 'II. PENGELUARAN' }}</td></tr>

                    @if($rapp->is_gabungan)
                        {{-- Gabungan: Group by UP with letter sub-chapters --}}
                        @foreach($tipeItems->groupBy('nomor_up') as $upId => $upItems)
                        @php $upIdx = $loop->index; $upInisial = $upItems->first()->detail_up_inisial ?? 'Lainnya'; $upNama = $upItems->first()->detail_up_nama ?? ''; @endphp
                        <tr style="background:rgba(52,152,219,.08)">
                            <td colspan="7" class="fw-bold" style="padding-left:15px">
                                <span class="badge bg-info me-1">{{ $letters[$upIdx] ?? '' }}</span>
                                {{ $upInisial }} — {{ $upNama }}
                            </td>
                        </tr>
                        @foreach($upItems as $d)
                        <tr>
                            <td style="padding-left:25px"><code class="text-primary small">{{ $d->kode }}</code></td>
                            <td class="small">{{ $d->nama_akun }}</td>
                            <td class="small text-muted">{{ $d->keterangan ?: '-' }}</td>
                            <td class="text-center"><span class="badge bg-primary">{{ $d->volume }}x</span></td>
                            <td class="text-end small">Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($d->volume * $d->nominal, 0, ',', '.') }}</td>
                            <td>
                                @if(in_array($rapp->status, ['draft', 'ditolak']))
                                <form action="{{ route('pedoman.rapp.detail.destroy', $d->id) }}" method="POST">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="fw-bold" style="background:rgba(52,152,219,.05)">
                            <td colspan="5" style="padding-left:25px">Subtotal {{ $upInisial }}</td>
                            <td class="text-end">Rp {{ number_format($upItems->sum(fn($d) => $d->volume * $d->nominal), 0, ',', '.') }}</td><td></td>
                        </tr>
                        @endforeach
                    @else
                        {{-- Non-gabungan: flat list with inline edit --}}
                        @foreach($tipeItems as $d)
                        @if(in_array($rapp->status, ['draft', 'ditolak']))
                        <tr>
                            <form action="{{ route('pedoman.rapp.detail.update', $d->id) }}" method="POST" class="d-contents">
                            @csrf @method('PUT')
                            <td><code class="text-primary small">{{ $d->kode }}</code></td>
                            <td class="small">{{ $d->nama_akun }}</td>
                            <td><input type="text" name="keterangan" class="form-control form-control-sm" value="{{ $d->keterangan }}" placeholder="Keterangan"></td>
                            <td><input type="number" name="volume" class="form-control form-control-sm text-center" value="{{ $d->volume }}" min="1" style="width:65px"></td>
                            <td><input type="number" name="nominal" class="form-control form-control-sm text-end" value="{{ $d->nominal }}" min="0" style="width:120px"></td>
                            <td class="text-end fw-semibold">Rp {{ number_format($d->volume * $d->nominal, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-sm btn-link text-success p-0" title="Simpan"><i class="fas fa-check"></i></button>
                                </div>
                            </form>
                                <form action="{{ route('pedoman.rapp.detail.destroy', $d->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0" title="Hapus"><i class="fas fa-times"></i></button>
                                </form>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td><code class="text-primary small">{{ $d->kode }}</code></td>
                            <td class="small">{{ $d->nama_akun }}</td>
                            <td class="small text-muted">{{ $d->keterangan ?: '-' }}</td>
                            <td class="text-center"><span class="badge bg-primary">{{ $d->volume }}x</span></td>
                            <td class="text-end small">Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($d->volume * $d->nominal, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        @endif
                        @endforeach
                    @endif

                    <tr class="fw-bold" style="background:rgba(230,126,34,.1)"><td colspan="5">Total {{ ucfirst($tipe) }}</td><td class="text-end">Rp {{ number_format($tipeItems->sum(fn($d) => $d->volume * $d->nominal), 0, ',', '.') }}</td><td></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Detail --}}
@if(in_array($rapp->status, ['draft', 'ditolak']))
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Detail RAPP
                @if($rapp->up_inisial) <span class="badge bg-info ms-2">{{ $rapp->up_inisial }}</span> @endif
            </h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('pedoman.rapp.detail.store', $rapp->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Akun @if($rapp->up_inisial)<small class="text-info">(Hanya akun {{ $rapp->up_inisial }})</small>@endif</label>
                        <select name="kode_akun_id" class="form-select select-search" required>
                            <option value="">Pilih akun...</option>
                            <optgroup label="Penerimaan">
                                @foreach($akunList->where('tipe','penerimaan') as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->nama,60) }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Pengeluaran">
                                @foreach($akunList->where('tipe','pengeluaran') as $a)
                                    <option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->nama,60) }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Volume</label>
                            <input type="number" name="volume" class="form-control" value="1" min="1" id="inpVol">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nominal (Rp)</label>
                            <input type="number" name="nominal" class="form-control" min="0" required id="inpNom">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jumlah</label>
                            <input type="text" class="form-control" id="dispJumlah" readonly style="background:rgba(46,204,113,.1);font-weight:700">
                        </div>
                    </div>
                    <div class="mt-3"><label class="form-label fw-semibold">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="(Opsional)"></div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Tambah</button></div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function calcJumlah() {
    const v = parseInt(document.getElementById('inpVol')?.value || 1);
    const n = parseFloat(document.getElementById('inpNom')?.value || 0);
    const j = v * n;
    const el = document.getElementById('dispJumlah');
    if (el) el.value = 'Rp ' + j.toLocaleString('id-ID');
}
document.getElementById('inpVol')?.addEventListener('input', calcJumlah);
document.getElementById('inpNom')?.addEventListener('input', calcJumlah);
</script>
@endpush
