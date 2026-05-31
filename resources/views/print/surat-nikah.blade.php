<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<title>Surat Pernikahan — {{ $nikah->pria_nama }} & {{ $nikah->wanita_nama }}</title>
<style>
    body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #222; margin: 35px; line-height: 1.5; }
    .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 12px; margin-bottom: 15px; }
    .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; letter-spacing: 2px; }
    .header .resort { font-size: 10pt; color: #555; }
    .header .alamat { font-size: 9pt; color: #666; }
    .title { text-align: center; margin: 20px 0 5px; font-size: 14pt; font-weight: bold; text-decoration: underline; }
    .subtitle { text-align: center; font-size: 10pt; color: #555; margin-bottom: 15px; }
    h4 { margin: 15px 0 8px; font-size: 12pt; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
    table.data { width: 100%; border-collapse: collapse; margin: 8px 0; }
    table.data td { padding: 3px 8px; vertical-align: top; font-size: 11pt; }
    table.data td:first-child { width: 160px; font-weight: bold; }
    table.data td:nth-child(2) { width: 12px; text-align: center; }
    table.biaya { width: 100%; border-collapse: collapse; margin: 8px 0; border: 1px solid #999; }
    table.biaya th { background: #f0f0f0; border: 1px solid #999; padding: 5px 8px; text-align: left; font-size: 10pt; }
    table.biaya td { border: 1px solid #999; padding: 4px 8px; font-size: 10pt; }
    table.biaya .nominal { text-align: right; font-family: monospace; }
    .signatures { margin-top: 30px; width: 100%; }
    .signatures td { text-align: center; vertical-align: top; padding: 8px 10px; }
    .signatures .line { border-bottom: 1px solid #333; width: 140px; margin: 55px auto 4px; }
    .signatures .name { font-weight: bold; font-size: 11pt; }
    .signatures .title-sign { font-size: 9pt; color: #555; }
    .qr-code { margin-bottom: 3px; }
    .footer { margin-top: 20px; font-size: 8pt; color: #888; text-align: center; border-top: 1px solid #ccc; padding-top: 8px; }
    .page-break { page-break-before: always; }
</style>
</head><body>

{{-- PAGE 1: Surat Pernikahan --}}
<div class="header" style="position:relative">
    @if(file_exists(public_path('images/logo-gke.png')))
    <img src="{{ public_path('images/logo-gke.png') }}" style="position:absolute;left:0;top:0;width:70px;height:70px" alt="Logo GKE">
    @endif
    <h2>{{ $profil->nama_gereja ?? 'GEREJA SION' }}</h2>
    <div class="resort">{{ $settings['resort'] ?? 'Resort' }}</div>
    <div class="alamat">{{ $settings['alamat_gereja_lengkap'] ?? $profil->alamat ?? '' }}</div>
</div>

<div class="title">SURAT PEMBERKATAN PERNIKAHAN</div>
<div class="subtitle">No. {{ str_pad($nikah->id, 4, '0', STR_PAD_LEFT) }}/NIKAH/{{ now()->year }}</div>

<h4>I. Data Mempelai Pria</h4>
<table class="data">
    <tr><td>Nama</td><td>:</td><td>{{ $nikah->pria_nama }}</td></tr>
    <tr><td>Tempat / Tgl Lahir</td><td>:</td><td>{{ $nikah->pria_tempat_lahir }}, {{ $nikah->pria_tanggal_lahir?->format('d F Y') }}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{ $nikah->pria_alamat ?? '-' }}</td></tr>
    <tr><td>No. Telepon</td><td>:</td><td>{{ $nikah->pria_no_telepon ?? '-' }}</td></tr>
    <tr><td>Nama Ayah</td><td>:</td><td>{{ $nikah->pria_nama_ayah ?? '-' }}</td></tr>
    <tr><td>Nama Ibu</td><td>:</td><td>{{ $nikah->pria_nama_ibu ?? '-' }}</td></tr>
</table>

<h4>II. Data Mempelai Wanita</h4>
<table class="data">
    <tr><td>Nama</td><td>:</td><td>{{ $nikah->wanita_nama }}</td></tr>
    <tr><td>Tempat / Tgl Lahir</td><td>:</td><td>{{ $nikah->wanita_tempat_lahir }}, {{ $nikah->wanita_tanggal_lahir?->format('d F Y') }}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{ $nikah->wanita_alamat ?? '-' }}</td></tr>
    <tr><td>No. Telepon</td><td>:</td><td>{{ $nikah->wanita_no_telepon ?? '-' }}</td></tr>
    <tr><td>Nama Ayah</td><td>:</td><td>{{ $nikah->wanita_nama_ayah ?? '-' }}</td></tr>
    <tr><td>Nama Ibu</td><td>:</td><td>{{ $nikah->wanita_nama_ibu ?? '-' }}</td></tr>
</table>

<h4>III. Jadwal Pemberkatan</h4>
<table class="data">
    <tr><td>Tanggal</td><td>:</td><td>{{ $nikah->jadwal?->tanggal?->format('d F Y') ?? $nikah->tanggal_nikah?->format('d F Y') ?? '-' }}</td></tr>
    <tr><td>Waktu</td><td>:</td><td>{{ $nikah->jadwal?->waktu ? substr($nikah->jadwal->waktu, 0, 5) . ' WIB' : '-' }}</td></tr>
    <tr><td>Lokasi</td><td>:</td><td>{{ $nikah->jadwal?->lokasi ?? $profil->nama_gereja ?? 'Gereja SION' }}</td></tr>
</table>

<h4>IV. Rincian Biaya</h4>
<table class="biaya">
    <thead><tr><th>Uraian</th><th style="width:150px">Nominal</th></tr></thead>
    <tbody>
        <tr><td>Biaya Administrasi</td><td class="nominal">Rp {{ number_format($nikah->biaya_administrasi, 0, ',', '.') }}</td></tr>
        <tr><td>Biaya Fasilitas Gereja</td><td class="nominal">Rp {{ number_format($nikah->biaya_fasilitas, 0, ',', '.') }}</td></tr>
        @foreach($nikah->insentif as $ins)
        <tr><td>Insentif: {{ $ins->jenis_insentif }}</td><td class="nominal">Rp {{ number_format($ins->nominal, 0, ',', '.') }}</td></tr>
        @endforeach
        <tr style="font-weight:bold; background:#f5f5f5"><td>TOTAL</td><td class="nominal">Rp {{ number_format($nikah->total_biaya + $nikah->total_insentif, 0, ',', '.') }}</td></tr>
    </tbody>
</table>

<p style="text-align:right; margin-top: 20px">{{ $settings['kota'] ?? '' }}, {{ now()->translatedFormat('d F Y') }}</p>

@if($penandatangan->count())
<table class="signatures">
    <tr>
        @foreach($penandatangan as $p)
        <td>
            <div class="title-sign">{{ $p->jabatan_tampilan }}</div>
            @if(isset($qrCodes[$p->id]))
            <div class="qr-code"><img src="data:image/svg+xml;base64,{{ $qrCodes[$p->id] }}" width="75" height="75"></div>
            @else
            <div class="line"></div>
            @endif
            <div class="name">{{ $p->nama_tampilan }}</div>
        </td>
        @endforeach
    </tr>
</table>
@endif

<div class="footer">Dokumen ini dicetak secara digital oleh Sistem Informasi {{ $profil->nama_gereja ?? 'Gereja SION' }} pada {{ now()->format('d/m/Y H:i') }}</div>
</body></html>
