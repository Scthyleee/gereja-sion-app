<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
    @page { margin: 10mm; size: A4 }
    body { font-family: 'Times New Roman', serif; font-size: 10pt; color: #1a1a1a }
    .amplop { border: 2px solid #c9a84c; border-radius: 8px; padding: 20px 25px; margin-bottom: 20px; page-break-inside: avoid; background: #fff }
    .amplop-kop { text-align: center; font-size: 9pt; border-bottom: 2px solid #c9a84c; padding-bottom: 8px; margin-bottom: 12px }
    .amplop-kop .nama { font-weight: bold; font-size: 11pt; letter-spacing: 1px; color: #333 }
    .amplop-kop .sub { font-size: 8pt; color: #888; margin-top: 2px }
    .amplop-info { font-size: 10pt; line-height: 1.8 }
    .amplop-info strong { color: #333; display: inline-block; width: 120px }
    .amplop-nominal { text-align: center; margin: 15px 0; padding: 12px; background: #f9f6ed; border: 1px dashed #c9a84c; border-radius: 6px }
    .amplop-nominal .label { font-size: 9pt; color: #888; text-transform: uppercase; letter-spacing: 1px }
    .amplop-nominal .value { font-size: 18pt; font-weight: bold; color: #c9a84c }
    .amplop-nominal .terbilang { font-size: 8pt; color: #666; font-style: italic; margin-top: 4px }
    .amplop-footer { text-align: right; font-size: 9pt; color: #999; margin-top: 10px; border-top: 1px dotted #ddd; padding-top: 5px }
    .cut-line { border-bottom: 1px dashed #ccc; margin: 8px 0; position: relative }
    .cut-line::after { content: '✂'; position: absolute; right: 0; top: -8px; color: #ccc; font-size: 12pt }
    @media print { .no-print { display: none } }
</style>
</head><body>

@foreach($jadwal->petugas->where('kategori', 'insentif')->where('nominal_insentif', '>', 0) as $jp)
<div class="amplop">
    <div class="amplop-kop">
        <div class="nama">{{ strtoupper($profil->nama_gereja ?? 'GEREJA KALIMANTAN EVANGELIS') }}</div>
        <div class="sub">{{ $settings['resort'] ?? 'Resort Palangka Raya Tengah' }}</div>
        <div class="sub">{{ $settings['alamat_gereja_lengkap'] ?? '' }}</div>
    </div>
    
    <div class="amplop-info">
        <strong>Kepada Yth.</strong>
        @if($jp->is_external)
        {{ $jp->nama_external }} (Luar)
        @else
        {{ $jp->petugas->nama_petugas ?? $jp->nama_external }}{{ $jp->petugas && $jp->petugas->is_pendeta_sion ? ' (Sion)' : '' }}
        @endif
        <br>
        <strong>Tugas</strong> {{ $jp->jenis_tugas }}<br>
        <strong>Ibadah</strong> {{ $jadwal->judul_ibadah ?: $jadwal->jenis_ibadah }}<br>
        <strong>Tanggal</strong> {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}<br>
        @if($jadwal->waktu)<strong>Waktu</strong> {{ substr($jadwal->waktu,0,5) }} WIB<br>@endif
        <strong>Lokasi</strong> {{ $jadwal->lokasi ?? '-' }}
    </div>

    <div class="amplop-nominal">
        <div class="label">Tali Kasih</div>
        <div class="value">Rp {{ number_format($jp->nominal_insentif, 0, ',', '.') }}</div>
        <div class="terbilang">( {{ terbilang($jp->nominal_insentif) }} rupiah )</div>
    </div>

    <div class="amplop-footer">
        {{ $settings['kota'] ?? 'Palangka Raya' }}, {{ $jadwal->tanggal->translatedFormat('d F Y') }}
        — Bendahara: {{ $settings['nama_bendahara'] ?? '_____________' }}
    </div>
</div>
@if(!$loop->last)<div class="cut-line"></div>@endif
@endforeach

</body></html>
