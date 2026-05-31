@extends('layouts.app')
@section('title', 'Detail Baptis — ' . $bapti->nama_lengkap)

@section('content')
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('baptis.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    @php $colors = ['pending'=>'warning','terdaftar'=>'info','lulus_katekisasi'=>'primary','dijadwalkan'=>'secondary','selesai'=>'success','ditolak'=>'danger']; @endphp
    <span class="badge bg-{{ $colors[$bapti->status] ?? 'secondary' }} fs-6 d-flex align-items-center">{{ ucfirst(str_replace('_',' ',$bapti->status)) }}</span>
    <a href="{{ route('print.surat-baptis', $bapti) }}" class="btn btn-sm btn-outline-primary ms-auto" target="_blank"><i class="fas fa-print me-1"></i>Cetak Surat Baptis</a>
</div>

{{-- Data Calon Baptis --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-water me-2" style="color:var(--primary)"></i>{{ $bapti->nama_lengkap }}</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-3"><small class="text-muted">Jenis Kelamin</small><div>{{ $bapti->jenis_kelamin ?? '-' }}</div></div>
            <div class="col-md-3"><small class="text-muted">TTL</small><div>{{ $bapti->tempat_lahir }}, {{ $bapti->tanggal_lahir?->format('d/m/Y') }}</div></div>
            <div class="col-md-3"><small class="text-muted">Lingkungan</small><div>{{ $bapti->lingkunganSion?->nama ?? '-' }}</div></div>
            <div class="col-md-3"><small class="text-muted">No. HP</small><div>{{ $bapti->no_telepon ?? '-' }}</div></div>
            <div class="col-md-6"><small class="text-muted">Alamat</small><div>{{ $bapti->alamat ?? '-' }}</div></div>
            <div class="col-md-3"><small class="text-muted">Nama Ayah</small><div>{{ $bapti->nama_ayah ?? '-' }}</div></div>
            <div class="col-md-3"><small class="text-muted">Nama Ibu</small><div>{{ $bapti->nama_ibu ?? '-' }}</div></div>
            @if($bapti->agama_sebelum)<div class="col-md-3"><small class="text-muted">Agama Sebelumnya</small><div>{{ $bapti->agama_sebelum }}</div></div>@endif
        </div>

        @if($bapti->katekisasi_mulai)
        <hr>
        <h6 class="fw-bold"><i class="fas fa-graduation-cap me-1"></i>Katekisasi</h6>
        <div class="row g-3">
            <div class="col-md-3"><small class="text-muted">Mulai</small><div>{{ $bapti->katekisasi_mulai?->format('d/m/Y') }}</div></div>
            <div class="col-md-3"><small class="text-muted">Selesai</small><div>{{ $bapti->katekisasi_selesai?->format('d/m/Y') ?? '-' }}</div></div>
            <div class="col-md-3"><small class="text-muted">Nilai Ujian</small><div class="fw-bold">{{ $bapti->nilai_ujian ?? '-' }}</div></div>
            <div class="col-md-3"><small class="text-muted">Rencana Baptis</small><div>{{ $bapti->tanggal_baptis_rencana?->format('d/m/Y') ?? '-' }}</div></div>
        </div>
        @endif
    </div>
</div>

{{-- Workflow Actions --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Aksi</h6></div>
    <div class="content-card__body">
        <div class="d-flex flex-wrap gap-2">
            @if($bapti->status === 'pending')
            <form action="{{ route('baptis.status', $bapti) }}" method="POST">@csrf @method('PUT')
                <input type="hidden" name="status" value="terdaftar">
                <button class="btn btn-sm btn-success" onclick="return confirm('Setujui pendaftaran?')"><i class="fas fa-check me-1"></i>Setujui</button>
            </form>
            <form action="{{ route('baptis.status', $bapti) }}" method="POST">@csrf @method('PUT')
                <input type="hidden" name="status" value="ditolak">
                <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak pendaftaran?')"><i class="fas fa-times me-1"></i>Tolak</button>
            </form>
            @endif

            @if($bapti->status === 'terdaftar')
            <form action="{{ route('baptis.status', $bapti) }}" method="POST" class="d-flex gap-2 align-items-end">@csrf @method('PUT')
                <input type="hidden" name="status" value="dijadwalkan">
                <div><label class="form-label small">Tanggal Baptis</label><input type="date" name="tanggal_baptis" class="form-control form-control-sm" required></div>
                <button class="btn btn-sm btn-primary"><i class="fas fa-calendar me-1"></i>Jadwalkan Baptis</button>
            </form>
            @endif

            @if($bapti->status === 'dijadwalkan')
            <form action="{{ route('baptis.status', $bapti) }}" method="POST">@csrf @method('PUT')
                <input type="hidden" name="status" value="selesai">
                <button class="btn btn-sm btn-success" onclick="return confirm('Tandai baptisan selesai? Status jemaat akan otomatis diupdate.')"><i class="fas fa-check-double me-1"></i>Selesai — Tandai Sudah Baptis</button>
            </form>
            @endif
        </div>

        @if($bapti->disetujui_oleh)
        <div class="mt-3 small text-muted"><i class="fas fa-check-circle me-1 text-success"></i>Disetujui oleh {{ $bapti->approver?->nama }} pada {{ $bapti->disetujui_at?->format('d/m/Y H:i') }}</div>
        @endif
        @if($bapti->jadwal)<div class="mt-1 small text-muted"><i class="fas fa-calendar me-1 text-primary"></i>Jadwal: {{ $bapti->jadwal->jenis_ibadah }} — {{ $bapti->jadwal->tanggal->format('d/m/Y') }}</div>@endif
    </div>
</div>

{{-- Penandatangan (preview) --}}
@if($penandatangan->count())
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-signature me-2"></i>Penandatangan Dokumen</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            @foreach($penandatangan as $p)
            <div class="col-md-4 text-center">
                <div class="border rounded p-3">
                    @if($p->qr_enabled)<div class="mb-2"><i class="fas fa-qrcode fa-3x text-muted"></i><br><small class="text-muted">(QR Tanda Tangan)</small></div>@endif
                    <div class="fw-bold">{{ $p->nama_tampilan }}</div>
                    <div class="small text-muted">{{ $p->jabatan_tampilan }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
