@extends('layouts.app')
@section('title', $jadwal->display_name . ' — ' . $jadwal->tanggal->format('d/m/Y'))

@section('content')
<div class="mb-3">
    <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    @if(auth()->user()->canEdit('jadwal'))
    @if($jadwal->status !== 'completed')
    <form action="{{ route('jadwal.finalize', $jadwal) }}" method="POST" class="d-inline" onsubmit="return confirm('Finalisasi jadwal ini?')">
        @csrf
        <button class="btn btn-sm btn-success ms-2"><i class="fas fa-check-circle me-1"></i>Finalisasi</button>
    </form>
    @else
    <span class="badge bg-success ms-2 fs-6"><i class="fas fa-check me-1"></i>Sudah Difinalisasi</span>
    <form action="{{ route('jadwal.unfinalize', $jadwal) }}" method="POST" class="d-inline" id="formUnfinalize">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Batalkan finalisasi? Transaksi otomatis akan dihapus.')"><i class="fas fa-undo me-1"></i>Batalkan Finalisasi</button>
    </form>
    @endif
    @else
    @if($jadwal->status === 'completed')
    <span class="badge bg-success ms-2 fs-6"><i class="fas fa-check me-1"></i>Sudah Difinalisasi</span>
    @endif
    @endif
    <div class="float-end d-flex gap-2 flex-wrap">
        <a href="{{ route('print.surat-tugas', $jadwal) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf me-1"></i>Surat Tugas</a>
        <a href="{{ route('print.surat-tanda-tangan', $jadwal) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fas fa-signature me-1"></i>Surat TTD</a>
        <a href="{{ route('print.amplop', $jadwal) }}" target="_blank" class="btn btn-sm btn-outline-warning"><i class="fas fa-envelope me-1"></i>Amplop</a>
        <a href="{{ route('print.jadwal-ibadah', $jadwal) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i>Print Jadwal</a>
    </div>
</div>

{{-- Info + Edit Hadir --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-3"><small class="text-muted d-block">Judul Ibadah</small><strong>{{ $jadwal->display_name }}</strong><br><small class="text-muted">Jenis: {{ $jadwal->jenis_ibadah }}</small></div>
            <div class="col-md-2"><small class="text-muted d-block">Tanggal</small><strong>{{ $jadwal->tanggal->translatedFormat('d M Y') }}</strong></div>
            <div class="col-md-2"><small class="text-muted d-block">Waktu</small><strong>{{ $jadwal->waktu ? substr($jadwal->waktu, 0, 5) : '-' }}</strong></div>
            <div class="col-md-2"><small class="text-muted d-block">Lokasi</small><strong>{{ $jadwal->lokasi ?: '-' }}</strong></div>
            <div class="col-md-1">
                <small class="text-muted d-block">Hadir</small>
                <form action="{{ route('jadwal.hadir.update', $jadwal) }}" method="POST" class="d-flex gap-1">
                    @csrf @method('PUT')
                    <input type="number" name="jumlah_hadir" value="{{ $jadwal->jumlah_hadir }}" class="form-control form-control-sm" style="width:70px" min="0">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                </form>
            </div>
            <div class="col-md-2"><small class="text-muted d-block">Status</small>
                <span class="badge bg-{{ $jadwal->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($jadwal->status) }}</span>
            </div>
        </div>
        {{-- Tema & Ayat Khotbah --}}
        <div class="row g-3 mt-2">
            <div class="col-md-5">
                <form action="{{ route('jadwal.update', $jadwal) }}" method="POST" class="d-flex gap-2 align-items-end">
                    @csrf @method('PUT')
                    <div class="flex-fill">
                        <small class="text-muted d-block">Tema Khotbah</small>
                        <input type="text" name="tema_ibadah" class="form-control form-control-sm" value="{{ $jadwal->tema_ibadah }}" placeholder="Tema ibadah...">
                    </div>
                    <input type="hidden" name="tanggal" value="{{ $jadwal->tanggal->format('Y-m-d') }}">
                    <input type="hidden" name="jenis_ibadah" value="{{ $jadwal->jenis_ibadah }}">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                </form>
            </div>
            <div class="col-md-5">
                <form action="{{ route('jadwal.update', $jadwal) }}" method="POST" class="d-flex gap-2 align-items-end">
                    @csrf @method('PUT')
                    <div class="flex-fill">
                        <small class="text-muted d-block">Ayat Khotbah</small>
                        <input type="text" name="ayat_khotbah" class="form-control form-control-sm" value="{{ $jadwal->ayat_khotbah }}" placeholder="e.g. Yohanes 3:16">
                    </div>
                    <input type="hidden" name="tanggal" value="{{ $jadwal->tanggal->format('Y-m-d') }}">
                    <input type="hidden" name="jenis_ibadah" value="{{ $jadwal->jenis_ibadah }}">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                </form>
            </div>
        </div>
        {{-- Judul Ibadah --}}
        <div class="row g-3 mt-2">
            <div class="col-md-10">
                <form action="{{ route('jadwal.update', $jadwal) }}" method="POST" class="d-flex gap-2 align-items-end">
                    @csrf @method('PUT')
                    <div class="flex-fill">
                        <small class="text-muted d-block">Judul Ibadah (custom)</small>
                        <input type="text" name="judul_ibadah" class="form-control form-control-sm" value="{{ $jadwal->judul_ibadah }}" placeholder="e.g. Ibadah Tutup Tahun di Gereja Sion">
                    </div>
                    <input type="hidden" name="tanggal" value="{{ $jadwal->tanggal->format('Y-m-d') }}">
                    <input type="hidden" name="jenis_ibadah" value="{{ $jadwal->jenis_ibadah }}">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Foto TTD --}}
<div class="content-card mb-4">
    <div class="content-card__header d-flex justify-content-between">
        <h6 class="mb-0"><i class="fas fa-camera me-2"></i>Foto Tanda Tangan (Verifikasi Kehadiran)</h6>
    </div>
    <div class="content-card__body">
        @if($jadwal->foto_ttd_path)
        <img src="{{ asset('storage/'.$jadwal->foto_ttd_path) }}" alt="Foto TTD" class="img-fluid rounded" style="max-height:300px">
        @else
        <p class="text-muted small mb-2">Belum ada foto tanda tangan</p>
        @endif
        <form action="{{ route('jadwal.upload-ttd', $jadwal) }}" method="POST" enctype="multipart/form-data" class="mt-2 d-flex gap-2">
            @csrf
            <input type="file" name="foto_ttd" accept="image/*" class="form-control form-control-sm" style="max-width:300px" required>
            <button class="btn btn-sm btn-primary"><i class="fas fa-upload me-1"></i>Upload</button>
        </form>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT COL --}}
    <div class="col-lg-6">
        {{-- Petugas Insentif --}}
        <div class="content-card mb-4">
            <div class="content-card__header d-flex justify-content-between">
                <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Petugas Insentif ({{ $jadwal->petugas->where('kategori','insentif')->count() }})</h6>
                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                <div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPetugas"><i class="fas fa-plus me-1"></i>Internal</button>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalExternal"><i class="fas fa-plus me-1"></i>Luar</button>
                </div>
                @endif
            </div>
            <div class="content-card__body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Tugas</th><th>Nama</th><th class="text-end">Insentif</th><th style="width:40px"></th></tr></thead>
                    <tbody>
                        @forelse($jadwal->petugas->where('kategori','insentif') as $jp)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $jp->slot_nama ?: $jp->jenis_tugas }}</span></td>
                            <td class="fw-semibold small">
                                @if($jp->is_external)
                                {{ $jp->nama_external }} <span class="badge bg-warning text-dark">Luar</span>
                                @else
                                {{ $jp->petugas->nama_petugas ?? 'N/A' }}
                                @if($jp->petugas && $jp->petugas->is_pendeta_sion) <span class="badge bg-info">Sion</span>@endif
                                @endif
                            </td>
                            <td class="text-end small">Rp {{ number_format($jp->nominal_insentif, 0, ',', '.') }}</td>
                            <td>
                                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                                <form action="{{ route('jadwal.petugas.destroy', $jp) }}" method="POST">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted small py-3">Belum ada petugas</td></tr>
                        @endforelse
                    </tbody>
                    @if($jadwal->petugas->where('kategori','insentif')->count())
                    <tfoot><tr class="fw-bold"><td colspan="2">Total</td><td class="text-end">Rp {{ number_format($jadwal->petugas->where('kategori','insentif')->sum('nominal_insentif'), 0, ',', '.') }}</td><td></td></tr></tfoot>
                    @endif
                </table>
            </div>
            {{-- Kode Akun Insentif info --}}
            @if($jadwal->petugas->where('kategori','insentif')->count() > 0)
            <div class="content-card__body border-top pt-2 pb-2" style="border-color:var(--border)!important">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Saat finalisasi, insentif akan dibebankan ke kode akun <code>II(APg).01.01</code> (Honor/Insentif Petugas).</small>
            </div>
            @endif
        </div>

        {{-- Petugas Pelayanan --}}
        <div class="content-card mb-4">
            <div class="content-card__header d-flex justify-content-between">
                <h6 class="mb-0"><i class="fas fa-hands-helping me-2"></i>Petugas Pelayanan ({{ $jadwal->petugas->where('kategori','pelayanan')->count() }})</h6>
                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                <div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPelayanan"><i class="fas fa-plus me-1"></i>Tambah</button>
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalRandom"><i class="fas fa-random me-1"></i>Random</button>
                </div>
                @endif
            </div>
            <div class="content-card__body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Tugas</th><th>Nama</th><th style="width:40px"></th></tr></thead>
                    <tbody>
                        @forelse($jadwal->petugas->where('kategori','pelayanan') as $jp)
                        <tr>
                            <td><span class="badge bg-info">{{ $jp->jenis_tugas }}</span></td>
                            <td class="small">{{ $jp->nama_external ?? 'N/A' }}</td>
                            <td>
                                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                                <form action="{{ route('jadwal.petugas.destroy', $jp) }}" method="POST">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted small py-3">Belum ada petugas pelayanan (Diakon/Penetua)</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kantong Kolekte --}}
        <div class="content-card">
            <div class="content-card__header d-flex justify-content-between">
                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Kantong Kolekte ({{ $jadwal->kantong->count() }})</h6>
                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalKantong"><i class="fas fa-plus"></i></button>
                @endif
            </div>
            <div class="content-card__body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Kode</th><th>Label</th><th class="text-end">Total</th><th style="width:40px"></th></tr></thead>
                    <tbody>
                        @forelse($jadwal->kantong as $k)
                        <tr>
                            <td><code class="text-primary small">{{ $k->kodeAkun->kode ?? '-' }}</code></td>
                            <td class="small">{{ $k->kodeAkun->keterangan ?? $k->kodeAkun->nama ?? $k->label }}</td>
                            <td class="text-end fw-semibold small">Rp {{ number_format($k->total, 0, ',', '.') }}</td>
                            <td>
                                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                                <form action="{{ route('jadwal.kantong.destroy', $k) }}" method="POST">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted small py-3">Belum ada kantong</td></tr>
                        @endforelse
                    </tbody>
                    @if($jadwal->kantong->count())
                    <tfoot><tr class="fw-bold"><td colspan="2">Total</td><td class="text-end">Rp {{ number_format($jadwal->kantong->sum('total'), 0, ',', '.') }}</td><td></td></tr></tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT: Amplop PS --}}
    <div class="col-lg-6">
        <div class="content-card">
            <div class="content-card__header d-flex justify-content-between">
                <h6 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>Amplop PS ({{ $jadwal->amplop->count() }})</h6>
                @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAmplop"><i class="fas fa-plus"></i></button>
                @endif
            </div>
            <div class="content-card__body p-0">
                @forelse($jadwal->amplop as $amp)
                <div class="border-bottom px-3 py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold small">{{ $amp->nama_pemberi }}</div>
                            @foreach($amp->detail as $d)
                            <div class="text-muted" style="font-size:.75rem">
                                <code>{{ $d->kodeAkun->kode ?? '?' }}</code> {{ $d->kodeAkun->nama ?? '' }} — Rp {{ number_format($d->nominal, 0, ',', '.') }}
                            </div>
                            @endforeach
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small text-success">Rp {{ number_format($amp->detail->sum('nominal'), 0, ',', '.') }}</div>
                            @if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
                            <form action="{{ route('jadwal.amplop.destroy', $amp) }}" method="POST" class="mt-1">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small"><i class="fas fa-envelope fa-2x mb-2 d-block"></i>Belum ada amplop</div>
                @endforelse
            </div>
            @if($jadwal->amplop->count())
            <div class="content-card__body border-top">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total Amplop</span>
                    <span class="text-success">Rp {{ number_format($jadwal->amplop->sum(fn($a) => $a->detail->sum('nominal')), 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}
@if($jadwal->status !== 'completed' && auth()->user()->canEdit('jadwal'))
@include('jadwal._modals-show')
@endif
@endsection

@push('scripts')
<script>
document.getElementById('selPetugas')?.addEventListener('change', function() {
    // Nominal now comes from InsentifConfig, no auto-fill from petugas
});
document.getElementById('selSlot')?.addEventListener('change', function() {
    const custom = document.getElementById('customTugasRow');
    const inp = document.getElementById('inpJenisTugas');
    const slotVal = this.value;
    if (slotVal) { inp.value = slotVal; custom.style.display = 'none'; }
    else { inp.value = ''; custom.style.display = 'block'; }

    // Filter petugas dropdown by roles matching the slot
    const sel = document.getElementById('selPetugas');
    const ts = sel.tomselect;
    if (ts) {
        ts.clear();
        ts.clearOptions();
        // Re-add filtered options
        @foreach($petugasList as $p)
            @php $cnt = $petugasCount[$p->id_petugas] ?? 0; $roles = $p->roles->pluck('jenis_tugas')->join(', '); @endphp
            if (!slotVal || '{{ addslashes($roles) }}'.toLowerCase().includes(slotVal.toLowerCase())) {
                ts.addOption({ value: '{{ $p->id_petugas }}', text: '{{ addslashes($p->nama_petugas) }}{{ $p->is_pendeta_sion ? " (Sion)" : "" }} \u2014 {{ addslashes($roles) }} ({{ $cnt }}x)' });
            }
        @endforeach
        ts.refreshOptions(false);
    } else {
        // Fallback: show/hide native options
        Array.from(sel.options).forEach(opt => {
            if (!opt.value) return;
            const roles = opt.dataset.roles || '';
            opt.style.display = (!slotVal || roles.toLowerCase().includes(slotVal.toLowerCase())) ? '' : 'none';
        });
    }
});
function addAmplopRow() {
    const first = document.querySelector('.amplop-row');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('input').forEach(i => i.value = '');
    clone.querySelector('select').value = '';
    document.getElementById('amplopRows').appendChild(clone);
}
function autoFillNamaPemberi(sel) {
    const opt = sel.options[sel.selectedIndex];
    const nama = opt?.dataset?.nama || '';
    const inp = document.querySelector('input[name="nama_pemberi"]');
    if (nama && inp) inp.value = nama;
}
// Hook TomSelect change for warga amplop dropdown
setTimeout(function() {
    const selW = document.getElementById('selWargaAmplop');
    if (selW && selW.tomselect) {
        selW.tomselect.on('change', function(val) {
            if (!val) return;
            const opt = selW.querySelector('option[value="' + val + '"]');
            const nama = opt?.dataset?.nama || opt?.textContent?.trim() || '';
            const inp = document.querySelector('input[name="nama_pemberi"]');
            if (nama && inp) inp.value = nama;
        });
    }
}, 500);
</script>
@endpush
