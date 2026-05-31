@include('print._styles')
<style>body{font-family:'Times New Roman',serif;font-size:10pt;margin:10mm}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:3px 5px;font-size:8pt}th{background:#f0f0f0;font-weight:bold;text-align:center}.text-right{text-align:right}.bold{font-weight:bold}h2{text-align:center;font-size:12pt}</style>

<h2>REKAP PERSEMBAHAN SYUKUR</h2>
<p style="text-align:center;font-weight:bold">Bulan: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>

<table>
    <thead><tr><th>No</th><th>Tanggal</th><th>Nama Jemaat</th><th>Jenis PS</th><th>Kode Akun</th><th>No. Kuitansi</th><th class="text-right">Jumlah (Rp)</th></tr></thead>
    <tbody>
        @foreach($data as $i => $d)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $d->tanggal->format('d/m/Y') }}</td>
            <td>{{ $d->jemaat?->nama_lengkap ?? '-' }}</td>
            <td>{{ $d->jenis_ps }}</td>
            <td>{{ $d->kodeAkun?->kode ?? '-' }}</td>
            <td>{{ $d->no_kuitansi }}</td>
            <td class="text-right">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="bold"><td colspan="6" class="text-right">TOTAL</td><td class="text-right">{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</td></tr>
    </tbody>
</table>

@if($summary->count())
<h3 style="margin-top:15px;font-size:10pt">Ringkasan per Jenis PS:</h3>
<table style="width:50%">
    <thead><tr><th>Jenis PS</th><th class="text-right">Total</th><th class="text-right">Jumlah Trx</th></tr></thead>
    <tbody>
        @foreach($summary as $s)
        <tr><td>{{ $s->jenis_ps }}</td><td class="text-right">{{ number_format($s->total, 0, ',', '.') }}</td><td class="text-right">{{ $s->cnt }}</td></tr>
        @endforeach
    </tbody>
</table>
@endif
