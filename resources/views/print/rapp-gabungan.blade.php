@include('print._styles')
<style>
    .up-header { background: #f5f0e1; padding: 8px 12px; font-weight: bold; font-size: 11px; border-top: 2px solid #333; margin-top: 15px; }
    .up-header:first-of-type { margin-top: 0; }
    .subtotal-row td { font-weight: bold; border-top: 1px solid #999; background: #fafaf5; }
</style>
</head><body>
@include('print._kop-surat')

<div class="title">RENCANA ANGGARAN PENDAPATAN DAN PENGELUARAN (RAPP)</div>
<div class="subtitle">TAHUN {{ $rapp->tahun }}</div>

{{-- A. PENERIMAAN --}}
<h4 style="font-size:12px;margin-top:20px;font-weight:bold">A. PENERIMAAN</h4>
<table class="data">
    <thead>
        <tr>
            <th style="width:30px">No</th>
            <th style="width:120px">Kode Akun</th>
            <th>Keterangan</th>
            <th style="width:80px" class="text-center">UP</th>
            <th class="text-right" style="width:150px">Target (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $noPen = 0; $totalPenerimaan = 0; $currentUp = null; $subTotal = 0; @endphp
        @foreach($penerimaan as $d)
            @if($currentUp !== null && $currentUp !== $d->detail_up_inisial)
            <tr class="subtotal-row">
                <td colspan="4" class="text-right" style="font-size:10px">Subtotal {{ $currentUp }}</td>
                <td class="text-right" style="font-size:10px">{{ number_format($subTotal, 0, ',', '.') }}</td>
            </tr>
            @php $subTotal = 0; @endphp
            @endif
            @php $currentUp = $d->detail_up_inisial; $noPen++; $totalPenerimaan += $d->nominal; $subTotal += $d->nominal; @endphp
        <tr>
            <td class="text-center" style="font-size:9px">{{ $noPen }}</td>
            <td style="font-size:9px"><code>{{ $d->kode }}</code></td>
            <td style="font-size:10px">{{ $d->keterangan ?: $d->nama_akun }}</td>
            <td class="text-center" style="font-size:9px">{{ $d->detail_up_inisial }}</td>
            <td class="text-right">{{ number_format($d->nominal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        @if($currentUp !== null)
        <tr class="subtotal-row">
            <td colspan="4" class="text-right" style="font-size:10px">Subtotal {{ $currentUp }}</td>
            <td class="text-right" style="font-size:10px">{{ number_format($subTotal, 0, ',', '.') }}</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">TOTAL PENERIMAAN</td>
            <td class="text-right">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- B. PENGELUARAN --}}
<h4 style="font-size:12px;margin-top:20px;font-weight:bold">B. PENGELUARAN</h4>
<table class="data">
    <thead>
        <tr>
            <th style="width:30px">No</th>
            <th style="width:120px">Kode Akun</th>
            <th>Keterangan</th>
            <th style="width:80px" class="text-center">UP</th>
            <th class="text-right" style="width:150px">Target (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $noKel = 0; $totalPengeluaran = 0; $currentUp = null; $subTotal = 0; @endphp
        @foreach($pengeluaran as $d)
            @if($currentUp !== null && $currentUp !== $d->detail_up_inisial)
            <tr class="subtotal-row">
                <td colspan="4" class="text-right" style="font-size:10px">Subtotal {{ $currentUp }}</td>
                <td class="text-right" style="font-size:10px">{{ number_format($subTotal, 0, ',', '.') }}</td>
            </tr>
            @php $subTotal = 0; @endphp
            @endif
            @php $currentUp = $d->detail_up_inisial; $noKel++; $totalPengeluaran += $d->nominal; $subTotal += $d->nominal; @endphp
        <tr>
            <td class="text-center" style="font-size:9px">{{ $noKel }}</td>
            <td style="font-size:9px"><code>{{ $d->kode }}</code></td>
            <td style="font-size:10px">{{ $d->keterangan ?: $d->nama_akun }}</td>
            <td class="text-center" style="font-size:9px">{{ $d->detail_up_inisial }}</td>
            <td class="text-right">{{ number_format($d->nominal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        @if($currentUp !== null)
        <tr class="subtotal-row">
            <td colspan="4" class="text-right" style="font-size:10px">Subtotal {{ $currentUp }}</td>
            <td class="text-right" style="font-size:10px">{{ number_format($subTotal, 0, ',', '.') }}</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">TOTAL PENGELUARAN</td>
            <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- REKAPITULASI --}}
<div style="border:2px solid #333;padding:10px;margin-top:15px">
    <h4 style="font-size:12px;font-weight:bold;text-align:center;margin-bottom:8px">REKAPITULASI RAPP {{ $rapp->tahun }}</h4>
    <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:11px">
        <span>Total Penerimaan</span>
        <span>Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:11px">
        <span>Total Pengeluaran</span>
        <span>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;border-top:2px solid #333;padding-top:6px;margin-top:6px;font-weight:bold;font-size:13px">
        <span>SELISIH (Surplus/Defisit)</span>
        <span>Rp {{ number_format($totalPenerimaan - $totalPengeluaran, 0, ',', '.') }}</span>
    </div>
</div>

{{-- Tanda tangan --}}
<div class="signature">
    <table>
        <tr>
            <td>
                {{ $settings['kota'] ?? 'Palangka Raya' }}, {{ now()->translatedFormat('d F Y') }}<br>
                {{ $settings['jabatan_bendahara'] ?? 'Bendahara' }}<br><br><br><br>
                <span class="nama">{{ $settings['nama_bendahara'] ?? '...' }}</span>
            </td>
            <td>&nbsp;</td>
            <td>
                Mengetahui,<br>
                {{ $settings['jabatan_ketua'] ?? 'Ketua MPH' }}<br><br><br><br>
                <span class="nama">{{ $settings['nama_ketua'] ?? $settings['nama_majelis'] ?? '...' }}</span>
            </td>
        </tr>
    </table>
</div>

</body></html>
