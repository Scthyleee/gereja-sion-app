<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')</head><body>
@include('print._kop-surat')
<div class="title">WARTA JEMAAT</div>
<div class="subtitle">{{ $jadwal->judul_ibadah ?: $jadwal->jenis_ibadah }} — {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}{{ $jadwal->waktu ? ' · '.substr($jadwal->waktu,0,5).' WIB' : '' }}</div>

<h4 style="font-size:12px;margin-top:20px">I. PETUGAS PELAYANAN</h4>
<table class="data">
    <thead><tr><th style="width:30px">No</th><th>Nama</th><th>Tugas</th></tr></thead>
    <tbody>
        @foreach($jadwal->petugas as $i => $jp)
        <tr><td class="text-center">{{ $i+1 }}</td><td>{{ $jp->petugas->nama_petugas }}</td><td>{{ $jp->jenis_tugas }}</td></tr>
        @endforeach
    </tbody>
</table>

@if($jadwal->kantong->count())
<h4 style="font-size:12px;margin-top:15px">II. PERSEMBAHAN</h4>
<table class="data">
    <thead><tr><th>Kantong</th><th class="text-right">Jumlah</th></tr></thead>
    <tbody>
        @foreach($jadwal->kantong as $k)
        <tr><td>{{ $k->label }}</td><td class="text-right">Rp {{ number_format($k->total, 0, ',', '.') }}</td></tr>
        @endforeach
    </tbody>
    <tfoot><tr class="total-row"><td class="text-right">Total</td><td class="text-right">Rp {{ number_format($jadwal->kantong->sum('total'), 0, ',', '.') }}</td></tr></tfoot>
</table>
@endif

@if($jadwal->keterangan)
<h4 style="font-size:12px;margin-top:15px">III. PENGUMUMAN</h4>
<p style="font-size:10px">{{ $jadwal->keterangan }}</p>
@endif

<p style="text-align:center;margin-top:30px;font-size:10px;color:#666">— Tuhan Memberkati —</p>
</body></html>
