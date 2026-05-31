@include('print._styles')
<style>
    .rekap-box { border: 2px solid #333; padding: 10px; margin-top: 15px; }
    .rekap-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 11px; }
    .rekap-total { border-top: 2px solid #333; padding-top: 6px; margin-top: 6px; font-weight: bold; font-size: 13px; }
    .sign-block { text-align: right; margin-top: 30px; font-size: 10px; }
    .sign-name { margin-top: 50px; font-weight: bold; text-decoration: underline; }
    .amplop-family { margin-bottom: 6px; padding-left: 8px; border-left: 3px solid #c9a84c; font-size: 10px; }
    .amplop-family strong { font-size: 11px; }
    .amplop-item { color: #555; padding-left: 15px; }
</style>
</head><body>
@include('print._kop-surat')

<div class="title">PENERIMAAN DAN PENGELUARAN KEUANGAN</div>
<div class="subtitle">Periode {{ $warta->tanggal_awal->translatedFormat('d F') }} — {{ $warta->tanggal_akhir->translatedFormat('d F Y') }}</div>

{{-- A. PENERIMAAN --}}
<h4 style="font-size:12px;margin-top:20px;font-weight:bold">A. PENERIMAAN</h4>
<table class="data">
    <thead>
        <tr><th style="width:80px">Tanggal</th><th style="width:120px">Kode Akun</th><th>Keterangan</th><th class="text-right" style="width:130px">Total (Rp)</th></tr>
    </thead>
    <tbody>
        @forelse($penerimaan as $p)
        <tr>
            <td style="font-size:9px">{{ $p->tanggal->format('d/m') }}</td>
            <td style="font-size:9px"><code>{{ $p->kodeAkun->kode ?? '-' }}</code></td>
            <td>{{ $p->keterangan ?: ($p->kodeAkun->keterangan ?? $p->kodeAkun->nama ?? '-') }}</td>
            <td class="text-right">{{ number_format($p->jumlah, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center" style="color:#999">— Tidak ada penerimaan —</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="text-right">TOTAL PENERIMAAN</td>
            <td class="text-right">{{ number_format($warta->total_penerimaan, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- B. PENGELUARAN --}}
<h4 style="font-size:12px;margin-top:15px;font-weight:bold">B. PENGELUARAN</h4>
<table class="data">
    <thead>
        <tr><th style="width:80px">Tanggal</th><th style="width:120px">Kode Akun</th><th>Keterangan</th><th class="text-right" style="width:130px">Total (Rp)</th></tr>
    </thead>
    <tbody>
        @forelse($pengeluaran as $p)
        <tr>
            <td style="font-size:9px">{{ $p->tanggal->format('d/m') }}</td>
            <td style="font-size:9px"><code>{{ $p->kodeAkun->kode ?? '-' }}</code></td>
            <td>{{ $p->keterangan ?: ($p->kodeAkun->keterangan ?? $p->kodeAkun->nama ?? '-') }}</td>
            <td class="text-right">{{ number_format($p->jumlah, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center" style="color:#999">— Tidak ada pengeluaran —</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="text-right">TOTAL PENGELUARAN</td>
            <td class="text-right">{{ number_format($warta->total_pengeluaran, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- B2. RINCIAN INSENTIF PER IBADAH --}}
@if(isset($insentifPerJadwal) && $insentifPerJadwal->count())
<h4 style="font-size:12px;margin-top:15px;font-weight:bold">B2. RINCIAN INSENTIF PER IBADAH</h4>
@foreach($insentifPerJadwal as $jadwalId => $petugasList)
@php
    $first = $petugasList->first();
    $tgl = \Carbon\Carbon::parse($first->tanggal);
    $subtotal = $petugasList->sum('nominal_insentif');
@endphp
<div style="margin-bottom:8px;padding:4px 8px;border-left:3px solid #e74c3c;font-size:10px">
    <strong>Insentif {{ $first->judul_ibadah ?? $first->jenis_ibadah }} {{ $first->waktu ? '('.$first->waktu.')' : '' }} — {{ $tgl->translatedFormat('d M Y') }}</strong>
    <span style="float:right;font-weight:bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
    <br>
    @foreach($petugasList as $pt)
    <div style="padding-left:12px;color:#555">• {{ $pt->jenis_tugas }}: {{ $pt->nama_petugas }} ({{ $pt->asal }}) — Rp {{ number_format($pt->nominal_insentif, 0, ',', '.') }}</div>
    @endforeach
</div>
@endforeach
@endif

{{-- REKAPITULASI --}}
<div class="rekap-box">
    <h4 style="font-size:12px;font-weight:bold;text-align:center;margin-bottom:8px">REKAPITULASI</h4>
    <div class="rekap-row">
        <span>Saldo Awal</span>
        <span>Rp {{ number_format($warta->saldo_awal_minggu, 0, ',', '.') }}</span>
    </div>
    <div class="rekap-row">
        <span>Jumlah Penerimaan (+)</span>
        <span>Rp {{ number_format($warta->total_penerimaan, 0, ',', '.') }}</span>
    </div>
    <div class="rekap-row">
        <span>Jumlah Pengeluaran (−)</span>
        <span>Rp {{ number_format($warta->total_pengeluaran, 0, ',', '.') }}</span>
    </div>
    <div class="rekap-row rekap-total">
        <span>SALDO AKHIR</span>
        <span>Rp {{ number_format($warta->saldo_akhir_minggu, 0, ',', '.') }}</span>
    </div>
</div>

{{-- C. AMPLOP PERSEMBAHAN SYUKUR --}}
@if(count($amplopPS) > 0)
<div style="page-break-before:always"></div>
@include('print._kop-surat')
<h4 style="font-size:12px;margin-top:15px;font-weight:bold;text-align:center;text-decoration:underline">C. AMPLOP PERSEMBAHAN SYUKUR</h4>
<p style="font-size:10px;text-align:center;margin-bottom:10px">Periode {{ $warta->tanggal_awal->translatedFormat('d F') }} — {{ $warta->tanggal_akhir->translatedFormat('d F Y') }}</p>

@php $grandTotal = 0; @endphp
@foreach($amplopPS as $jemaatId => $items)
@php
    $nama = $items->first()->nama_lengkap ?? 'N/A';
    $familyTotal = $items->sum('total');
    $grandTotal += $familyTotal;
@endphp
<div class="amplop-family">
    <strong>{{ $loop->iteration }}. {{ $nama }}</strong> — <strong>Rp {{ number_format($familyTotal, 0, ',', '.') }}</strong>
    @foreach($items as $item)
    <div class="amplop-item">• {{ $item->jenis_ps }}: Rp {{ number_format($item->total, 0, ',', '.') }}</div>
    @endforeach
</div>
@endforeach

<div style="border-top:2px solid #333;padding-top:5px;margin-top:10px;font-weight:bold;text-align:right;font-size:12px">
    TOTAL AMPLOP PS: Rp {{ number_format($grandTotal, 0, ',', '.') }}
</div>
@endif

{{-- Tanda tangan --}}
<div class="sign-block">
    <p>{{ $settings['kota'] ?? 'Palangka Raya' }}, {{ $warta->tanggal_akhir->translatedFormat('d F Y') }}</p>
    <p>Bendahara Majelis Jemaat</p>
    <p class="sign-name">{{ $settings['nama_bendahara'] ?? '' }}</p>
</div>

</body></html>
