@extends('layouts.app')
@section('title', 'Detail Nikah — ' . $nikah->pria_nama . ' & ' . $nikah->wanita_nama)
@section('content')
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('nikah.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    @php $colors = ['pending'=>'warning','terdaftar'=>'info','dijadwalkan'=>'secondary','divalidasi'=>'success','selesai'=>'success','ditolak'=>'danger']; @endphp
    <span class="badge bg-{{ $colors[$nikah->status] ?? 'secondary' }} fs-6 d-flex align-items-center">{{ ucfirst($nikah->status) }}</span>
    <a href="{{ route('print.surat-nikah', $nikah) }}" class="btn btn-sm btn-outline-primary ms-auto" target="_blank"><i class="fas fa-print me-1"></i>Cetak Surat Nikah</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="content-card h-100"><div class="content-card__header"><h6 class="mb-0"><i class="fas fa-male me-2" style="color:#4a90d9"></i>Mempelai Pria</h6></div>
        <div class="content-card__body"><div class="row g-2">
            <div class="col-12 fw-bold fs-5">{{ $nikah->pria_nama }}</div>
            <div class="col-6"><small class="text-muted">TTL</small><div>{{ $nikah->pria_tempat_lahir }}, {{ $nikah->pria_tanggal_lahir?->format('d/m/Y') }}</div></div>
            <div class="col-6"><small class="text-muted">HP</small><div>{{ $nikah->pria_no_telepon ?? '-' }}</div></div>
            <div class="col-12"><small class="text-muted">Alamat</small><div>{{ $nikah->pria_alamat ?? '-' }}</div></div>
            <div class="col-6"><small class="text-muted">Ayah</small><div>{{ $nikah->pria_nama_ayah ?? '-' }}</div></div>
            <div class="col-6"><small class="text-muted">Ibu</small><div>{{ $nikah->pria_nama_ibu ?? '-' }}</div></div>
        </div></div></div>
    </div>
    <div class="col-md-6">
        <div class="content-card h-100"><div class="content-card__header"><h6 class="mb-0"><i class="fas fa-female me-2" style="color:#e8609a"></i>Mempelai Wanita</h6></div>
        <div class="content-card__body"><div class="row g-2">
            <div class="col-12 fw-bold fs-5">{{ $nikah->wanita_nama }}</div>
            <div class="col-6"><small class="text-muted">TTL</small><div>{{ $nikah->wanita_tempat_lahir }}, {{ $nikah->wanita_tanggal_lahir?->format('d/m/Y') }}</div></div>
            <div class="col-6"><small class="text-muted">HP</small><div>{{ $nikah->wanita_no_telepon ?? '-' }}</div></div>
            <div class="col-12"><small class="text-muted">Alamat</small><div>{{ $nikah->wanita_alamat ?? '-' }}</div></div>
            <div class="col-6"><small class="text-muted">Ayah</small><div>{{ $nikah->wanita_nama_ayah ?? '-' }}</div></div>
            <div class="col-6"><small class="text-muted">Ibu</small><div>{{ $nikah->wanita_nama_ibu ?? '-' }}</div></div>
        </div></div></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="content-card h-100"><div class="content-card__header"><h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Biaya</h6></div>
        <div class="content-card__body">
            <div class="d-flex justify-content-between mb-2"><span>Administrasi</span><span class="fw-bold">Rp {{ number_format($nikah->biaya_administrasi,0,',','.') }}</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Fasilitas</span><span class="fw-bold">Rp {{ number_format($nikah->biaya_fasilitas,0,',','.') }}</span></div>
            <hr><div class="d-flex justify-content-between"><span class="fw-bold">Total Biaya</span><span class="fw-bold text-success">Rp {{ number_format($nikah->total_biaya,0,',','.') }}</span></div>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="content-card h-100"><div class="content-card__header d-flex justify-content-between"><h6 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Petugas & Insentif</h6>
            @if(!in_array($nikah->status, ['selesai','ditolak']))
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#formTambahInsentif"><i class="fas fa-plus"></i></button>
            @endif
        </div>
        <div class="content-card__body">
            {{-- Inline Add Form --}}
            <div class="collapse mb-3" id="formTambahInsentif">
                <form action="{{ route('nikah.insentif.store', $nikah) }}" method="POST" class="row g-2 align-items-end">@csrf
                    <div class="col-5"><label class="form-label small">Jenis Petugas</label>
                        <select name="jenis_insentif" class="form-select form-select-sm">
                            <option>Pendeta</option><option>Pianis</option><option>Song Leader</option>
                            <option>Operator LCD</option><option>Sound System</option><option>Kebersihan</option>
                            <option>Pendeta (Luar)</option><option>Organis</option><option>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-4"><label class="form-label small">Nominal (Rp)</label><input type="number" name="nominal" class="form-control form-control-sm" value="0" min="0"></div>
                    <div class="col-3"><button class="btn btn-sm btn-primary w-100"><i class="fas fa-plus me-1"></i>Tambah</button></div>
                </form>
            </div>
            @forelse($nikah->insentif as $ins)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>{{ $ins->jenis_insentif }}</span>
                <span class="d-flex align-items-center gap-2">
                    <span class="fw-bold">Rp {{ number_format($ins->nominal,0,',','.') }}</span>
                    @if(!in_array($nikah->status, ['selesai','ditolak']))
                    <form action="{{ route('nikah.insentif.destroy', [$nikah, $ins]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" style="padding:1px 5px"><i class="fas fa-times" style="font-size:.7rem"></i></button>
                    </form>
                    @endif
                </span>
            </div>
            @empty
            <p class="text-muted small">Belum ada petugas/insentif</p>
            @endforelse
            <hr><div class="d-flex justify-content-between"><span class="fw-bold">Total Insentif</span><span class="fw-bold text-success">Rp {{ number_format($nikah->total_insentif,0,',','.') }}</span></div>
        </div></div>
    </div>
</div>

{{-- Upload Lampiran --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Lampiran</h6></div>
    <div class="content-card__body">
        @if($nikah->lampiran->count())
        <ul class="list-group list-group-flush mb-3">
            @foreach($nikah->lampiran as $l)
            <li class="list-group-item d-flex justify-content-between" style="background:transparent;border-color:var(--border)">
                <span><i class="fas fa-file me-2"></i>{{ $l->jenis_lampiran }} — {{ $l->keterangan ?? '' }}</span>
                <span class="badge bg-success">Uploaded</span>
            </li>
            @endforeach
        </ul>
        @endif
        <form action="{{ route('nikah.lampiran', $nikah) }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">@csrf
            <div class="col-md-3"><label class="form-label small">Jenis</label>
                <select name="jenis_lampiran" class="form-select form-select-sm"><option>KTP</option><option>KK</option><option>Surat Baptis</option><option>Foto</option><option>Halaman 2</option><option>Halaman 3</option><option>Lainnya</option></select>
            </div>
            <div class="col-md-4"><label class="form-label small">File</label><input type="file" name="file" class="form-control form-control-sm" required></div>
            <div class="col-md-3"><label class="form-label small">Keterangan</label><input type="text" name="keterangan" class="form-control form-control-sm"></div>
            <div class="col-md-2"><button class="btn btn-sm btn-primary"><i class="fas fa-upload me-1"></i>Upload</button></div>
        </form>
    </div>
</div>

{{-- Actions --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Aksi</h6></div>
    <div class="content-card__body d-flex flex-wrap gap-2">
        @if($nikah->status === 'pending')
        <form action="{{ route('nikah.status', $nikah) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="terdaftar"><button class="btn btn-sm btn-success" onclick="return confirm('Setujui?')"><i class="fas fa-check me-1"></i>Setujui</button></form>
        <form action="{{ route('nikah.status', $nikah) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="ditolak"><button class="btn btn-sm btn-danger" onclick="return confirm('Tolak?')"><i class="fas fa-times me-1"></i>Tolak</button></form>
        @endif
        @if(in_array($nikah->status, ['terdaftar','dijadwalkan']))
        <form action="{{ route('nikah.validate', $nikah) }}" method="POST">@csrf<button class="btn btn-sm btn-success" onclick="return confirm('Validasi? Biaya akan masuk ke kode akun dan insentif akan dibuat otomatis.')"><i class="fas fa-check-double me-1"></i>Validasi & Proses Keuangan</button></form>
        @endif
        @if($nikah->status === 'divalidasi')
        <form action="{{ route('nikah.status', $nikah) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="selesai"><button class="btn btn-sm btn-primary" onclick="return confirm('Tandai selesai?')"><i class="fas fa-flag-checkered me-1"></i>Selesai</button></form>
        @endif
        @if($nikah->divalidasi_oleh)<div class="mt-2 small text-muted"><i class="fas fa-check-circle text-success me-1"></i>Divalidasi oleh {{ $nikah->validator?->nama }} pada {{ $nikah->divalidasi_at?->format('d/m/Y H:i') }}</div>@endif
        @if($nikah->jadwal)<div class="mt-1 small text-muted"><i class="fas fa-calendar text-primary me-1"></i>Jadwal: {{ $nikah->jadwal->jenis_ibadah }} — {{ $nikah->jadwal->tanggal->format('d/m/Y') }}</div>@endif
    </div>
</div>
@endsection
