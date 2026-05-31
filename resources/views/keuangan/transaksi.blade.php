@extends('layouts.app')
@section('title', 'Transaksi')

@section('content')
{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Penerimaan Bulan {{ $bulan }}/{{ $tahun }}</div>
                <div class="stat-card__value">Rp {{ number_format($summary['penerimaan'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card stat-card--red">
            <div class="stat-card__icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__label">Pengeluaran Bulan {{ $bulan }}/{{ $tahun }}</div>
                <div class="stat-card__value">Rp {{ number_format($summary['pengeluaran'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter + Add --}}
<div class="content-card mb-4">
    <div class="content-card__body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Bulan</label>
                        <select name="bulan" class="form-select" style="width:130px">
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" @selected($bulan==$m)>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Tahun</label>
                        <input type="number" name="tahun" class="form-control" style="width:100px" value="{{ $tahun }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small fw-semibold">Tipe</label>
                        <select name="tipe" class="form-select" style="width:140px">
                            <option value="semua" @selected($tipe==='semua')>Semua</option>
                            <option value="penerimaan" @selected($tipe==='penerimaan')>Penerimaan</option>
                            <option value="pengeluaran" @selected($tipe==='pengeluaran')>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                @if(auth()->user()->canEdit('transaksi'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-1"></i>Tambah Transaksi
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="content-card">
    <div class="content-card__header">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Transaksi</h6>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:100px">Tanggal</th>
                        <th style="width:140px">Kode Akun</th>
                        <th>Keterangan</th>
                        <th style="width:80px">Tipe</th>
                        <th class="text-end" style="width:140px">Jumlah</th>
                        <th style="width:110px">Diinput Oleh</th>
                        <th style="width:80px">Status</th>
                        <th style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                    <tr>
                        <td class="small">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="small"><code class="text-primary">{{ $t->kodeAkun->kode }}</code></td>
                        <td class="small">{{ $t->keterangan ?: ($t->kodeAkun->keterangan ?: $t->kodeAkun->nama) }}</td>
                        <td>
                            <span class="badge bg-{{ $t->tipe === 'penerimaan' ? 'success' : 'danger' }}">
                                {{ $t->tipe === 'penerimaan' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold {{ $t->tipe === 'penerimaan' ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="small text-muted">{{ $t->creator?->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $t->status === 'approved' ? 'success' : ($t->status === 'verified' ? 'info' : 'warning') }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('print.kwitansi', $t) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Kwitansi"><i class="fas fa-receipt"></i></a>
                            @if(auth()->user()->canEdit('transaksi'))
                            @if($t->status === 'draft')
                            <form action="{{ route('keuangan.transaksi.verify', $t) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning" title="Validasi"><i class="fas fa-check"></i></button>
                            </form>
                            @elseif($t->status === 'verified')
                            <form action="{{ route('keuangan.transaksi.approve', $t) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-info" title="Approve"><i class="fas fa-check-double"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('keuangan.transaksi.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transaksi->hasPages())
    <div class="content-card__body border-top py-2 px-3">{{ $transaksi->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border);">
            <div class="modal-header border-bottom" style="border-color:var(--border)!important">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('keuangan.transaksi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Tipe selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Transaksi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipe_input" id="tipePenerimaan" value="penerimaan" checked>
                            <label class="btn btn-outline-success" for="tipePenerimaan"><i class="fas fa-arrow-down me-1"></i>Penerimaan</label>
                            <input type="radio" class="btn-check" name="tipe_input" id="tipePengeluaran" value="pengeluaran">
                            <label class="btn btn-outline-danger" for="tipePengeluaran"><i class="fas fa-arrow-up me-1"></i>Pengeluaran</label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Akun</label>
                            <select name="kode_akun_id" id="selectAkun" class="form-select" required>
                                <option value="">Pilih akun...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Penyetor</label>
                            <input type="text" name="nama_penyetor" class="form-control" placeholder="(Opsional)">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan transaksi..."></textarea>
                        </div>

                        {{-- Detail Items (ATK Breakdown) --}}
                        <div class="col-12" id="detailSection" style="display:none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0"><i class="fas fa-list-ol me-1"></i>Rincian Item</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDetailRow()"><i class="fas fa-plus me-1"></i>Tambah Baris</button>
                            </div>
                            <div id="detailRows">
                                <div class="row g-1 mb-1 detail-row">
                                    <div class="col-4"><input type="text" name="detail_uraian[]" class="form-control form-control-sm" placeholder="Uraian (mis: Kertas HVS)"></div>
                                    <div class="col-1"><input type="number" name="detail_qty[]" class="form-control form-control-sm" placeholder="Qty" value="1" min="1"></div>
                                    <div class="col-2"><input type="text" name="detail_satuan[]" class="form-control form-control-sm" placeholder="Satuan"></div>
                                    <div class="col-3"><input type="number" name="detail_harga[]" class="form-control form-control-sm" placeholder="Harga" min="0"></div>
                                    <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.detail-row').remove()"><i class="fas fa-times"></i></button></div>
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Rincian ini akan muncul di kwitansi PDF.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color:var(--border)!important">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $akunPenerimaanJson = $akunPenerimaan->map(function($a) {
        return ['id' => $a->id, 'label' => $a->kode . ' — ' . ($a->keterangan ?: $a->nama), 'kode' => $a->kode, 'up' => $a->nomor_up];
    })->values();
    $akunPengeluaranJson = $akunPengeluaran->map(function($a) {
        return ['id' => $a->id, 'label' => $a->kode . ' — ' . ($a->keterangan ?: $a->nama), 'kode' => $a->kode, 'up' => $a->nomor_up];
    })->values();
@endphp
<script>
const akunPenerimaan = @json($akunPenerimaanJson);
const akunPengeluaran = @json($akunPengeluaranJson);

const upLabels = {
    1: '📋 MPH — Majelis Pekerja Harian',
    2: '👶 SPA — Seksi Pelayanan Anak',
    3: '🧑 SPRP — Seksi Pelayanan Remaja & Pemuda',
    4: '👩 SPPer — Seksi Pelayanan Perempuan',
    5: '👨 SPB — Seksi Pelayanan Bapak',
    6: '🧓 SPUI — Seksi Pelayanan Usia Indah',
    7: '📖 KPI/PKB — Kesaksian & Pemberitaan Injil',
    8: '🎄 PHBG — Panitia Hari Raya Gereja',
    9: '🏗️ P4A/SarPrasG — Pembangunan & Sarana Prasarana',
    10: '🤝 TPDS — Tim Pelayanan Diakonia Sosial',
    11: '🕊️ TPDK — Tim Pelayanan Diakonia Kedukaan',
    12: '💻 TPMTI — Tim Pelayanan Multimedia & TI',
    13: '🎵 TPMPSG — Tim Musik & Paduan Suara Gerejawi',
    14: '💰 TPUD — Tim Pelayanan Usaha Dana',
    15: '⚖️ MP — Majelis Pertimbangan',
    16: '🔍 BPP — Badan Pengawas Perbendaharaan',
    17: '🏠 PLP SION — Pengurus Lingkungan Pelayanan Sion',
};

let tomSelectAkun = null;

function updateAkunSelect() {
    const tipe = document.querySelector('input[name="tipe_input"]:checked').value;
    const sel = document.getElementById('selectAkun');
    const list = tipe === 'penerimaan' ? akunPenerimaan : akunPengeluaran;

    // Destroy existing TomSelect instance
    if (tomSelectAkun) {
        tomSelectAkun.destroy();
        tomSelectAkun = null;
    }

    sel.innerHTML = '<option value="">Pilih akun ' + (tipe === 'penerimaan' ? 'penerimaan' : 'pengeluaran') + '...</option>';

    // Group by UP
    const groups = {};
    list.forEach(a => {
        const g = a.up || 0;
        if (!groups[g]) groups[g] = [];
        groups[g].push(a);
    });

    const prefix = tipe === 'penerimaan' ? '📥' : '📤';
    Object.keys(groups).sort((a,b) => a-b).forEach(up => {
        const grp = document.createElement('optgroup');
        grp.label = prefix + ' ' + (upLabels[up] || (tipe === 'penerimaan' ? 'Penerimaan Lainnya' : 'Pengeluaran Lainnya'));
        groups[up].forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.label;
            grp.appendChild(opt);
        });
        sel.appendChild(grp);
    });

    // Reinit TomSelect with updated options
    tomSelectAkun = new TomSelect(sel, {
        plugins: ['dropdown_input'],
        create: false,
        allowEmptyOption: true,
        sortField: { field: 'text', direction: 'asc' }
    });
}

document.querySelectorAll('input[name="tipe_input"]').forEach(r => r.addEventListener('change', function() {
    updateAkunSelect();
    // Show detail section only for pengeluaran
    const sec = document.getElementById('detailSection');
    if (sec) sec.style.display = this.value === 'pengeluaran' ? 'block' : 'none';
}));
updateAkunSelect();

function addDetailRow() {
    const row = document.createElement('div');
    row.className = 'row g-1 mb-1 detail-row';
    row.innerHTML = `
        <div class="col-4"><input type="text" name="detail_uraian[]" class="form-control form-control-sm" placeholder="Uraian"></div>
        <div class="col-1"><input type="number" name="detail_qty[]" class="form-control form-control-sm" value="1" min="1"></div>
        <div class="col-2"><input type="text" name="detail_satuan[]" class="form-control form-control-sm" placeholder="Satuan"></div>
        <div class="col-3"><input type="number" name="detail_harga[]" class="form-control form-control-sm" placeholder="Harga" min="0"></div>
        <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.detail-row').remove()"><i class="fas fa-times"></i></button></div>
    `;
    document.getElementById('detailRows').appendChild(row);
}
</script>
@endpush
