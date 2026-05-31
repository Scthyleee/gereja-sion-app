@extends('layouts.app')
@section('title', 'Pendaftaran Nikah Baru')
@section('content')
<div class="mb-3"><a href="{{ route('nikah.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a></div>
<form action="{{ route('nikah.store') }}" method="POST" enctype="multipart/form-data">@csrf
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-male me-2" style="color:#4a90d9"></i>Calon Mempelai Pria</h6></div>
    <div class="content-card__body"><div class="row g-3">
        <div class="col-md-12"><select name="pria_warga_id" class="form-select form-select-sm" onchange="fillM(this,'pria')"><option value="">— Isi Manual —</option>@foreach($wargaList->where('jenis_kelamin','Laki-laki') as $w)<option value="{{ $w->id_warga }}" data-n="{{ $w->nama_lengkap }}" data-t="{{ $w->tempat_lahir }}" data-d="{{ $w->tanggal_lahir?->format('Y-m-d') }}" data-a="{{ $w->alamat }}" data-h="{{ $w->no_telepon }}" data-j="{{ $w->pekerjaan }}">{{ $w->nama_lengkap }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Nama *</label><input type="text" name="pria_nama" id="pria_n" class="form-control form-control-sm" required></div>
        <div class="col-md-3"><label class="form-label">Tempat Lahir</label><input type="text" name="pria_tempat_lahir" id="pria_t" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label">Tgl Lahir</label><input type="date" name="pria_tanggal_lahir" id="pria_d" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label class="form-label">Sidi</label><select name="pria_status_sidi" class="form-select form-select-sm"><option>Sudah Sidi</option><option>Belum Sidi</option></select></div>
        <div class="col-md-6"><label class="form-label">Alamat</label><input type="text" name="pria_alamat" id="pria_a" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pria_pekerjaan" id="pria_j" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label">HP</label><input type="text" name="pria_no_telepon" id="pria_h" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Ayah</label><input type="text" name="pria_nama_ayah" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Ibu</label><input type="text" name="pria_nama_ibu" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Agama</label><input type="text" name="pria_agama" class="form-control form-control-sm" value="Kristen Protestan"></div>
    </div></div>
</div>
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-female me-2" style="color:#e8609a"></i>Calon Mempelai Wanita</h6></div>
    <div class="content-card__body"><div class="row g-3">
        <div class="col-md-12"><select name="wanita_warga_id" class="form-select form-select-sm" onchange="fillM(this,'wanita')"><option value="">— Isi Manual —</option>@foreach($wargaList->where('jenis_kelamin','Perempuan') as $w)<option value="{{ $w->id_warga }}" data-n="{{ $w->nama_lengkap }}" data-t="{{ $w->tempat_lahir }}" data-d="{{ $w->tanggal_lahir?->format('Y-m-d') }}" data-a="{{ $w->alamat }}" data-h="{{ $w->no_telepon }}" data-j="{{ $w->pekerjaan }}">{{ $w->nama_lengkap }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Nama *</label><input type="text" name="wanita_nama" id="wanita_n" class="form-control form-control-sm" required></div>
        <div class="col-md-3"><label class="form-label">Tempat Lahir</label><input type="text" name="wanita_tempat_lahir" id="wanita_t" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label">Tgl Lahir</label><input type="date" name="wanita_tanggal_lahir" id="wanita_d" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label class="form-label">Sidi</label><select name="wanita_status_sidi" class="form-select form-select-sm"><option>Sudah Sidi</option><option>Belum Sidi</option></select></div>
        <div class="col-md-6"><label class="form-label">Alamat</label><input type="text" name="wanita_alamat" id="wanita_a" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="wanita_pekerjaan" id="wanita_j" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label">HP</label><input type="text" name="wanita_no_telepon" id="wanita_h" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Ayah</label><input type="text" name="wanita_nama_ayah" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Ibu</label><input type="text" name="wanita_nama_ibu" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Agama</label><input type="text" name="wanita_agama" class="form-control form-control-sm" value="Kristen Protestan"></div>
    </div></div>
</div>
{{-- Saksi-saksi --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-user-friends me-2" style="color:var(--accent)"></i>Saksi-Saksi Pernikahan</h6></div>
    <div class="content-card__body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label">Saksi Pihak Pria 1</label><input type="text" name="saksi_pria_1" class="form-control form-control-sm"></div>
        <div class="col-md-6"><label class="form-label">Saksi Pihak Pria 2</label><input type="text" name="saksi_pria_2" class="form-control form-control-sm"></div>
        <div class="col-md-6"><label class="form-label">Saksi Pihak Wanita 1</label><input type="text" name="saksi_wanita_1" class="form-control form-control-sm"></div>
        <div class="col-md-6"><label class="form-label">Saksi Pihak Wanita 2</label><input type="text" name="saksi_wanita_2" class="form-control form-control-sm"></div>
    </div></div>
</div>
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-money-bill me-2" style="color:var(--primary)"></i>Biaya, Jadwal & Dokumen</h6></div>
    <div class="content-card__body"><div class="row g-3">
        <div class="col-md-3"><label class="form-label">Biaya Admin (Rp)</label><input type="number" name="biaya_administrasi" class="form-control form-control-sm" value="{{ $defaults['biaya_administrasi'] }}"></div>
        <div class="col-md-3"><label class="form-label">Akun Admin</label><select name="kode_akun_admin_id" class="form-select form-select-sm"><option value="">-</option>@foreach($akunPenerimaan as $a)<option value="{{ $a->id }}">{{ $a->kode }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Biaya Fasilitas (Rp)</label><input type="number" name="biaya_fasilitas" class="form-control form-control-sm" value="{{ $defaults['biaya_fasilitas'] }}"></div>
        <div class="col-md-3"><label class="form-label">Akun Fasilitas</label><select name="kode_akun_fasilitas_id" class="form-select form-select-sm"><option value="">-</option>@foreach($akunPenerimaan as $a)<option value="{{ $a->id }}">{{ $a->kode }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Tgl Nikah (Rencana)</label><input type="date" name="tanggal_nikah_rencana" class="form-control form-control-sm"></div>
        <div class="col-md-5"><label class="form-label">Catatan</label><input type="text" name="catatan" class="form-control form-control-sm"></div>
        <div class="col-md-4"><label class="form-label">Scan Formulir Fisik</label><input type="file" name="scan_formulir" class="form-control form-control-sm" accept="image/*,.pdf"></div>
    </div></div>
</div>
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between align-items-center"><h6 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Insentif</h6><button type="button" class="btn btn-sm btn-success" onclick="addIns()"><i class="fas fa-plus me-1"></i>Tambah</button></div>
    <div class="content-card__body p-0"><table class="table table-hover mb-0"><thead><tr><th>Jenis</th><th style="width:180px" class="text-end">Nominal (Rp)</th><th style="width:40px"></th></tr></thead><tbody id="insBody">
        <tr><td><input type="text" name="insentif_jenis[]" class="form-control form-control-sm" value="Pianis"></td><td><input type="number" name="insentif_nominal[]" class="form-control form-control-sm text-end" value="{{ $defaults['insentif_pianis'] }}"></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td></tr>
        <tr><td><input type="text" name="insentif_jenis[]" class="form-control form-control-sm" value="LCD"></td><td><input type="number" name="insentif_nominal[]" class="form-control form-control-sm text-end" value="{{ $defaults['insentif_lcd'] }}"></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td></tr>
        <tr><td><input type="text" name="insentif_jenis[]" class="form-control form-control-sm" value="Sound System"></td><td><input type="number" name="insentif_nominal[]" class="form-control form-control-sm text-end" value="{{ $defaults['insentif_sound'] }}"></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td></tr>
        <tr><td><input type="text" name="insentif_jenis[]" class="form-control form-control-sm" value="Kebersihan"></td><td><input type="number" name="insentif_nominal[]" class="form-control form-control-sm text-end" value="{{ $defaults['insentif_kebersihan'] }}"></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td></tr>
    </tbody></table></div>
</div>
<div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
</form>
@endsection
@push('scripts')
<script>
function fillM(s,p){const o=s.options[s.selectedIndex];if(!o.value)return;['n','t','d','a','h','j'].forEach(k=>{const el=document.getElementById(p+'_'+k);if(el)el.value=o.dataset[k]||'';})}
function addIns(){const r=document.createElement('tr');r.innerHTML='<td><input type="text" name="insentif_jenis[]" class="form-control form-control-sm"></td><td><input type="number" name="insentif_nominal[]" class="form-control form-control-sm text-end" value="0"></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest(\'tr\').remove()"><i class="fas fa-times"></i></button></td>';document.getElementById('insBody').appendChild(r)}
</script>
@endpush
