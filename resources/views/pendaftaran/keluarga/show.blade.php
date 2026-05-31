@extends('layouts.app')
@section('title', 'Detail Keluarga — ' . $keluarga->nama_kepala_keluarga)

@section('content')
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('keluarga.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    <a href="{{ route('keluarga.edit', $keluarga) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-pencil-alt me-1"></i>Edit</a>
    <a href="{{ route('print.kartu-keluarga', $keluarga) }}" class="btn btn-sm btn-outline-primary ms-auto" target="_blank"><i class="fas fa-print me-1"></i>Cetak Formulir KK</a>
</div>

<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-home me-2" style="color:var(--primary)"></i>{{ $keluarga->nama_kepala_keluarga }}</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-4"><small class="text-muted">No. KK</small><div class="fw-semibold">{{ $keluarga->no_kk ?: '-' }}</div></div>
            <div class="col-md-4"><small class="text-muted">Lingkungan</small><div class="fw-semibold"><span class="badge bg-info">{{ $keluarga->lingkunganSion?->nama ?? '-' }}</span></div></div>
            <div class="col-md-4"><small class="text-muted">Telepon</small><div class="fw-semibold">{{ $keluarga->no_telepon ?: '-' }}</div></div>
            <div class="col-md-12"><small class="text-muted">Alamat</small><div>{{ $keluarga->alamat ?? '-' }}, RT {{ $keluarga->rt ?? '-' }}/RW {{ $keluarga->rw ?? '-' }}, {{ $keluarga->kelurahan }}, {{ $keluarga->kecamatan }}, {{ $keluarga->kota }}</div></div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-users me-2"></i>Anggota Keluarga ({{ $keluarga->anggota->count() }} jiwa)</h6></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Nama</th><th>Hubungan</th><th>JK</th><th>TTL</th><th>Baptis</th><th>Sidi</th><th>Pekerjaan</th></tr></thead>
                <tbody>
                    @foreach($keluarga->anggota as $i => $a)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $a->nama_lengkap }}</td>
                        <td><span class="badge bg-secondary">{{ $a->hubungan_keluarga ?? '-' }}</span></td>
                        <td>{{ $a->jenis_kelamin }}</td>
                        <td class="small">{{ $a->tempat_lahir }}, {{ $a->tanggal_lahir?->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $a->status_baptis === 'Sudah Baptis' ? 'success' : 'warning' }}">{{ $a->status_baptis }}</span></td>
                        <td><span class="badge bg-{{ $a->status_sidi === 'Sudah Sidi' ? 'success' : 'warning' }}">{{ $a->status_sidi }}</span></td>
                        <td class="small">{{ $a->pekerjaan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
