<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<title>Surat Baptisan Kudus — {{ $bapti->nama_lengkap }}</title>
<style>
    body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #222; margin: 40px; line-height: 1.6; }
    .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
    .header h2 { margin: 0; font-size: 18pt; text-transform: uppercase; letter-spacing: 2px; }
    .header h3 { margin: 5px 0 0; font-size: 14pt; font-weight: normal; }
    .header .resort { font-size: 10pt; color: #555; margin-top: 3px; }
    .header .alamat { font-size: 9pt; color: #666; }
    .title { text-align: center; margin: 25px 0; font-size: 16pt; font-weight: bold; text-decoration: underline; letter-spacing: 1px; }
    .subtitle { text-align: center; font-size: 11pt; color: #555; margin-bottom: 20px; }
    table.data { width: 100%; border-collapse: collapse; margin: 15px 0; }
    table.data td { padding: 5px 10px; vertical-align: top; }
    table.data td:first-child { width: 180px; font-weight: bold; }
    table.data td:nth-child(2) { width: 15px; text-align: center; }
    .content { margin: 20px 0; text-align: justify; }
    .signatures { margin-top: 40px; width: 100%; }
    .signatures td { text-align: center; vertical-align: top; padding: 10px 15px; width: 33%; }
    .signatures .line { border-bottom: 1px solid #333; width: 150px; margin: 60px auto 5px; }
    .signatures .name { font-weight: bold; }
    .signatures .title-sign { font-size: 10pt; color: #555; }
    .qr-code { margin-bottom: 5px; }
    .footer { margin-top: 30px; font-size: 9pt; color: #888; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; }
</style>
</head><body>

<div class="header" style="position:relative">
    @if(file_exists(public_path('images/logo-gke.png')))
    <img src="{{ public_path('images/logo-gke.png') }}" style="position:absolute;left:0;top:0;width:70px;height:70px" alt="Logo GKE">
    @endif
    <h2>{{ $profil->nama_gereja ?? 'GEREJA SION' }}</h2>
    <div class="resort">{{ $settings['resort'] ?? 'Resort' }}</div>
    <div class="alamat">{{ $settings['alamat_gereja_lengkap'] ?? $profil->alamat ?? '' }}</div>
</div>

<div class="title">SURAT BAPTISAN KUDUS</div>
<div class="subtitle">No. Reg: {{ str_pad($bapti->id, 4, '0', STR_PAD_LEFT) }}/BAPTIS/{{ now()->year }}</div>

<div class="content">
    <p>Dengan ini menerangkan bahwa telah dibaptis secara kudus menurut tata cara Gereja:</p>
</div>

<table class="data">
    <tr><td>Nama Lengkap</td><td>:</td><td>{{ $bapti->nama_lengkap }}</td></tr>
    <tr><td>Tempat / Tanggal Lahir</td><td>:</td><td>{{ $bapti->tempat_lahir }}, {{ $bapti->tanggal_lahir?->format('d F Y') }}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{ $bapti->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
    <tr><td>Nama Ayah</td><td>:</td><td>{{ $bapti->nama_ayah ?? '-' }}</td></tr>
    <tr><td>Nama Ibu</td><td>:</td><td>{{ $bapti->nama_ibu ?? '-' }}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{ $bapti->alamat ?? '-' }}</td></tr>
    <tr><td>Lingkungan</td><td>:</td><td>{{ $bapti->lingkunganSion?->nama ?? '-' }}</td></tr>
    <tr><td>Tanggal Baptis</td><td>:</td><td>{{ $bapti->jadwal?->tanggal?->format('d F Y') ?? '-' }}</td></tr>
    <tr><td>Status</td><td>:</td><td style="font-weight:bold; color: #2e7d32">{{ strtoupper($bapti->status) }}</td></tr>
</table>

<div class="content">
    <p>Demikian surat ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    <p style="text-align:right">{{ $settings['kota'] ?? '' }}, {{ now()->translatedFormat('d F Y') }}</p>
</div>

@if($penandatangan->count())
<table class="signatures">
    <tr>
        @foreach($penandatangan as $p)
        <td>
            <div class="title-sign">{{ $p->jabatan_tampilan }}</div>
            @if(isset($qrCodes[$p->id]))
            <div class="qr-code"><img src="data:image/svg+xml;base64,{{ $qrCodes[$p->id] }}" width="80" height="80"></div>
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
