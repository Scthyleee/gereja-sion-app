@extends('layouts.app')
@section('title', 'Edit Keluarga — ' . $keluarga->nama_kepala_keluarga)
@section('content')
<div class="mb-3"><a href="{{ route('keluarga.show', $keluarga) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a></div>

{{-- Data Keluarga --}}
<form action="{{ route('keluarga.update', $keluarga) }}" method="POST">@csrf @method('PUT')
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i>Edit Data Keluarga</h6></div>
    <div class="content-card__body"><div class="row g-3">
        <div class="col-md-4"><label class="form-label">No. KK</label><input type="text" name="no_kk" class="form-control form-control-sm" value="{{ $keluarga->no_kk }}"></div>
        <div class="col-md-4"><label class="form-label">No. KK Gereja</label><input type="text" name="no_kk_gereja" class="form-control form-control-sm" value="{{ $keluarga->no_kk_gereja }}"></div>
        <div class="col-md-4"><label class="form-label">Nama KK *</label><input type="text" name="nama_kepala_keluarga" class="form-control form-control-sm" value="{{ $keluarga->nama_kepala_keluarga }}" required></div>
        <div class="col-md-4"><label class="form-label">Lingkungan</label><select name="lingkungan_sion_id" class="form-select form-select-sm"><option value="">—</option>@foreach($sionList as $s)<option value="{{ $s->id }}" @selected($keluarga->lingkungan_sion_id==$s->id)>{{ $s->nama }}</option>@endforeach</select></div>
        <div class="col-md-12"><label class="form-label">Alamat</label><input type="text" name="alamat" class="form-control form-control-sm" value="{{ $keluarga->alamat }}"></div>
        <div class="col-md-2"><label class="form-label">RT</label><input type="text" name="rt" class="form-control form-control-sm" value="{{ $keluarga->rt }}"></div>
        <div class="col-md-2"><label class="form-label">RW</label><input type="text" name="rw" class="form-control form-control-sm" value="{{ $keluarga->rw }}"></div>
        <div class="col-md-3"><label class="form-label">Kelurahan</label><input type="text" name="kelurahan" class="form-control form-control-sm" value="{{ $keluarga->kelurahan }}"></div>
        <div class="col-md-3"><label class="form-label">Kecamatan</label><input type="text" name="kecamatan" class="form-control form-control-sm" value="{{ $keluarga->kecamatan }}"></div>
        <div class="col-md-2"><label class="form-label">Kota</label><input type="text" name="kota" class="form-control form-control-sm" value="{{ $keluarga->kota }}"></div>
        <div class="col-md-3"><label class="form-label">Telepon</label><input type="text" name="no_telepon" class="form-control form-control-sm" value="{{ $keluarga->no_telepon }}"></div>
        <div class="col-md-9"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control form-control-sm" value="{{ $keluarga->keterangan }}"></div>
    </div></div>
</div>
<div class="d-flex justify-content-end mb-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Data Keluarga</button></div>
</form>

{{-- Anggota Keluarga --}}
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-users me-2"></i>Anggota Keluarga ({{ $keluarga->anggota->count() }} jiwa)</h6>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota"><i class="fas fa-plus me-1"></i>Tambah Anggota</button>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>#</th><th>Nama</th><th>Hubungan</th><th>JK</th><th>TTL</th><th>Baptis</th><th>Sidi</th><th>Pekerjaan</th><th>Telepon</th><th style="width:100px">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($keluarga->anggota as $i => $a)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $a->nama_lengkap }}</td>
                        <td><span class="badge bg-secondary">{{ $a->hubungan_keluarga ?? '-' }}</span></td>
                        <td>{{ $a->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                        <td class="small">{{ $a->tempat_lahir }}, {{ $a->tanggal_lahir?->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $a->status_baptis === 'Sudah Baptis' ? 'success' : 'warning' }}">{{ $a->status_baptis }}</span></td>
                        <td><span class="badge bg-{{ $a->status_sidi === 'Sudah Sidi' ? 'success' : 'warning' }}">{{ $a->status_sidi }}</span></td>
                        <td class="small">{{ $a->pekerjaan ?? '-' }}</td>
                        <td class="small">{{ $a->no_telepon ?? '-' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditAnggota{{ $a->id_warga }}" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                            <form action="{{ route('keluarga.anggota.destroy', [$keluarga, $a->id_warga]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus anggota ini dari keluarga?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">Belum ada anggota terdaftar</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit per Anggota --}}
@foreach($keluarga->anggota as $a)
<div class="modal fade" id="modalEditAnggota{{ $a->id_warga }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Edit {{ $a->nama_lengkap }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('keluarga.anggota.update', [$keluarga, $a->id_warga]) }}" method="POST">@csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="nama_lengkap" class="form-control" value="{{ $a->nama_lengkap }}" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Hubungan</label><select name="hubungan_keluarga" class="form-select"><option value="Kepala Keluarga" @selected($a->hubungan_keluarga=='Kepala Keluarga')>Kepala Keluarga</option><option value="Istri" @selected($a->hubungan_keluarga=='Istri')>Istri</option><option value="Anak" @selected($a->hubungan_keluarga=='Anak')>Anak</option><option value="Lainnya" @selected($a->hubungan_keluarga=='Lainnya')>Lainnya</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Jenis Kelamin *</label><select name="jenis_kelamin" class="form-select" required><option value="Laki-laki" @selected($a->jenis_kelamin=='Laki-laki')>Laki-laki</option><option value="Perempuan" @selected($a->jenis_kelamin=='Perempuan')>Perempuan</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control" value="{{ $a->tempat_lahir }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control" value="{{ $a->tanggal_lahir?->format('Y-m-d') }}"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Pekerjaan</label><input type="text" name="pekerjaan" class="form-control" value="{{ $a->pekerjaan }}"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Gol. Darah</label><select name="golongan_darah" class="form-select"><option value="">-</option>@foreach(['A','B','AB','O'] as $g)<option value="{{ $g }}" @selected($a->golongan_darah==$g)>{{ $g }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Nikah</label><select name="status_pernikahan" class="form-select"><option value="">-</option>@foreach(['Belum Menikah','Menikah','Cerai','Duda/Janda'] as $s)<option value="{{ $s }}" @selected($a->status_pernikahan==$s)>{{ $s }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Baptis</label><select name="status_baptis" class="form-select"><option value="Belum Baptis" @selected($a->status_baptis=='Belum Baptis')>Belum Baptis</option><option value="Sudah Baptis" @selected($a->status_baptis=='Sudah Baptis')>Sudah Baptis</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Sidi</label><select name="status_sidi" class="form-select"><option value="Belum Sidi" @selected($a->status_sidi=='Belum Sidi')>Belum Sidi</option><option value="Sudah Sidi" @selected($a->status_sidi=='Sudah Sidi')>Sudah Sidi</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">No. Telepon</label><input type="text" name="no_telepon" class="form-control" value="{{ $a->no_telepon }}"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Tambah Anggota Baru --}}
<div class="modal fade" id="modalTambahAnggota" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Anggota Baru</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('keluarga.anggota.store', $keluarga) }}" method="POST">@csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" name="nama_lengkap" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Hubungan</label><select name="hubungan_keluarga" class="form-select"><option>Kepala Keluarga</option><option>Istri</option><option selected>Anak</option><option>Lainnya</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Jenis Kelamin *</label><select name="jenis_kelamin" class="form-select" required><option>Laki-laki</option><option>Perempuan</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Pekerjaan</label><input type="text" name="pekerjaan" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Gol. Darah</label><select name="golongan_darah" class="form-select"><option value="">-</option><option>A</option><option>B</option><option>AB</option><option>O</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Nikah</label><select name="status_pernikahan" class="form-select"><option value="">-</option><option>Belum Menikah</option><option>Menikah</option><option>Cerai</option><option>Duda/Janda</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Baptis</label><select name="status_baptis" class="form-select"><option>Belum Baptis</option><option>Sudah Baptis</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold">Status Sidi</label><select name="status_sidi" class="form-select"><option>Belum Sidi</option><option>Sudah Sidi</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">No. Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Tambah</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
