@extends('layouts.app')
@section('title', 'Master Akun ZA6')

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-book"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Total Kode Akun</div>
                <div class="stat-card__value">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Penerimaan</div>
                <div class="stat-card__value">{{ $stats['penerimaan'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card--red">
            <div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Pengeluaran</div>
                <div class="stat-card__value">{{ $stats['pengeluaran'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tipe</label>
                <select name="tipe" class="form-select">
                    <option value="semua" @selected($tipe==='semua')>Semua</option>
                    <option value="penerimaan" @selected($tipe==='penerimaan')>Penerimaan</option>
                    <option value="pengeluaran" @selected($tipe==='pengeluaran')>Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Unit Pelayanan</label>
                <select name="up" class="form-select">
                    <option value="">Semua UP</option>
                    @foreach($units as $u)
                        <option value="{{ $u->nomor_up }}" @selected($up==$u->nomor_up)>
                            {{ sprintf('%02d', $u->nomor_up) }} — {{ $u->inisial }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Cari</label>
                <input type="text" name="q" class="form-control" placeholder="Kode, nama, atau sumber..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Kode Akun</h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary">{{ $akun->total() }} akun</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
                <i class="fas fa-plus me-1"></i>Tambah Akun
            </button>
        </div>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:180px">Kode</th>
                        <th>Nama</th>
                        <th style="width:100px">Tipe</th>
                        <th style="width:80px">UP</th>
                        <th style="width:100px">Kategori</th>
                        <th>Sumber Lapangan</th>
                        <th style="width:90px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($akun as $a)
                    <tr class="{{ $a->parent_kode ? 'table-row-child' : '' }}">
                        <td>
                            @if($a->parent_kode)
                                <code class="text-info ms-3">↳ {{ $a->kode }}</code>
                            @else
                                <code class="text-primary fw-bold">{{ $a->kode }}</code>
                            @endif
                        </td>
                        <td class="{{ $a->parent_kode ? 'ps-4 small' : '' }}">{{ $a->nama }}</td>
                        <td>
                            <span class="badge bg-{{ $a->tipe === 'penerimaan' ? 'success' : 'danger' }}">
                                {{ ucfirst($a->tipe) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $a->unitPelayanan->inisial ?? '-' }}</span>
                        </td>
                        <td>
                            @if($a->kategori_akun === 'kategorial')
                                <span class="badge bg-warning text-dark">Kategorial</span>
                            @else
                                <span class="badge bg-info">MPH</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ Str::limit($a->sumber_lapangan, 60) }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-secondary btn-sm" title="Edit"
                                    onclick="editAkun({{ $a->id }}, '{{ addslashes($a->kode) }}', '{{ addslashes($a->nama) }}', '{{ $a->tipe }}', {{ $a->nomor_up }}, '{{ $a->kategori_akun }}', '{{ addslashes($a->sumber_lapangan ?? '') }}', '{{ addslashes($a->keterangan ?? '') }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('keuangan.akun.destroy', $a) }}" method="POST" onsubmit="return confirm('Nonaktifkan kode akun {{ $a->kode }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" title="Nonaktifkan"><i class="fas fa-ban"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($akun->hasPages())
    <div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <small class="text-muted">Showing {{ $akun->firstItem() }} to {{ $akun->lastItem() }} of {{ $akun->total() }} results</small>
        {{ $akun->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Modal Tambah Kode Akun --}}
<div class="modal fade" id="modalTambahAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus me-2"></i>Tambah Kode Akun</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('keuangan.akun.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Akun <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" placeholder="Contoh: I(APn).01.01h" required>
                            <small class="text-muted">Kode harus unik dan belum digunakan</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Parent Kode</label>
                            <input type="text" name="parent_kode" class="form-control" placeholder="Kosongkan jika akun utama">
                            <small class="text-muted">Isi jika ini sub-akun dari akun lain</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama kode akun" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select" required>
                                <option value="penerimaan">Penerimaan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit Pelayanan <span class="text-danger">*</span></label>
                            <select name="nomor_up" class="form-select" required>
                                @foreach($units as $u)
                                    <option value="{{ $u->nomor_up }}">{{ sprintf('%02d', $u->nomor_up) }} — {{ $u->inisial }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_akun" class="form-select" required>
                                <option value="mph">MPH</option>
                                <option value="kategorial">Kategorial</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sumber Lapangan</label>
                            <input type="text" name="sumber_lapangan" class="form-control" placeholder="Contoh: kolekte, persembahan syukur, dll">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Kode Akun --}}
<div class="modal fade" id="modalEditAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Kode Akun — <span id="editKodeLabel"></span></h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditAkun" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                            <select name="tipe" id="editTipe" class="form-select" required>
                                <option value="penerimaan">Penerimaan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit Pelayanan</label>
                            <select name="nomor_up" id="editNomorUp" class="form-select">
                                @foreach($units as $u)
                                    <option value="{{ $u->nomor_up }}">{{ sprintf('%02d', $u->nomor_up) }} — {{ $u->inisial }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_akun" id="editKategori" class="form-select" required>
                                <option value="mph">MPH</option>
                                <option value="kategorial">Kategorial</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sumber Lapangan</label>
                            <input type="text" name="sumber_lapangan" id="editSumber" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" id="editKeterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editAkun(id, kode, nama, tipe, nomorUp, kategori, sumber, keterangan) {
    document.getElementById('editKodeLabel').textContent = kode;
    document.getElementById('editNama').value = nama;
    document.getElementById('editTipe').value = tipe;
    document.getElementById('editNomorUp').value = nomorUp;
    document.getElementById('editKategori').value = kategori;
    document.getElementById('editSumber').value = sumber;
    document.getElementById('editKeterangan').value = keterangan;
    document.getElementById('formEditAkun').action = '/admin/keuangan/master-akun/' + id;
    new bootstrap.Modal(document.getElementById('modalEditAkun')).show();
}

// Auto-open modal if validation errors exist
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalTambahAkun')).show();
    });
@endif
</script>
@endpush
@endsection
