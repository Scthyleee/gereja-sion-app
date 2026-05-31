@include('print._styles')
<style>
    body{font-family:'Times New Roman',serif;font-size:10pt;margin:15mm}
    table{width:100%;border-collapse:collapse}
    th,td{border:1px solid #000;padding:3px 6px;font-size:9pt}
    th{background:#f0f0f0;font-weight:bold;text-align:center}
    .no-border td,.no-border th{border:none}
    .text-right{text-align:right}
    .text-center{text-align:center}
    .bold{font-weight:bold}
    .section-title{font-weight:bold;text-decoration:underline;margin:10px 0 5px}
    h2{text-align:center;font-size:12pt;margin-bottom:3px}
    .period{text-align:center;font-size:10pt;font-weight:bold;margin-bottom:10px}
</style>

{{-- PAGE 1: Penerimaan & Pengeluaran --}}
@include('print._kop-surat')
<h2>PENERIMAAN DAN PENGELUARAN KEUANGAN MAJELIS JEMAAT GKE PALANGKA</h2>
<div class="period">{{ \Carbon\Carbon::parse($tanggalAwal)->format('d F Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y') }}</div>

<table>
    <thead>
        <tr><th style="width:12%">Tanggal</th><th>Penerimaan dan Pengeluaran</th><th style="width:15%" class="text-right">Total Rp</th></tr>
    </thead>
    <tbody>
        {{-- A. PENERIMAAN --}}
        <tr><td></td><td class="bold">A. PENERIMAAN :</td><td></td></tr>

        {{-- KK entries --}}
        @foreach($kkPeriode as $kk)
        <tr>
            <td class="text-center">{{ $kk->tanggal->format('d/m/Y') }}</td>
            <td>{{ $kk->judul_ibadah ?? $kk->jenis_ibadah }}</td>
            <td class="text-right">{{ number_format($kk->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach

        {{-- PS entries grouped by jemaat (from kantor: show name, from jadwal: group by jenis) --}}
        @php
            $psKantor = $psPeriode->where('sumber', 'kantor')->groupBy('jemaat_id');
            $psJadwal = $psPeriode->where('sumber', 'jadwal')->groupBy(fn($i) => $i->tanggal->format('d/m/Y'));
        @endphp

        @foreach($psKantor as $jemaatId => $items)
        @php $jemaat = $items->first()->jemaat; @endphp
        <tr><td class="text-center">{{ $items->first()->tanggal->format('d/m/Y') }}</td><td class="bold">PS dari Kel {{ $jemaat?->nama_lengkap ?? 'N/A' }} ({{ \Carbon\Carbon::parse($items->first()->bulan)->translatedFormat('F Y') }})</td><td></td></tr>
        @foreach($items as $ps)
        <tr><td></td><td style="padding-left:20px">{{ $ps->jenis_ps }}</td><td class="text-right">{{ number_format($ps->jumlah, 0, ',', '.') }}</td></tr>
        @endforeach
        @endforeach

        @foreach($psJadwal as $tgl => $items)
        @php $grouped = $items->groupBy('jenis_ps'); @endphp
        @foreach($grouped as $jenis => $jenisItems)
        <tr>
            <td class="text-center">{{ $tgl }}</td>
            <td>{{ $jenis }} ({{ $jenisItems->count() }}) KK</td>
            <td class="text-right">{{ number_format($jenisItems->sum('jumlah'), 0, ',', '.') }}</td>
        </tr>
        @endforeach
        @endforeach

        <tr class="bold"><td colspan="2" class="text-right">TOTAL PENERIMAAN :</td><td class="text-right">{{ number_format($totalPenerimaan, 0, ',', '.') }}</td></tr>

        {{-- B. PENGELUARAN --}}
        <tr><td></td><td class="bold">B. PENGELUARAN:</td><td></td></tr>
        @foreach($pengeluaran as $p)
        <tr>
            <td class="text-center">{{ $p->tanggal->format('d/m/Y') }}</td>
            <td>{{ $p->keterangan ?? $p->kodeAkun?->nama }}</td>
            <td class="text-right">{{ number_format($p->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="bold"><td colspan="2" class="text-right">TOTAL PENGELUARAN :</td><td class="text-right">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr>

        {{-- REKAPITULASI --}}
        <tr><td colspan="3"></td></tr>
        <tr class="bold"><td colspan="2">REKAPITULASI TOTAL</td><td></td></tr>
        <tr class="bold"><td colspan="2">SALDO MINGGU LALU</td><td class="text-right">{{ number_format($saldoMingguLalu, 0, ',', '.') }}</td></tr>
        <tr class="bold"><td colspan="2">TOTAL PENERIMAAN</td><td class="text-right">{{ number_format($totalPenerimaan, 0, ',', '.') }}</td></tr>
        <tr class="bold"><td colspan="2">TOTAL PENGELUARAN</td><td class="text-right">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr>
        <tr class="bold"><td colspan="2" class="text-right">SALDO AKHIR</td><td class="text-right">{{ number_format($saldoMingguLalu + $totalPenerimaan - $totalPengeluaran, 0, ',', '.') }}</td></tr>
        <tr class="bold"><td colspan="2" class="text-right">SALDO/KAS PER {{ \Carbon\Carbon::parse($tanggalAwal)->format('d F Y') }}</td><td class="text-right">{{ number_format($saldoMingguLalu + $totalPenerimaan - $totalPengeluaran, 0, ',', '.') }}</td></tr>

        {{-- Breakdown per kantong --}}
        <tr><td colspan="3"></td></tr>
        <tr class="bold"><td colspan="2">TERDIRI DARI:</td><td></td></tr>
        @foreach($saldoData as $s)
        <tr class="bold"><td colspan="2">{{ $s->label }}</td><td class="text-right">{{ number_format($s->akhir, 0, ',', '.') }}</td></tr>
        @endforeach
    </tbody>
</table>

{{-- PAGE 2: Amplop PS per ibadah (if jadwal data exists) --}}
@if($amplopPerIbadah->count())
<div style="page-break-before:always"></div>
@php
$jenisColumns = DB::table('jenis_ps_config')->where('aktif', true)->orderBy('urutan')->pluck('nama')->toArray();
@endphp

@foreach($amplopPerIbadah as $key => $items)
@php
    $parts = explode('|', $key);
    $tgl = $parts[0];
    $jadwalId = $parts[1] ?? '';
    $jadwal = $jadwalId && $jadwalId !== 'kantor' ? \App\Models\JadwalIbadah::find($jadwalId) : null;
    $label = $jadwal ? ($jadwal->judul_ibadah ?: $jadwal->jenis_ibadah) . ' ' . $jadwal->waktu : 'Ibadah';
    // Group by jemaat
    $byJemaat = $items->groupBy('jemaat_id');
@endphp

<h2 style="font-size:10pt;margin-top:20px">AMPLOP PERSEMBAHAN SYUKUR ( TGL {{ \Carbon\Carbon::parse($tgl)->format('d F Y') }} PKL {{ $jadwal?->waktu ?? '08.00' }} WIB)</h2>
<table style="font-size:7pt">
    <thead>
        <tr>
            <th>No</th><th>NAMA</th>
            @foreach($jenisColumns as $col)<th>{{ $col }}</th>@endforeach
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; $colTotals = array_fill(0, count($jenisColumns), 0); $grandTotal = 0; @endphp
        @foreach($byJemaat as $jId => $jItems)
        @php
            $nama = $jItems->first()->jemaat?->nama_lengkap ?? 'N/A';
            $rowTotal = $jItems->sum('jumlah');
            $grandTotal += $rowTotal;
        @endphp
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $nama }}</td>
            @foreach($jenisColumns as $ci => $col)
            @php $val = $jItems->where('jenis_ps', $col)->sum('jumlah'); $colTotals[$ci] += $val; @endphp
            <td class="text-right">{{ $val > 0 ? number_format($val, 0, ',', '.') : '' }}</td>
            @endforeach
            <td class="text-right bold">{{ number_format($rowTotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="bold">
            <td colspan="2" class="text-right">Jumlah</td>
            @foreach($colTotals as $ct)
            <td class="text-right">{{ $ct > 0 ? number_format($ct, 0, ',', '.') : '-' }}</td>
            @endforeach
            <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
@endforeach
@endif
