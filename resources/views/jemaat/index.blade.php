@extends('layouts.app')
@section('title', 'Data Jemaat')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-users"></i></div><div class="stat-card__body"><div class="stat-card__label">Total Aktif</div><div class="stat-card__value">{{ $stats['total'] }}</div></div></div></div>
    <div class="col-md-3"><div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-male"></i></div><div class="stat-card__body"><div class="stat-card__label">Pria</div><div class="stat-card__value">{{ $stats['pria'] }}</div></div></div></div>
    <div class="col-md-3"><div class="stat-card stat-card--orange"><div class="stat-card__icon"><i class="fas fa-female"></i></div><div class="stat-card__body"><div class="stat-card__label">Wanita</div><div class="stat-card__value">{{ $stats['wanita'] }}</div></div></div></div>
    <div class="col-md-3"><div class="stat-card stat-card--red"><div class="stat-card__icon"><i class="fas fa-cross"></i></div><div class="stat-card__body"><div class="stat-card__label">Sudah Sidi</div><div class="stat-card__value">{{ $stats['sidi'] }}</div></div></div></div>
</div>

<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select" style="width:140px">
                            <option value="aktif" @selected($status==='aktif')>Aktif</option>
                            <option value="pindah" @selected($status==='pindah')>Pindah</option>
                            <option value="meninggal" @selected($status==='meninggal')>Meninggal</option>
                            <option value="semua" @selected($status==='semua')>Semua</option>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label small fw-semibold">Cari</label>
                        <input type="text" name="q" class="form-control" placeholder="Nama, No KK, Alamat..." value="{{ $search }}">
                    </div>
                    <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Cari</button></div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('print.daftar-jemaat', ['status' => $status]) }}" class="btn btn-outline-secondary me-1" target="_blank"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                @if(auth()->user()->canEdit('jemaat'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus me-1"></i>Tambah Jemaat</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-card__header d-flex justify-content-between"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Jemaat</h6><span class="badge bg-secondary">{{ $jemaat->total() }}</span></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>No KK</th><th>Lingkungan</th><th>JK</th><th>Jabatan</th><th>Sidi</th><th>Telepon</th><th>Status</th><th style="width:100px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($jemaat as $j)
                    <tr>
                        <td class="fw-semibold">{{ $j->nama_lengkap }}</td>
                        <td class="small">{{ $j->no_kk ?: '-' }}</td>
                        <td class="small">{{ $j->lingkungan ?: '-' }}</td>
                        <td><span class="badge bg-{{ $j->jenis_kelamin === 'Laki-laki' ? 'primary' : 'danger' }}">{{ $j->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</span></td>
                        <td>
                            @if($j->jabatan_gereja)
                            <span class="badge bg-{{ $j->jabatan_gereja === 'Diakon' ? 'info' : 'warning' }}">{{ $j->jabatan_gereja === 'Diakon' ? 'Dkn' : 'Pnt' }}</span>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="small">{{ $j->status_sidi ?: '-' }}</td>
                        <td class="small">{{ $j->no_telepon ?: '-' }}</td>
                        <td><span class="badge bg-{{ $j->status_jemaat === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($j->status_jemaat) }}</span></td>
                        <td>
                            @if(auth()->user()->canEdit('jemaat'))
                            <button class="btn btn-sm btn-outline-primary btn-edit-jemaat"
                                data-id="{{ $j->id_warga }}"
                                data-nama="{{ $j->nama_lengkap }}"
                                data-nokk="{{ $j->no_kk }}"
                                data-lingkungan="{{ $j->lingkungan }}"
                                data-jk="{{ $j->jenis_kelamin }}"
                                data-tempatlahir="{{ $j->tempat_lahir }}"
                                data-tgllahir="{{ $j->tanggal_lahir?->format('Y-m-d') }}"
                                data-sidi="{{ $j->status_sidi }}"
                                data-alamat="{{ $j->alamat }}"
                                data-telp="{{ $j->no_telepon }}"
                                data-email="{{ $j->email }}"
                                data-pekerjaan="{{ $j->pekerjaan }}"
                                data-pernikahan="{{ $j->status_pernikahan }}"
                                data-status="{{ $j->status_jemaat }}"
                                data-jabatan="{{ $j->jabatan_gereja }}"
                                data-tglgabung="{{ $j->tanggal_bergabung?->format('Y-m-d') }}"
                                data-baptis="{{ $j->status_baptis }}"
                            ><i class="fas fa-edit"></i></button>
                            <form action="{{ route('jemaat.destroy', $j) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-users fa-2x mb-2 d-block"></i>Belum ada data jemaat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jemaat->hasPages())<div class="content-card__body border-top d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3"><small class="text-muted">Showing {{ $jemaat->firstItem() }} to {{ $jemaat->lastItem() }} of {{ $jemaat->total() }} results</small>{{ $jemaat->links('pagination::bootstrap-5') }}</div>@endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Jemaat</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jemaat.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="nama_lengkap" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">No KK</label><input type="text" name="no_kk" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Lingkungan</label><select name="lingkungan" class="form-select"><option value="">Pilih Lingkungan...</option>@foreach($lingkungan as $l)<option value="{{ $l->nama }}">{{ $l->nama }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Jenis Kelamin *</label><select name="jenis_kelamin" class="form-select" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Sidi</label><select name="status_sidi" class="form-select"><option value="">-</option><option>Sudah Sidi</option><option>Belum Sidi</option></select></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Alamat</label><input type="text" name="alamat" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Pekerjaan</label><input type="text" name="pekerjaan" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Status Pernikahan</label><select name="status_pernikahan" class="form-select"><option value="">-</option><option>Belum Menikah</option><option>Menikah</option><option>Cerai</option><option>Duda/Janda</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tgl Bergabung</label><input type="date" name="tanggal_bergabung" class="form-control" value="{{ date('Y-m-d') }}"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Jemaat</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formEditJemaat" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="nama_lengkap" id="e_nama" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">No KK</label><input type="text" name="no_kk" id="e_nokk" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Lingkungan</label><select name="lingkungan" id="e_lingkungan" class="form-select"><option value="">Pilih Lingkungan...</option>@foreach($lingkungan as $l)<option value="{{ $l->nama }}">{{ $l->nama }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Jenis Kelamin *</label><select name="jenis_kelamin" id="e_jk" class="form-select" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Tempat Lahir</label><input type="text" name="tempat_lahir" id="e_tempatlahir" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Tanggal Lahir</label><input type="date" name="tanggal_lahir" id="e_tgllahir" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Sidi</label><select name="status_sidi" id="e_sidi" class="form-select"><option value="">-</option><option>Sudah Sidi</option><option>Belum Sidi</option></select></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Alamat</label><input type="text" name="alamat" id="e_alamat" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Telepon</label><input type="text" name="no_telepon" id="e_telp" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Email</label><input type="email" name="email" id="e_email" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Pekerjaan</label><input type="text" name="pekerjaan" id="e_pekerjaan" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Nikah</label><select name="status_pernikahan" id="e_pernikahan" class="form-select"><option value="">-</option><option>Belum Menikah</option><option>Menikah</option><option>Cerai</option><option>Duda/Janda</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status</label><select name="status_jemaat" id="e_status" class="form-select"><option value="aktif">Aktif</option><option value="pindah">Pindah</option><option value="meninggal">Meninggal</option></select></div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jabatan Gereja</label>
                            <select name="jabatan_gereja" id="e_jabatan" class="form-select">
                                <option value="">- Tidak Ada -</option>
                                <option value="Diakon">Diakon</option>
                                <option value="Penetua">Penetua</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status Baptis</label>
                            <select name="status_baptis" id="e_baptis" class="form-select">
                                <option value="">-</option>
                                <option value="Sudah Baptis">Sudah Baptis</option>
                                <option value="Belum Baptis">Belum Baptis</option>
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Tgl Bergabung</label><input type="date" name="tanggal_bergabung" id="e_tglgabung" class="form-control"></div>
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
document.querySelectorAll('.btn-edit-jemaat').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = this.dataset;
        document.getElementById('formEditJemaat').action = `/admin/jemaat/${d.id}`;
        document.getElementById('e_nama').value = d.nama;
        document.getElementById('e_nokk').value = d.nokk;
        document.getElementById('e_lingkungan').value = d.lingkungan;
        document.getElementById('e_jk').value = d.jk;
        document.getElementById('e_tempatlahir').value = d.tempatlahir;
        document.getElementById('e_tgllahir').value = d.tgllahir;
        document.getElementById('e_sidi').value = d.sidi;
        document.getElementById('e_alamat').value = d.alamat;
        document.getElementById('e_telp').value = d.telp;
        document.getElementById('e_email').value = d.email;
        document.getElementById('e_pekerjaan').value = d.pekerjaan;
        document.getElementById('e_pernikahan').value = d.pernikahan;
        document.getElementById('e_status').value = d.status;
        document.getElementById('e_jabatan').value = d.jabatan || '';
        document.getElementById('e_tglgabung').value = d.tglgabung;
        document.getElementById('e_baptis').value = d.baptis || '';
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });
});
</script>
@endpush
