@extends('layouts.app')
@section('title', 'Pendaftaran Baptis Baru')

@section('content')
<div class="mb-3"><a href="{{ route('baptis.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a></div>

<form action="{{ route('baptis.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-water me-2" style="color:var(--primary)"></i>Formulir Pendaftaran Baptis Kudus</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-12"><label class="form-label fw-semibold">Data dari Jemaat Existing?</label>
                <select name="warga_id" class="form-select form-select-sm" id="selWarga" onchange="fillWarga(this)">
                    <option value="">— Isi Manual —</option>
                    @foreach($wargaList as $w)<option value="{{ $w->id_warga }}" data-nama="{{ $w->nama_lengkap }}" data-ttl="{{ $w->tempat_lahir }}" data-tgl="{{ $w->tanggal_lahir?->format('Y-m-d') }}" data-jk="{{ $w->jenis_kelamin }}" data-alamat="{{ $w->alamat }}" data-hp="{{ $w->no_telepon }}" data-job="{{ $w->pekerjaan }}">{{ $w->nama_lengkap }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input type="text" name="nama_lengkap" class="form-control form-control-sm" id="fNama" required></div>
            <div class="col-md-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select form-select-sm" id="fJK"><option value="">-</option><option>Laki-laki</option><option>Perempuan</option></select></div>
            <div class="col-md-3"><label class="form-label">Lingkungan Sion</label>
                <select name="lingkungan_sion_id" class="form-select form-select-sm"><option value="">-</option>@foreach($sionList as $s)<option value="{{ $s->id }}">{{ $s->nama }}</option>@endforeach</select>
            </div>
            <div class="col-md-3"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control form-control-sm" id="fTTL"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control form-control-sm" id="fTGL"></div>
            <div class="col-md-6"><label class="form-label">Alamat</label><input type="text" name="alamat" class="form-control form-control-sm" id="fAlamat"></div>
            <div class="col-md-3"><label class="form-label">No. Telepon</label><input type="text" name="no_telepon" class="form-control form-control-sm" id="fHP"></div>
            <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan" class="form-control form-control-sm" id="fJob"></div>
            <div class="col-md-4"><label class="form-label">Nama Ayah</label><input type="text" name="nama_ayah" class="form-control form-control-sm"></div>
            <div class="col-md-4"><label class="form-label">Nama Ibu</label><input type="text" name="nama_ibu" class="form-control form-control-sm"></div>
            <div class="col-md-4"><label class="form-label">Agama Sebelumnya</label><input type="text" name="agama_sebelum" class="form-control form-control-sm" placeholder="(jika pindah agama)"></div>

            <div class="col-12"><hr><h6 class="fw-bold"><i class="fas fa-user-friends me-1"></i>Saksi Baptis</h6></div>
            <div class="col-md-6"><label class="form-label">Saksi 1</label><input type="text" name="saksi_1" class="form-control form-control-sm" placeholder="Nama lengkap saksi pertama"></div>
            <div class="col-md-6"><label class="form-label">Saksi 2</label><input type="text" name="saksi_2" class="form-control form-control-sm" placeholder="Nama lengkap saksi kedua"></div>


            <div class="col-12"><hr><h6 class="fw-bold"><i class="fas fa-money-bill me-1"></i>Biaya & Dokumen</h6></div>
            <div class="col-md-4"><label class="form-label">Rencana Tgl Baptis</label><input type="date" name="tanggal_baptis_rencana" class="form-control form-control-sm"></div>
            <div class="col-md-4"><label class="form-label">Biaya Baptis (Rp)</label><input type="number" name="biaya_baptis" class="form-control form-control-sm" min="0" step="1000" placeholder="0"></div>
            <div class="col-md-8"><label class="form-label">Scan Formulir Fisik</label><input type="file" name="scan_formulir" class="form-control form-control-sm" accept="image/*,.pdf"></div>

            <div class="col-md-12"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control form-control-sm" rows="2"></textarea></div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Pendaftaran</button></div>
</form>
@endsection

@push('scripts')
<script>
function fillWarga(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('fNama').value = opt.dataset.nama || '';
    document.getElementById('fTTL').value = opt.dataset.ttl || '';
    document.getElementById('fTGL').value = opt.dataset.tgl || '';
    document.getElementById('fJK').value = opt.dataset.jk || '';
    document.getElementById('fAlamat').value = opt.dataset.alamat || '';
    document.getElementById('fHP').value = opt.dataset.hp || '';
    document.getElementById('fJob').value = opt.dataset.job || '';
}
</script>
@endpush
