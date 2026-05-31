<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')
<style>
    .info-grid { display: table; width: 100%; margin-bottom: 15px; }
    .info-item { display: table-cell; padding: 5px 10px; border: 1px solid #ccc; text-align: center; font-size: 9pt; }
    .info-item strong { display: block; font-size: 11pt; }
    .section { margin-top: 15px; }
    .section h4 { font-size: 11pt; border-bottom: 1px solid #999; padding-bottom: 3px; margin-bottom: 8px; }
</style>
</head><body>
@include('print._kop-surat')
<div class="title">LAPORAN JADWAL IBADAH</div>
<div class="subtitle">{{ $jadwal->judul_ibadah ?: $jadwal->jenis_ibadah }} — {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</div>

<div class="info-grid">
    <div class="info-item">Waktu<br><strong>{{ $jadwal->waktu ? substr($jadwal->waktu, 0, 5) . ' WIB' : '-' }}</strong></div>
    <div class="info-item">Lokasi<br><strong>{{ $jadwal->lokasi ?: '-' }}</strong></div>
    <div class="info-item">Hadir<br><strong>{{ $jadwal->jumlah_hadir ?? 0 }} orang</strong></div>
    <div class="info-item">Status<br><strong>{{ ucfirst($jadwal->status) }}</strong></div>
</div>

<div class="section">
    <h4>I. PETUGAS INSENTIF</h4>
    <table class="data">
        <thead><tr><th>No</th><th>Nama</th><th>Tugas</th><th class="text-right">Insentif</th></tr></thead>
        <tbody>
            @foreach($jadwal->petugas->where('kategori', 'insentif') as $jp)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $jp->is_external ? $jp->nama_external.' (Luar)' : ($jp->petugas->nama_petugas ?? $jp->nama_external) }}</td>
                <td>{{ $jp->jenis_tugas }}</td>
                <td class="text-right">Rp {{ number_format($jp->nominal_insentif, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr class="total-row"><td colspan="3" class="text-right">Total Insentif</td><td class="text-right">Rp {{ number_format($jadwal->petugas->where('kategori','insentif')->sum('nominal_insentif'), 0, ',', '.') }}</td></tr></tfoot>
    </table>
</div>

@if($jadwal->petugas->where('kategori', 'pelayanan')->count())
<div class="section">
    <h4>II. PETUGAS PELAYANAN (Tanpa Insentif)</h4>
    <table class="data">
        <thead><tr><th>No</th><th>Nama</th><th>Tugas</th><th>Jabatan</th></tr></thead>
        <tbody>
            @foreach($jadwal->petugas->where('kategori', 'pelayanan') as $jp)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $jp->nama_external ?? ($jp->petugas->nama_petugas ?? 'N/A') }}</td>
                <td>{{ $jp->jenis_tugas }}</td>
                <td>Dkn/Pnt</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($jadwal->kantong->count())
<div class="section">
    <h4>III. KANTONG KOLEKTE</h4>
    <table class="data">
        <thead><tr><th>Kantong</th><th class="text-right">Jumlah</th></tr></thead>
        <tbody>
            @foreach($jadwal->kantong as $k)
            <tr><td>{{ $k->label }}</td><td class="text-right">Rp {{ number_format($k->total, 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
        <tfoot><tr class="total-row"><td class="text-right">Total KK</td><td class="text-right">Rp {{ number_format($jadwal->kantong->sum('total'), 0, ',', '.') }}</td></tr></tfoot>
    </table>
</div>
@endif

@if($jadwal->amplop->count())
<div class="section">
    <h4>IV. PERSEMBAHAN SYUKUR ({{ $jadwal->amplop->count() }} Amplop)</h4>
    <table class="data">
        <thead><tr><th>No</th><th>Nama</th><th class="text-right">Total</th></tr></thead>
        <tbody>
            @foreach($jadwal->amplop as $amp)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $amp->nama_pemberi }}</td>
                <td class="text-right">Rp {{ number_format($amp->detail->sum('nominal'), 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr class="total-row"><td colspan="2" class="text-right">Total PS</td><td class="text-right">Rp {{ number_format($jadwal->amplop->sum(fn($a) => $a->detail->sum('nominal')), 0, ',', '.') }}</td></tr></tfoot>
    </table>
</div>
@endif

<p style="text-align:center;margin-top:30px;font-size:10px;color:#666">— Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} —</p>
</body></html>
