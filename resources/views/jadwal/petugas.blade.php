@extends('layouts.app')
@section('title', 'Master Petugas')

@section('content')
{{-- Tabs --}}
<ul class="nav nav-pills mb-4" style="gap:8px">
    <li class="nav-item"><a href="{{ route('jadwal.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-calendar-alt me-1"></i>Jadwal</a></li>
    <li class="nav-item"><a href="{{ route('insentif.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-money-bill me-1"></i>Insentif</a></li>
    <li class="nav-item"><a href="{{ route('petugas.index') }}" class="nav-link active" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none"><i class="fas fa-users-cog me-1"></i>Petugas</a></li>
</ul>

<div class="row g-4">
{{-- Daftar Petugas --}}
<div class="col-lg-8">
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Daftar Petugas</h6>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width:220px">
                <span class="input-group-text" style="background:var(--bg);border-color:var(--border);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                <input type="text" id="searchPetugas" class="form-control form-control-sm" placeholder="Cari nama atau job..." autocomplete="off" style="background:var(--bg);border-color:var(--border);color:var(--text)">
            </div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus me-1"></i>Tambah</button>
        </div>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>Job / Tugas</th><th>Status</th><th style="width:120px">Aksi</th></tr></thead>
                <tbody id="petugasTableBody">
                    @forelse($petugas as $p)
                    @php $jobsStr = $p->roles->pluck('jenis_tugas')->join(' '); @endphp
                    <tr class="petugas-row" data-nama="{{ strtolower($p->nama_petugas) }}" data-jobs="{{ strtolower($jobsStr) }}">
                        <td class="fw-semibold">{{ $p->nama_petugas }}</td>
                        <td>
                            @forelse($p->roles as $role)
                                <span class="badge bg-info me-1" style="font-size:.75rem">{{ $role->jenis_tugas }}</span>
                            @empty
                                <span class="text-muted small">- Belum ada job -</span>
                            @endforelse
                        </td>
                        <td><span class="badge bg-{{ $p->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($p->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary btn-detail"
                                    data-id="{{ $p->id_petugas }}"
                                    data-nama="{{ $p->nama_petugas }}"
                                    data-telepon="{{ $p->no_telepon }}"
                                    data-status="{{ $p->status }}"
                                    data-pendeta="{{ $p->is_pendeta_sion }}"
                                    data-roles='@json($p->roles->pluck("jenis_tugas"))'
                                ><i class="fas fa-eye me-1"></i>Detail</button>
                                <form action="{{ route('petugas.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus petugas ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada petugas</td></tr>
                    @endforelse
                    <tr id="noResultRow" style="display:none"><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>Tidak ada petugas yang cocok</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

{{-- Jenis Tugas Management --}}
<div class="col-lg-4">
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Daftar Jenis Tugas</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalJenisTugas"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead><tr><th>Jenis Tugas</th><th>Tipe Event</th><th>Nominal</th><th style="width:40px"></th></tr></thead>
                <tbody>
                    @forelse($insentifConfigs as $ic)
                    <tr>
                        <td class="fw-semibold small">{{ $ic->jenis_tugas }}</td>
                        <td><span class="badge bg-secondary" style="font-size:.65rem">{{ ucwords(str_replace('_', ' ', $ic->tipe_event)) }}</span></td>
                        <td class="small">Rp {{ number_format($ic->nominal, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('insentif.config.destroy', $ic) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3 small">Belum ada jenis tugas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

{{-- Modal Tambah Petugas --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Petugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('petugas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Nama *</label><input type="text" name="nama_petugas" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Job / Tugas Pertama</label>
                        <select name="jenis_tugas" class="form-select">
                            <option value="">-- Tambah nanti --</option>
                            @foreach($jenisTugasList as $jt)
                                <option>{{ $jt }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih dari daftar jenis tugas, atau bisa ditambah nanti di Detail.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label fw-semibold">No Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
                        <div class="col-6"><label class="form-label fw-semibold">Link Warga</label>
                            <select name="warga_id" class="form-select select-search"><option value="">Tidak di-link</option>
                                @foreach($wargaList as $w)<option value="{{ $w->id_warga }}">{{ $w->nama_lengkap }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input type="checkbox" name="is_pendeta_sion" value="1" class="form-check-input" id="chkPendeta">
                        <label for="chkPendeta" class="form-check-label">Pendeta Sion (tanpa insentif)</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Petugas --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title fw-bold"><i class="fas fa-user me-2"></i>Detail Petugas</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Info --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Nama Petugas</label>
                    <div class="fw-bold fs-5" id="detNama"></div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted">Telepon</label>
                        <div id="detTelepon" class="small">-</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted">Status</label>
                        <div id="detStatus"></div>
                    </div>
                </div>

                {{-- Current Roles --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Jenis Tugas Saat Ini</label>
                    <div id="detRoles" class="d-flex flex-wrap gap-2"></div>
                </div>

                <hr style="border-color:var(--border)">

                {{-- Add Role --}}
                <label class="form-label fw-semibold"><i class="fas fa-plus-circle me-1"></i>Tambah Jenis Tugas</label>
                <form id="formAddRole" method="POST" class="mb-2">
                    @csrf
                    <div class="d-flex gap-2 flex-wrap">
                        {{-- Predefined list --}}
                        @foreach($insentifConfigs as $ic)
                        <button type="button" class="btn btn-sm btn-outline-info btn-add-role" data-tugas="{{ $ic->jenis_tugas }}">
                            <i class="fas fa-plus me-1"></i>{{ $ic->jenis_tugas }}
                        </button>
                        @endforeach
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <input type="text" name="jenis_tugas" class="form-control form-control-sm" placeholder="Atau ketik jenis tugas baru..." id="inputRoleBaru">
                        <button type="submit" class="btn btn-sm btn-success" style="white-space:nowrap"><i class="fas fa-plus me-1"></i>Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Jenis Tugas --}}
<div class="modal fade" id="modalJenisTugas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Jenis Tugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('insentif.config.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas *</label>
                        <input type="text" name="jenis_tugas" class="form-control" required placeholder="Contoh: Song Leader, Pianis, Operator LCD...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Event</label>
                        <select name="tipe_event" class="form-select">
                            <option value="ibadah_biasa">Ibadah Biasa (Minggu)</option>
                            <option value="perayaan">Perayaan (Natal, Paskah, dll)</option>
                            <option value="pernikahan">Pemberkatan Pernikahan</option>
                            <option value="duka">Ibadah Duka/Penghiburan</option>
                            <option value="keluarga">Ibadah Keluarga</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Insentif (Rp)</label>
                        <input type="number" name="nominal" class="form-control" min="0" step="1000" value="0" placeholder="100000">
                        <small class="text-muted">Nominal default untuk jenis tugas ini. Bisa 0 jika tidak ada insentif.</small>
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
// Detail Modal
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = this.dataset;
        document.getElementById('detNama').textContent = d.nama;
        document.getElementById('detTelepon').textContent = d.telepon || '-';
        document.getElementById('detStatus').innerHTML = d.status === 'active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">' + d.status + '</span>';

        // Show current roles with delete buttons
        const rolesDiv = document.getElementById('detRoles');
        rolesDiv.innerHTML = '';
        const roles = JSON.parse(d.roles);
        if (roles.length === 0) {
            rolesDiv.innerHTML = '<span class="text-muted small">Belum ada jenis tugas</span>';
        }
        roles.forEach(role => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-info d-inline-flex align-items-center gap-1';
            badge.style.fontSize = '.8rem';
            badge.innerHTML = role + ' <i class="fas fa-times" style="cursor:pointer;opacity:.7" onclick="removeRoleByName(\'' + d.id + '\', \'' + role.replace(/'/g, "\\'") + '\')"></i>';
            rolesDiv.appendChild(badge);
        });

        // Update form action
        document.getElementById('formAddRole').action = `/admin/jadwal-petugas/${d.id}/role`;

        // Hide buttons for roles already assigned
        document.querySelectorAll('.btn-add-role').forEach(b => {
            b.style.display = roles.includes(b.dataset.tugas) ? 'none' : '';
        });

        new bootstrap.Modal(document.getElementById('modalDetail')).show();
    });
});

// Quick add role from predefined button
document.querySelectorAll('.btn-add-role').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('inputRoleBaru').value = this.dataset.tugas;
        document.getElementById('formAddRole').submit();
    });
});

// Delete role by name — find the PetugasRole by name and delete
function removeRoleByName(petugasId, roleName) {
    if (!confirm(`Hapus tugas "${roleName}" dari petugas ini?`)) return;
    // Find the role ID by re-checking data
    // We use a simple form post approach
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/jadwal-petugas/${petugasId}/role-delete-by-name`;
    form.innerHTML = `@csrf @method('DELETE') <input type="hidden" name="jenis_tugas" value="${roleName}">`;
    document.body.appendChild(form);
    form.submit();
}

// Live search filter for petugas table
const searchInput = document.getElementById('searchPetugas');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#petugasTableBody tr.petugas-row');
        const noResult = document.getElementById('noResultRow');
        let visibleCount = 0;

        rows.forEach(row => {
            const nama = (row.dataset.nama || '').toLowerCase();
            const jobs = (row.dataset.jobs || '').toLowerCase();
            const match = !keyword || nama.includes(keyword) || jobs.includes(keyword);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        if (noResult) noResult.style.display = visibleCount === 0 && keyword ? '' : 'none';
    });

    // Clear search on Escape
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
        }
    });
}
</script>
@endpush
