@extends('layouts.app')
@section('title', 'Edit Input — ' . $up->inisial)

@section('content')
<div class="mb-3">
    <a href="{{ route('input-kategorial.show', $input->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali ke Detail</a>
</div>

<div class="content-card mb-4">
    <div class="content-card__header">
        <h6 class="mb-0"><i class="fas fa-pencil-alt me-2" style="color:var(--primary)"></i>Edit Input — <span class="badge bg-info">{{ $up->inisial }}</span> {{ $up->nama }}</h6>
    </div>
    <div class="content-card__body">
        <form action="{{ route('input-kategorial.update', $input->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="unit_pelayanan_id" value="{{ $up->nomor_up }}">

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Jenis Input <span class="text-danger">*</span></label>
                    <select name="jenis_input" class="form-select form-select-sm" required>
                        <option value="kolekte_ibadah" @selected($input->jenis_input==='kolekte_ibadah')>Kantong Kolekte</option>
                        <option value="amplop_ps" @selected($input->jenis_input==='amplop_ps')>Amplop PS</option>
                        <option value="amplop_kk" @selected($input->jenis_input==='amplop_kk')>Amplop KK</option>
                        <option value="pengeluaran" @selected($input->jenis_input==='pengeluaran')>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $input->tanggal->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Ibadah</label>
                    <input type="text" name="jenis_ibadah" class="form-control form-control-sm" value="{{ $input->jenis_ibadah }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="catatan" class="form-control form-control-sm" value="{{ $input->catatan }}">
                </div>
            </div>

            <div class="content-card">
                <div class="content-card__header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detail Item</h6>
                    <button type="button" class="btn btn-sm btn-success" onclick="addRow()"><i class="fas fa-plus me-1"></i>Tambah Baris</button>
                </div>
                <div class="content-card__body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width:250px">Kode Akun</th>
                                    <th>Nama Penyetor</th>
                                    <th>Keterangan</th>
                                    <th class="text-end" style="width:160px">Jumlah (Rp)</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="detailBody">
                                @foreach($input->detail as $d)
                                <tr>
                                    <td>
                                        <select name="akun_ids[]" class="form-select form-select-sm">
                                            @if($akunPenerimaan->count())
                                            <optgroup label="Penerimaan">
                                            @foreach($akunPenerimaan as $a)
                                            <option value="{{ $a->id }}" @selected($a->id == $d->kode_akun_id)>{{ $a->kode }} — {{ Str::limit($a->nama, 40) }}</option>
                                            @endforeach
                                            </optgroup>
                                            @endif
                                            @if($akunPengeluaran->count())
                                            <optgroup label="Pengeluaran">
                                            @foreach($akunPengeluaran as $a)
                                            <option value="{{ $a->id }}" @selected($a->id == $d->kode_akun_id)>{{ $a->kode }} — {{ Str::limit($a->nama, 40) }}</option>
                                            @endforeach
                                            </optgroup>
                                            @endif
                                        </select>
                                    </td>
                                    <td><input type="text" name="nama_penyetor[]" class="form-control form-control-sm" value="{{ $d->nama_penyetor }}"></td>
                                    <td><input type="text" name="keterangan_detail[]" class="form-control form-control-sm" value="{{ $d->keterangan }}"></td>
                                    <td><input type="number" name="jumlah[]" class="form-control form-control-sm text-end jumlah-input" min="0" value="{{ $d->jumlah }}" oninput="calcTotal()"></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();calcTotal()"><i class="fas fa-times"></i></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold" style="background:rgba(82,138,94,.08)">
                                    <td colspan="3" class="text-end">TOTAL</td>
                                    <td class="text-end" id="grandTotal">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const akunOptions = `
@if($akunPenerimaan->count())
<optgroup label="Penerimaan">
@foreach($akunPenerimaan as $a)
<option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->nama, 40) }}</option>
@endforeach
</optgroup>
@endif
@if($akunPengeluaran->count())
<optgroup label="Pengeluaran">
@foreach($akunPengeluaran as $a)
<option value="{{ $a->id }}">{{ $a->kode }} — {{ Str::limit($a->nama, 40) }}</option>
@endforeach
</optgroup>
@endif
`;

function addRow() {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><select name="akun_ids[]" class="form-select form-select-sm">${akunOptions}</select></td>
        <td><input type="text" name="nama_penyetor[]" class="form-control form-control-sm" placeholder="(opsional)"></td>
        <td><input type="text" name="keterangan_detail[]" class="form-control form-control-sm" placeholder="(opsional)"></td>
        <td><input type="number" name="jumlah[]" class="form-control form-control-sm text-end jumlah-input" min="0" value="0" oninput="calcTotal()"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();calcTotal()"><i class="fas fa-times"></i></button></td>
    `;
    document.getElementById('detailBody').appendChild(row);
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.jumlah-input').forEach(el => { total += parseFloat(el.value) || 0; });
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
calcTotal();
</script>
@endpush
