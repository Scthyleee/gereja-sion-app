@extends('layouts.app')
@section('title', 'Daftarkan Keluarga Baru')

@section('content')
<div class="mb-3"><a href="{{ route('keluarga.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a></div>

<form action="{{ route('keluarga.store') }}" method="POST" id="formKeluarga">
@csrf

{{-- Header Keluarga --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-home me-2" style="color:var(--primary)"></i>Data Kepala Keluarga</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">No. KK Negara</label><input type="text" name="no_kk" class="form-control form-control-sm" placeholder="16 digit"></div>
            <div class="col-md-3"><label class="form-label">No. KK Gereja</label><input type="text" name="no_kk_gereja" class="form-control form-control-sm" placeholder="Nomor KK Gereja"></div>
            <div class="col-md-4"><label class="form-label">Nama Kepala Keluarga <span class="text-danger">*</span></label><input type="text" name="nama_kepala_keluarga" class="form-control form-control-sm" required></div>
            <div class="col-md-4"><label class="form-label">Lingkungan Sion</label>
                <select name="lingkungan_sion_id" class="form-select form-select-sm">
                    <option value="">— Pilih —</option>
                    @foreach($sionList as $s)<option value="{{ $s->id }}">{{ $s->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-12"><label class="form-label">Alamat</label><input type="text" name="alamat" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="form-label">RT</label><input type="text" name="rt" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="form-label">RW</label><input type="text" name="rw" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="form-label">Kelurahan</label><input type="text" name="kelurahan" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="form-label">Kecamatan</label><input type="text" name="kecamatan" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="form-label">Kota</label><input type="text" name="kota" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="form-label">No. Telepon</label><input type="text" name="no_telepon" class="form-control form-control-sm"></div>
            <div class="col-md-9"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control form-control-sm"></div>
        </div>
    </div>
</div>

{{-- Anggota Keluarga --}}
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-users me-2"></i>Anggota Keluarga</h6>
        <button type="button" class="btn btn-sm btn-success" onclick="addAnggota()"><i class="fas fa-plus me-1"></i>Tambah Anggota</button>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tblAnggota">
                <thead>
                    <tr>
                        <th>Nama Lengkap *</th><th style="width:110px">Hub. Keluarga</th><th style="width:100px">JK *</th>
                        <th style="width:110px">Tempat Lahir</th><th style="width:110px">Tgl Lahir</th>
                        <th style="width:100px">Pekerjaan</th><th style="width:90px">Status Baptis</th><th style="width:90px">Status Sidi</th><th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="anggotaBody">
                    <tr>
                        <td><input type="text" name="anggota[0][nama_lengkap]" class="form-control form-control-sm" required></td>
                        <td><select name="anggota[0][hubungan_keluarga]" class="form-select form-select-sm"><option>Kepala Keluarga</option><option>Istri</option><option>Anak</option><option>Lainnya</option></select></td>
                        <td><select name="anggota[0][jenis_kelamin]" class="form-select form-select-sm" required><option>Laki-laki</option><option>Perempuan</option></select></td>
                        <td><input type="text" name="anggota[0][tempat_lahir]" class="form-control form-control-sm"></td>
                        <td><input type="date" name="anggota[0][tanggal_lahir]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="anggota[0][pekerjaan]" class="form-control form-control-sm"></td>
                        <td><select name="anggota[0][status_baptis]" class="form-select form-select-sm"><option>Belum Baptis</option><option>Sudah Baptis</option></select></td>
                        <td><select name="anggota[0][status_sidi]" class="form-select form-select-sm"><option>Belum Sidi</option><option>Sudah Sidi</option></select></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Keluarga</button>
</div>
</form>
@endsection

@push('scripts')
<script>
let idx = 1;
function addAnggota() {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="anggota[${idx}][nama_lengkap]" class="form-control form-control-sm" required></td>
        <td><select name="anggota[${idx}][hubungan_keluarga]" class="form-select form-select-sm"><option>Kepala Keluarga</option><option>Istri</option><option>Anak</option><option>Lainnya</option></select></td>
        <td><select name="anggota[${idx}][jenis_kelamin]" class="form-select form-select-sm" required><option>Laki-laki</option><option>Perempuan</option></select></td>
        <td><input type="text" name="anggota[${idx}][tempat_lahir]" class="form-control form-control-sm"></td>
        <td><input type="date" name="anggota[${idx}][tanggal_lahir]" class="form-control form-control-sm"></td>
        <td><input type="text" name="anggota[${idx}][pekerjaan]" class="form-control form-control-sm"></td>
        <td><select name="anggota[${idx}][status_baptis]" class="form-select form-select-sm"><option>Belum Baptis</option><option>Sudah Baptis</option></select></td>
        <td><select name="anggota[${idx}][status_sidi]" class="form-select form-select-sm"><option>Belum Sidi</option><option>Sudah Sidi</option></select></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
    `;
    document.getElementById('anggotaBody').appendChild(row);
    idx++;
}
</script>
@endpush
