@include('print._styles')
<style>body{font-family:'Times New Roman',serif;font-size:10pt;margin:10mm}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:3px 5px;font-size:9pt}th{background:#f0f0f0;font-weight:bold;text-align:center}.text-right{text-align:right}.bold{font-weight:bold}h2{text-align:center;font-size:12pt}</style>

<h2>REKAP KANTONG KOLEKTE</h2>
<p style="text-align:center;font-weight:bold">Bulan: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>

<table>
    <thead><tr><th>No</th><th>Tanggal</th><th>Jenis Ibadah</th><th>Minggu Ke</th><th>Jml Kantong</th><th>Petugas Hitung</th><th class="text-right">Jumlah (Rp)</th></tr></thead>
    <tbody>
        @foreach($data as $i => $d)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $d->tanggal->format('d/m/Y') }}</td>
            <td>{{ $d->judul_ibadah ?? $d->jenis_ibadah }}</td>
            <td class="text-center">{{ $d->minggu_ke }}</td>
            <td class="text-center">{{ $d->jml_kantong }}</td>
            <td>{{ $d->petugas_hitung ?? '-' }}</td>
            <td class="text-right">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="bold"><td colspan="6" class="text-right">TOTAL</td><td class="text-right">{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</td></tr>
    </tbody>
</table>
