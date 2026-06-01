@extends('layouts.app')
@section('title', 'Setting Insentif')

@section('content')
{{-- Tabs --}}
<ul class="nav nav-pills mb-4" style="gap:8px">
    <li class="nav-item"><a href="{{ route('jadwal.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-calendar-alt me-1"></i>Jadwal</a></li>
    <li class="nav-item"><a href="{{ route('insentif.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-money-bill me-1"></i>Insentif</a></li>
    <li class="nav-item"><a href="{{ route('petugas.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-users-cog me-1"></i>Petugas</a></li>
    <li class="nav-item"><a href="{{ route('insentif.setting') }}" class="nav-link active" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none"><i class="fas fa-cog me-1"></i>Setting Insentif</a></li>
</ul>

<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Setting Biaya Insentif per Event & Tugas</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="content-card__body">
        <p class="text-muted small mb-3">Setup nominal insentif untuk setiap kombinasi <strong>Tipe Event</strong> dan <strong>Jenis Tugas</strong>. Nominal ini akan otomatis digunakan saat menambahkan petugas ke jadwal ibadah.</p>
    </div>
</div>

<div class="content-card">
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Tipe Event</th>
                    <th>Jenis Tugas</th>
                    <th>Nominal (Rp)</th>
                    <th>Status</th>
                    <th style="width:140px">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($configs as $c)
                    <tr>
                        <td><span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $c->tipe_event)) }}</span></td>
                        <td class="fw-semibold">{{ $c->jenis_tugas }}</td>
                        <td>Rp {{ number_format($c->nominal, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $c->aktif ? 'success' : 'secondary' }}">{{ $c->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-edit"
                                data-id="{{ $c->id }}"
                                data-tipe="{{ $c->tipe_event }}"
                                data-tugas="{{ $c->jenis_tugas }}"
                                data-nominal="{{ $c->nominal }}"
                                data-aktif="{{ $c->aktif }}"
                            ><i class="fas fa-edit"></i></button>
                            <form action="{{ route('insentif.config.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-cog fa-2x mb-2 d-block"></i>Belum ada konfigurasi insentif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Setting Insentif</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('insentif.config.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Event *</label>
                        <select name="tipe_event" class="form-select" required>
                            <option value="ibadah_biasa">Ibadah Biasa (Minggu)</option>
                            <option value="perayaan">Perayaan (Natal, Paskah, dll)</option>
                            <option value="pernikahan">Pemberkatan Pernikahan</option>
                            <option value="duka">Ibadah Duka/Penghiburan</option>
                            <option value="keluarga">Ibadah Keluarga</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas *</label>
                        <input type="text" name="jenis_tugas" class="form-control" required placeholder="Contoh: Song Leader, Pianis, Operator LCD...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal (Rp) *</label>
                        <input type="number" name="nominal" class="form-control" required min="0" step="1000" placeholder="100000">
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Setting Insentif</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Event *</label>
                        <select name="tipe_event" id="e_tipe" class="form-select" required>
                            <option value="ibadah_biasa">Ibadah Biasa (Minggu)</option>
                            <option value="perayaan">Perayaan (Natal, Paskah, dll)</option>
                            <option value="pernikahan">Pemberkatan Pernikahan</option>
                            <option value="duka">Ibadah Duka/Penghiburan</option>
                            <option value="keluarga">Ibadah Keluarga</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas *</label>
                        <input type="text" name="jenis_tugas" id="e_tugas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal (Rp) *</label>
                        <input type="number" name="nominal" id="e_nominal" class="form-control" required min="0" step="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="aktif" id="e_aktif" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = this.dataset;
        document.getElementById('formEdit').action = `/admin/insentif-setting/${d.id}`;
        document.getElementById('e_tipe').value = d.tipe;
        document.getElementById('e_tugas').value = d.tugas;
        document.getElementById('e_nominal').value = d.nominal;
        document.getElementById('e_aktif').value = d.aktif;
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });
});
</script>
@endpush
