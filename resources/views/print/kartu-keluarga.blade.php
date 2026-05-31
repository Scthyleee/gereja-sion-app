<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<title>Kartu Keluarga Jemaat — {{ $keluarga->nama_kepala_keluarga }}</title>
<style>
    body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #222; margin: 35px; line-height: 1.4; }
    .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 12px; margin-bottom: 15px; }
    .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; letter-spacing: 2px; }
    .header .alamat { font-size: 9pt; color: #666; }
    .title { text-align: center; margin: 15px 0 5px; font-size: 14pt; font-weight: bold; text-decoration: underline; }
    .subtitle { text-align: center; font-size: 10pt; color: #555; margin-bottom: 15px; }
    table.info { width: 100%; border-collapse: collapse; margin: 10px 0; }
    table.info td { padding: 3px 8px; vertical-align: top; }
    table.info td:first-child { width: 160px; font-weight: bold; }
    table.info td:nth-child(2) { width: 12px; text-align: center; }
    table.anggota { width: 100%; border-collapse: collapse; margin: 10px 0; border: 1px solid #999; }
    table.anggota th { background: #e8e0d4; border: 1px solid #999; padding: 5px 8px; text-align: center; font-size: 9pt; text-transform: uppercase; }
    table.anggota td { border: 1px solid #999; padding: 4px 6px; font-size: 9.5pt; }
    table.anggota td.center { text-align: center; }
    h4 { margin: 15px 0 5px; font-size: 12pt; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
    .footer { margin-top: 20px; font-size: 8pt; color: #888; text-align: center; border-top: 1px solid #ccc; padding-top: 8px; }
</style>
</head><body>

<div class="header" style="position:relative">
    @if(file_exists(public_path('images/logo-gke.png')))
    <img src="{{ public_path('images/logo-gke.png') }}" style="position:absolute;left:0;top:0;width:70px;height:70px" alt="Logo GKE">
    @endif
    <h2>{{ $profil->nama_gereja ?? 'GEREJA SION' }}</h2>
    <div class="alamat">{{ $profil->alamat ?? '' }}</div>
</div>

<div class="title">FORMULIR PENDAFTARAN KELUARGA</div>
<div class="subtitle">No. KK: {{ $keluarga->no_kk ?? '-' }}</div>

<h4>Data Keluarga</h4>
<table class="info">
    <tr><td>Kepala Keluarga</td><td>:</td><td><strong>{{ $keluarga->nama_kepala_keluarga }}</strong></td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{ $keluarga->alamat ?? '-' }}</td></tr>
    <tr><td>RT / RW</td><td>:</td><td>{{ $keluarga->rt ?? '-' }} / {{ $keluarga->rw ?? '-' }}</td></tr>
    <tr><td>Kelurahan</td><td>:</td><td>{{ $keluarga->kelurahan ?? '-' }}</td></tr>
    <tr><td>Kecamatan</td><td>:</td><td>{{ $keluarga->kecamatan ?? '-' }}</td></tr>
    <tr><td>Kota</td><td>:</td><td>{{ $keluarga->kota ?? '-' }}</td></tr>
    <tr><td>Telepon</td><td>:</td><td>{{ $keluarga->no_telepon ?? '-' }}</td></tr>
    <tr><td>Lingkungan Sion</td><td>:</td><td>{{ $keluarga->lingkunganSion?->nama ?? '-' }}</td></tr>
</table>

<h4>Anggota Keluarga</h4>
<table class="anggota">
    <thead>
        <tr>
            <th style="width:25px">No</th>
            <th>Nama Lengkap</th>
            <th style="width:60px">L/P</th>
            <th>Tempat Lahir</th>
            <th style="width:85px">Tgl Lahir</th>
            <th>Hubungan</th>
            <th>Agama</th>
            <th style="width:55px">Baptis</th>
        </tr>
    </thead>
    <tbody>
        @forelse($keluarga->anggota as $i => $w)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $w->nama }}</td>
            <td class="center">{{ $w->jenis_kelamin }}</td>
            <td>{{ $w->tempat_lahir ?? '-' }}</td>
            <td class="center">{{ $w->tanggal_lahir ? \Carbon\Carbon::parse($w->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
            <td class="center">{{ $w->hubungan_keluarga ?? '-' }}</td>
            <td class="center">{{ $w->agama ?? 'Kristen' }}</td>
            <td class="center">{{ $w->status_baptis === 'sudah' ? '✓' : '✗' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center; padding:15px; color:#999">Belum ada anggota terdaftar</td></tr>
        @endforelse
    </tbody>
</table>

<table style="width:100%; margin-top:25px">
    <tr>
        <td style="width:50%">&nbsp;</td>
        <td style="text-align:center; font-size:10pt">
            <div>Mengetahui,</div>
            <div style="margin-top:60px; border-top:1px solid #333; display:inline-block; width:180px; padding-top:3px">Ketua Majelis</div>
        </td>
    </tr>
</table>

<div class="footer">Dicetak dari Sistem Informasi {{ $profil->nama_gereja ?? 'Gereja SION' }} — {{ now()->format('d/m/Y H:i') }}</div>
</body></html>
