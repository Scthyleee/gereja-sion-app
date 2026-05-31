@extends('layouts.app')
@section('title', 'Input Persembahan Syukur')

@section('content')
<div class="mb-3"><a href="{{ route('ps.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a></div>

<form action="{{ route('ps.store') }}" method="POST" id="formPS">@csrf
<input type="hidden" name="sumber" value="{{ $sumber }}">

<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-hand-holding-heart me-2" style="color:var(--primary)"></i>Input PS — {{ $sumber === 'jadwal' ? 'Dari Ibadah' : 'Dari Kantor' }}</h6></div>
    <div class="content-card__body">
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label fw-semibold">Tanggal *</label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">Diterima Oleh</label><input type="text" name="diterima_oleh" class="form-control" value="{{ auth()->user()->nama }}"></div>
        </div>
    </div>
</div>

{{-- Amplop entries --}}
<div id="amplopEntries">
    <div class="content-card mb-3 amplop-entry" data-idx="0">
        <div class="content-card__header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-envelope-open me-2"></i>Amplop #1</h6>
            <button type="button" class="btn btn-sm btn-outline-danger d-none btn-hapus-amplop" onclick="this.closest('.amplop-entry').remove();reindex()"><i class="fas fa-times me-1"></i>Hapus</button>
        </div>
        <div class="content-card__body">
            {{-- Jemaat search --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cari Jemaat *</label>
                    <input type="text" class="form-control jemaat-search" placeholder="Ketik nama atau No KK..." autocomplete="off">
                    <input type="hidden" name="items[0][jemaat_id]" class="jemaat-id" required>
                    <div class="jemaat-dropdown"></div>
                </div>
                <div class="col-md-6">
                    <div class="jemaat-info p-2 rounded small" style="background:var(--bg-body);display:none">
                        <div class="fw-semibold jemaat-nama"></div>
                        <div class="text-muted jemaat-detail"></div>
                    </div>
                </div>
            </div>
            {{-- PS rows --}}
            <div class="ps-rows">
                <div class="row g-2 mb-2 ps-row">
                    <div class="col-md-4">
                        <select name="items[0][rows][0][jenis_ps]" class="form-select form-select-sm jenis-ps-select" required>
                            <option value="">— Pilih Jenis PS —</option>
                            @foreach($jenisConfig as $j)<option value="{{ $j->nama }}" data-kode="{{ $j->kode_akun }}" data-kantong="{{ $j->kantong_dana }}">{{ $j->nama }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="text" class="form-control form-control-sm kode-akun-display" readonly placeholder="Kode Akun" style="background:var(--bg-body)"></div>
                    <div class="col-md-2"><input type="text" class="form-control form-control-sm kantong-display" readonly placeholder="Kantong" style="background:var(--bg-body)"></div>
                    <div class="col-md-3"><input type="number" name="items[0][rows][0][jumlah]" class="form-control form-control-sm jumlah-input" placeholder="Jumlah (Rp)" min="0" required></div>
                    <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.ps-row').remove()"><i class="fas fa-times"></i></button></div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success mt-2 btn-add-ps-row"><i class="fas fa-plus me-1"></i>Tambah Jenis PS</button>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mb-4">
    <button type="button" class="btn btn-outline-primary" id="btnTambahAmplop"><i class="fas fa-plus me-1"></i>Tambah Amplop Baru</button>
    <button type="submit" class="btn btn-primary ms-auto"><i class="fas fa-save me-1"></i>Simpan Semua</button>
</div>
</form>
@endsection

@push('styles')
<style>
.jemaat-dropdown{position:relative}
.jemaat-dropdown .dropdown-list{position:absolute;top:0;left:0;right:0;z-index:100;background:var(--bg-card);border:1px solid var(--border);border-radius:6px;max-height:250px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.15)}
.jemaat-dropdown .dropdown-item{padding:8px 12px;cursor:pointer;border-bottom:1px solid var(--border)}
.jemaat-dropdown .dropdown-item:hover{background:var(--primary);color:#fff}
.jemaat-dropdown .dropdown-item .small{opacity:.7}
</style>
@endpush

@push('scripts')
<script>
let amplopIdx = 1;
const jenisOptions = `<option value="">— Pilih Jenis PS —</option>@foreach($jenisConfig as $j)<option value="{{ $j->nama }}" data-kode="{{ $j->kode_akun }}" data-kantong="{{ $j->kantong_dana }}">{{ $j->nama }}</option>@endforeach`;

// Add amplop
document.getElementById('btnTambahAmplop').addEventListener('click', () => {
    const tpl = document.querySelector('.amplop-entry').cloneNode(true);
    tpl.dataset.idx = amplopIdx;
    tpl.querySelector('.amplop-entry h6, .content-card__header h6').innerHTML = `<i class="fas fa-envelope-open me-2"></i>Amplop #${amplopIdx+1}`;
    tpl.querySelector('.btn-hapus-amplop').classList.remove('d-none');
    tpl.querySelector('.jemaat-search').value = '';
    tpl.querySelector('.jemaat-id').value = '';
    tpl.querySelector('.jemaat-id').name = `items[${amplopIdx}][jemaat_id]`;
    tpl.querySelector('.jemaat-info').style.display = 'none';
    const rows = tpl.querySelector('.ps-rows');
    rows.innerHTML = `<div class="row g-2 mb-2 ps-row">
        <div class="col-md-4"><select name="items[${amplopIdx}][rows][0][jenis_ps]" class="form-select form-select-sm jenis-ps-select" required>${jenisOptions}</select></div>
        <div class="col-md-2"><input type="text" class="form-control form-control-sm kode-akun-display" readonly placeholder="Kode Akun" style="background:var(--bg-body)"></div>
        <div class="col-md-2"><input type="text" class="form-control form-control-sm kantong-display" readonly placeholder="Kantong" style="background:var(--bg-body)"></div>
        <div class="col-md-3"><input type="number" name="items[${amplopIdx}][rows][0][jumlah]" class="form-control form-control-sm jumlah-input" placeholder="Jumlah (Rp)" min="0" required></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.ps-row').remove()"><i class="fas fa-times"></i></button></div>
    </div>`;
    document.getElementById('amplopEntries').appendChild(tpl);
    bindEntry(tpl);
    amplopIdx++;
});

// Bind events per entry
function bindEntry(entry) {
    const idx = entry.dataset.idx;
    // Add PS row
    entry.querySelector('.btn-add-ps-row').addEventListener('click', function() {
        const rows = entry.querySelector('.ps-rows');
        const rowCount = rows.querySelectorAll('.ps-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 ps-row';
        row.innerHTML = `
            <div class="col-md-4"><select name="items[${idx}][rows][${rowCount}][jenis_ps]" class="form-select form-select-sm jenis-ps-select" required>${jenisOptions}</select></div>
            <div class="col-md-2"><input type="text" class="form-control form-control-sm kode-akun-display" readonly placeholder="Kode Akun" style="background:var(--bg-body)"></div>
            <div class="col-md-2"><input type="text" class="form-control form-control-sm kantong-display" readonly placeholder="Kantong" style="background:var(--bg-body)"></div>
            <div class="col-md-3"><input type="number" name="items[${idx}][rows][${rowCount}][jumlah]" class="form-control form-control-sm jumlah-input" placeholder="Jumlah (Rp)" min="0" required></div>
            <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.ps-row').remove()"><i class="fas fa-times"></i></button></div>`;
        rows.appendChild(row);
        bindJenisSelect(row.querySelector('.jenis-ps-select'));
    });

    // Jemaat search
    let searchTimeout;
    const searchInput = entry.querySelector('.jemaat-search');
    const dropdown = entry.querySelector('.jemaat-dropdown');
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { dropdown.innerHTML = ''; return; }
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('ps.search-jemaat') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) { dropdown.innerHTML = '<div class="dropdown-list"><div class="dropdown-item text-muted">Tidak ditemukan</div></div>'; return; }
                    let html = '<div class="dropdown-list">';
                    data.forEach(j => {
                        html += `<div class="dropdown-item" data-id="${j.id}" data-nama="${j.nama}" data-detail="${j.lingkungan || '-'} · KK: ${j.no_kk || '-'}">
                            <div class="fw-semibold">${j.nama}</div>
                            <div class="small">${j.lingkungan || '-'} · KK: ${j.no_kk || '-'}</div>
                        </div>`;
                    });
                    html += '</div>';
                    dropdown.innerHTML = html;
                    dropdown.querySelectorAll('.dropdown-item').forEach(item => {
                        item.addEventListener('click', function() {
                            entry.querySelector('.jemaat-id').value = this.dataset.id;
                            searchInput.value = this.dataset.nama;
                            entry.querySelector('.jemaat-nama').textContent = this.dataset.nama;
                            entry.querySelector('.jemaat-detail').textContent = this.dataset.detail;
                            entry.querySelector('.jemaat-info').style.display = 'block';
                            dropdown.innerHTML = '';
                        });
                    });
                });
        }, 300);
    });

    // Bind jenis selects
    entry.querySelectorAll('.jenis-ps-select').forEach(bindJenisSelect);
}

function bindJenisSelect(sel) {
    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const row = this.closest('.ps-row');
        row.querySelector('.kode-akun-display').value = opt.dataset.kode || '';
        row.querySelector('.kantong-display').value = opt.dataset.kantong || '';
    });
}

// Init
document.querySelectorAll('.amplop-entry').forEach(bindEntry);
</script>
@endpush
