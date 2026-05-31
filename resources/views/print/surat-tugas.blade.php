<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
    @page { margin: 15mm 20mm; size: A4 }
    body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #1a1a1a; line-height: 1.5 }
    .kop { text-align: center; border-bottom: 3px double #c9a84c; padding-bottom: 10px; margin-bottom: 15px }
    .kop img { height: 60px; vertical-align: middle }
    .kop h2 { margin: 5px 0 2px; font-size: 16pt; text-transform: uppercase; letter-spacing: 1px }
    .kop h3 { margin: 0; font-size: 11pt; font-weight: normal }
    .kop p { margin: 3px 0; font-size: 9pt; color: #555 }
    .nomor { text-align: center; margin: 20px 0 5px; font-size: 10pt }
    .title { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin: 10px 0; border-bottom: 2px solid #333; padding-bottom: 5px; display: inline-block; width: 100% }
    .subtitle { text-align: center; font-size: 11pt; margin-bottom: 20px; color: #444 }
    table.data { width: 100%; border-collapse: collapse; margin: 15px 0 }
    table.data th, table.data td { border: 1px solid #999; padding: 6px 10px; font-size: 10pt }
    table.data th { background: #f5f0e0; font-weight: bold; text-align: center }
    table.data .text-right { text-align: right }
    table.data .text-center { text-align: center }
    .total-row { background: #f9f6ed; font-weight: bold }
    .info-box { background: #fafaf5; border: 1px solid #ddd; border-radius: 4px; padding: 10px; margin: 15px 0; font-size: 10pt }
    .info-box strong { color: #333 }
    .signature { margin-top: 40px }
    .signature table { width: 100%; border-collapse: collapse }
    .signature td { text-align: center; width: 50%; vertical-align: top; padding: 0 20px }
    .signature .nama { font-weight: bold; text-decoration: underline; display: inline-block; margin-top: 50px }
    .signature .jabatan { font-size: 9pt; color: #666 }
    .footer { text-align: center; font-size: 8pt; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 5px }
    @media print { .no-print { display: none } }
</style>
</head><body>
<div class="kop">
    @if(file_exists(public_path('images/logo-gke.png')))
    <img src="{{ public_path('images/logo-gke.png') }}" style="position:absolute;left:0;top:0;width:70px;height:70px" alt="Logo GKE">
    @endif
    <h2>{{ $profil->nama_gereja ?? 'Gereja Kalimantan Evangelis' }}</h2>
    <h3>{{ $settings['resort'] ?? 'Resort Palangka Raya Tengah' }}</h3>
    <p>{{ $settings['alamat_gereja_lengkap'] ?? $profil->alamat ?? '' }}</p>
    <p>Telp. {{ $settings['telepon_gereja'] ?? '-' }} | Email: {{ $settings['email_gereja'] ?? '-' }}</p>
</div>

<div class="nomor">No. Surat: _____ / ST-PLY / {{ $profil->nama_gereja ?? 'GKE' }} / {{ now()->format('m/Y') }}</div>
<div class="title">SURAT TUGAS PELAYANAN</div>
<div class="subtitle">{{ $jadwal->judul_ibadah ?: $jadwal->jenis_ibadah }} — {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</div>

<p style="text-indent:30px">Yang bertanda tangan di bawah ini, Ketua Majelis {{ $profil->nama_gereja ?? 'Gereja' }}, dengan ini menugaskan petugas-petugas di bawah ini untuk melaksanakan pelayanan ibadah sebagai berikut:</p>

<div class="info-box">
    <table style="width:100%;border:none">
        <tr><td style="width:120px"><strong>Hari/Tanggal</strong></td><td>: {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</td></tr>
        <tr><td><strong>Waktu</strong></td><td>: {{ $jadwal->waktu ? substr($jadwal->waktu,0,5).' WIB' : '-' }}</td></tr>
        <tr><td><strong>Lokasi</strong></td><td>: {{ $jadwal->lokasi ?? '-' }}</td></tr>
        @if($jadwal->keterangan)<tr><td><strong>Keterangan</strong></td><td>: {{ $jadwal->keterangan }}</td></tr>@endif
    </table>
</div>

<table class="data">
    <thead><tr><th style="width:35px">No</th><th>Nama Petugas</th><th>Jenis Tugas</th><th style="width:130px" class="text-right">Tali Kasih (Rp)</th></tr></thead>
    <tbody>
        @foreach($jadwal->petugas as $i => $jp)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td><strong>@if($jp->is_external){{ $jp->nama_external }} (Luar)@else{{ $jp->petugas?->nama_petugas ?? $jp->nama_external }}{{ $jp->petugas?->is_pendeta_sion ? ' (Sion)' : '' }}@endif</strong></td>
            <td>{{ $jp->jenis_tugas }}</td>
            <td class="text-right">{{ number_format($jp->nominal_insentif, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="text-right">Total Tali Kasih</td>
            <td class="text-right">Rp {{ number_format($jadwal->petugas->sum('nominal_insentif'), 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<p style="font-size:10pt">Demikian surat tugas ini dibuat untuk dapat dilaksanakan dengan sebaik-baiknya. Kiranya Tuhan memberkati pelayanan ini.</p>

<div class="signature">
    <table><tr>
        <td>{{ $settings['kota'] ?? 'Palangka Raya' }}, {{ $jadwal->tanggal->translatedFormat('d F Y') }}<br>{{ $settings['jabatan_ketua'] ?? 'Ketua MPH' }}<br><span class="nama">{{ $settings['nama_ketua'] ?? $settings['nama_majelis'] ?? '...........................' }}</span></td>
        <td>Mengetahui,<br>{{ $settings['jabatan_bendahara'] ?? 'Bendahara' }}<br><span class="nama">{{ $settings['nama_bendahara'] ?? '...........................' }}</span></td>
    </tr></table>
</div>

<div class="footer">Dokumen ini dicetak secara otomatis oleh Sistem Informasi {{ $profil->nama_gereja ?? 'Gereja' }}</div>
</body></html>
