<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')</head><body>
@include('print._kop-surat')
<div class="title">LAPORAN PENGELUARAN (ZA-15)</div>
<div class="subtitle">Bulan {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</div>

<table class="data">
    <thead><tr><th style="width:30px">No</th><th style="width:140px">Kode Akun</th><th>Nama Akun</th><th class="text-right" style="width:150px">Jumlah (Rp)</th></tr></thead>
    <tbody>
        @forelse($data as $i => $d)
        <tr><td class="text-center">{{ $i+1 }}</td><td>{{ $d->kode }}</td><td>{{ $d->nama }}</td><td class="text-right">{{ number_format($d->jumlah, 0, ',', '.') }}</td></tr>
        @empty
        <tr><td colspan="4" class="text-center">Tidak ada pengeluaran</td></tr>
        @endforelse
    </tbody>
    <tfoot><tr class="total-row"><td colspan="3" class="text-right">TOTAL PENGELUARAN</td><td class="text-right">Rp {{ number_format($data->sum('jumlah'), 0, ',', '.') }}</td></tr></tfoot>
</table>
<div class="signature"><table><tr><td>{{ $settings['kota'] ?? '' }}, {{ now()->translatedFormat('d F Y') }}<br>Bendahara<br><br><br><br><span class="nama">{{ $settings['nama_bendahara'] ?? '...' }}</span></td><td>&nbsp;</td><td>Mengetahui,<br>Ketua Majelis<br><br><br><br><span class="nama">{{ $settings['nama_majelis'] ?? '...' }}</span></td></tr></table></div>
</body></html>
