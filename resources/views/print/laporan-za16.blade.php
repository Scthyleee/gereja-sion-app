<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')</head><body>
@include('print._kop-surat')
<div class="title">NERACA SALDO / LAPORAN KEUANGAN (ZA-16)</div>
<div class="subtitle">Tahun {{ $tahun }}</div>

<table class="data">
    <thead><tr><th>Kantong Dana</th><th class="text-right">Saldo Awal</th><th class="text-right">Penerimaan</th><th class="text-right">Pengeluaran</th><th class="text-right">Saldo Akhir</th></tr></thead>
    <tbody>
        @php $tA=0;$tM=0;$tK=0;$tAk=0; @endphp
        @foreach($saldoData as $s)
        @php $tA+=$s->awal;$tM+=$s->masuk;$tK+=$s->keluar;$tAk+=$s->akhir; @endphp
        <tr>
            <td class="fw-bold">{{ $s->label }}</td>
            <td class="text-right">{{ number_format($s->awal, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($s->masuk, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($s->keluar, 0, ',', '.') }}</td>
            <td class="text-right fw-bold">{{ number_format($s->akhir, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot><tr class="total-row">
        <td class="fw-bold">TOTAL</td>
        <td class="text-right">{{ number_format($tA, 0, ',', '.') }}</td>
        <td class="text-right">{{ number_format($tM, 0, ',', '.') }}</td>
        <td class="text-right">{{ number_format($tK, 0, ',', '.') }}</td>
        <td class="text-right">{{ number_format($tAk, 0, ',', '.') }}</td>
    </tr></tfoot>
</table>
<div class="signature"><table><tr><td>{{ $settings['kota'] ?? '' }}, {{ now()->translatedFormat('d F Y') }}<br>Bendahara<br><br><br><br><span class="nama">{{ $settings['nama_bendahara'] ?? '...' }}</span></td><td>&nbsp;</td><td>Mengetahui,<br>Ketua Majelis<br><br><br><br><span class="nama">{{ $settings['nama_majelis'] ?? '...' }}</span></td></tr></table></div>
</body></html>
