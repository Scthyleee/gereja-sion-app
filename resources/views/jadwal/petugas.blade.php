@extends('layouts.app')
@section('title', 'Master Petugas')

@section('content')
{{-- Tabs --}}
<ul class="nav nav-pills mb-4" style="gap:8px">
    <li class="nav-item"><a href="{{ route('jadwal.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-calendar-alt me-1"></i>Jadwal</a></li>
    <li class="nav-item"><a href="{{ route('insentif.index') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-money-bill me-1"></i>Insentif</a></li>
    <li class="nav-item"><a href="{{ route('petugas.index') }}" class="nav-link active" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none"><i class="fas fa-users-cog me-1"></i>Petugas</a></li>
    <li class="nav-item"><a href="{{ route('insentif.setting') }}" class="nav-link" style="color:var(--text)"><i class="fas fa-cog me-1"></i>Setting Insentif</a></li>
</ul>

<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Daftar Petugas</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>Job / Tugas</th><th>Telepon</th><th>Status</th><th style="width:100px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($petugas as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->nama_petugas }}</td>
                        <td>
                            @forelse($p->roles as $role)
                                <span class="badge bg-info me-1">{{ $role->jenis_tugas }}</span>
                            @empty
                                <span class="text-muted small">- Belum ada job -</span>
                            @endforelse
                        </td>
                        <td class="small">{{ $p->no_telepon ?: '-' }}</td>
                        <td><span class="badge bg-{{ $p->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($p->status) }}</span></td>
                        <td>
                            <form action="{{ route('petugas.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    {{-- Show roles inline --}}
                    @if($p->roles->count() > 0)
                    <tr>
                        <td colspan="5" class="py-1 px-4" style="background:var(--bg)">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <small class="text-muted fw-semibold">Tambah Job:</small>
                                <form action="{{ route('petugas.role.add', $p) }}" method="POST" class="d-flex gap-2 align-items-center">
                                    @csrf
                                    <input type="text" name="jenis_tugas" class="form-control form-control-sm" style="width:160px" placeholder="Jenis tugas baru" required>
                                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-plus"></i></button>
                                </form>
                                @foreach($p->roles as $role)
                                    <form action="{{ route('petugas.role.delete', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus role ini?')">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus {{ $role->jenis_tugas }}"><i class="fas fa-times"></i> {{ $role->jenis_tugas }}</button>
                                    </form>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada petugas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
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
                            <option>Song Leader</option><option>Pianis</option>
                            <option>Operator LCD</option><option>Koster</option><option>Multimedia</option><option>Security</option>
                            <option>Guru SHM</option><option>Band/Musik</option><option>Pendeta</option><option>Majelis</option>
                            <option>Lainnya</option>
                        </select>
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
@endsection
