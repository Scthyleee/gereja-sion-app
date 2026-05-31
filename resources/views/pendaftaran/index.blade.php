@extends('layouts.app')
@section('title', 'Pendaftaran Jemaat')

@section('content')
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Jenis</label>
                        <select name="jenis" class="form-select" style="width:160px">
                            <option value="semua" @selected($jenis==='semua')>Semua</option>
                            <option value="warga" @selected($jenis==='warga')>Warga Baru</option>
                            <option value="baptis" @selected($jenis==='baptis')>Baptis</option>
                            <option value="sidi" @selected($jenis==='sidi')>Sidi</option>
                            <option value="pernikahan" @selected($jenis==='pernikahan')>Pernikahan</option>
                            <option value="pelayanan" @selected($jenis==='pelayanan')>Pelayanan</option>
                        </select>
                    </div>
                    <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDaftar"><i class="fas fa-plus me-1"></i>Pendaftaran Baru</button>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Daftar Pendaftaran</h6></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Biaya</th><th>Status</th><th style="width:140px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($pendaftaran as $p)
                    <tr>
                        <td class="small">{{ $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                        <td class="fw-semibold">{{ $p->nama_lengkap }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($p->jenis) }}</span></td>
                        <td class="small">{{ $p->biaya > 0 ? 'Rp '.number_format($p->biaya,0,',','.') : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status==='selesai' ? 'success' : ($p->status==='ditolak' ? 'danger' : ($p->status==='terdaftar' ? 'info' : 'warning')) }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td>
                            @if($p->status === 'pending')
                            <form action="{{ route('pendaftaran.status', $p->id_pendaftaran) }}" method="POST" class="d-inline">@csrf @method('PUT')
                                <input type="hidden" name="status" value="terdaftar">
                                <button class="btn btn-sm btn-outline-success" title="Setujui"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('pendaftaran.destroy', $p->id_pendaftaran) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-clipboard fa-2x mb-2 d-block"></i>Belum ada pendaftaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pendaftaran->hasPages())<div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3"><small class="text-muted">Showing {{ $pendaftaran->firstItem() }} to {{ $pendaftaran->lastItem() }} of {{ $pendaftaran->total() }} results</small>{{ $pendaftaran->links('pagination::bootstrap-5') }}</div>@endif
</div>

{{-- Modal --}}
<div class="modal fade" id="modalDaftar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Pendaftaran Baru</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('pendaftaran.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold">Jenis *</label>
                            <select name="jenis" class="form-select" required>
                                <option value="warga">Warga Baru</option><option value="baptis">Baptis</option>
                                <option value="sidi">Sidi</option><option value="pernikahan">Pernikahan</option>
                                <option value="pelayanan">Pelayanan</option>
                            </select>
                        </div>
                        <div class="col-md-8"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="nama_lengkap" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select"><option value="">-</option><option>Laki-laki</option><option>Perempuan</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Alamat</label><input type="text" name="alamat" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Nama Ayah</label><input type="text" name="nama_ayah" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Nama Ibu</label><input type="text" name="nama_ibu" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Biaya (Rp)</label><input type="number" name="biaya" class="form-control" value="0" min="0"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Akun Penerimaan</label>
                            <select name="kode_akun_penerimaan_id" class="form-select"><option value="">-</option>
                                @foreach($akunPenerimaan as $a)<option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->nama,40) }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label fw-semibold">Keterangan</label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Daftar</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
