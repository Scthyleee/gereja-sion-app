<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
    @page { margin: 15mm; size: A4 }
    body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #1a1a1a; }
    .kop { display: table; width: 100%; margin-bottom: 5px; }
    .kop-logo { display: table-cell; width: 80px; vertical-align: top; }
    .kop-logo img { width: 70px; height: auto; }
    .kop-text { display: table-cell; vertical-align: middle; text-align: center; font-size: 10pt; }
    .kop-text .nama { font-weight: bold; font-size: 12pt; letter-spacing: 1px; }
    .kop-text .resort { font-size: 9pt; }
    .kop-text .majelis { font-weight: bold; font-size: 11pt; letter-spacing: 3px; }
    .kop-text .alamat { font-size: 8pt; }
    .kop-line { border-top: 3px solid #000; border-bottom: 1px solid #000; padding: 1px 0; margin: 8px 0 25px 0; }
    .title { text-align: center; font-weight: bold; font-size: 12pt; line-height: 1.5; margin-bottom: 20px; }
    table.data { width: 100%; border-collapse: collapse; margin: 15px 0; }
    table.data th, table.data td { border: 1px solid #000; padding: 8px 10px; font-size: 10pt; }
    table.data th { background: #f0f0f0; font-weight: bold; text-align: center; }
    table.data td.text-right { text-align: right; }
    table.data td.text-center { text-align: center; }
    .footer-ttd { margin-top: 30px; }
    .footer-ttd .tanggal { text-align: right; margin-bottom: 15px; font-size: 10pt; }
    .footer-ttd .org { text-align: center; font-weight: bold; font-size: 10pt; line-height: 1.5; }
    .footer-ttd .sign-area { display: table; width: 80%; margin: 15px auto 0; }
    .footer-ttd .sign-col { display: table-cell; width: 50%; text-align: center; font-size: 10pt; vertical-align: top; }
    .footer-ttd .sign-line { margin-top: 60px; }
</style>
</head><body>

<div class="kop">
    <div class="kop-logo">
        @if(file_exists(public_path('images/logo-gke.png')))
        <img src="{{ public_path('images/logo-gke.png') }}" alt="Logo">
        @endif
    </div>
    <div class="kop-text">
        <div class="nama">GEREJA KALIMANTAN EVANGELIS (GKE)</div>
        <div class="resort">( ANGGOTA PGI )</div>
        <div class="resort">RESORT {{ strtoupper($settings['resort'] ?? 'PALANGKA RAYA TENGAH') }}</div>
        <div class="majelis">MAJELIS JEMAAT GKE {{ strtoupper($settings['nama_majelis'] ?? 'PALANGKA') }}</div>
        <div class="alamat">{{ $settings['alamat_gereja_lengkap'] ?? 'Jl. Tjilik Riwut Km 1 No 28 PALANGKA RAYA' }}</div>
        <div class="alamat">KALIMANTAN TENGAH</div>
    </div>
</div>
<div class="kop-line"></div>

<div class="title">
    DAFTAR PEMBAYARAN<br>
    INSENTIF PETUGAS {{ strtoupper($jadwal->judul_ibadah ?: $jadwal->jenis_ibadah) }}<br>
    DI GEREJA SION<br>
    {{ strtoupper($jadwal->tanggal->translatedFormat('l, d F Y')) }}<br>
    PUKUL {{ $jadwal->waktu ? substr($jadwal->waktu, 0, 5) : '00.00' }} WIB
</div>

@php
    $petugasInsentif = $jadwal->petugas->where('kategori', 'insentif')->where('nominal_insentif', '>', 0);
    $totalInsentif = $petugasInsentif->sum('nominal_insentif');
@endphp

<table class="data">
    <thead>
        <tr>
            <th style="width:40px">NO</th>
            <th>PETUGAS</th>
            <th style="width:120px">RP</th>
            <th style="width:130px">TANDA TANGAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($petugasInsentif as $i => $jp)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>
                <strong>{{ $jp->jenis_tugas }}</strong><br>
                @if($jp->is_external)
                {{ $jp->nama_external }} (Luar)
                @else
                {{ $jp->petugas ? $jp->petugas->nama_petugas : $jp->nama_external }}
                @if($jp->petugas && $jp->petugas->is_pendeta_sion) (Sion)@endif
                @endif
            </td>
            <td class="text-right">Rp. {{ number_format($jp->nominal_insentif, 0, ',', '.') }},-</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:right;font-weight:bold">JUMLAH</td>
            <td class="text-right" style="font-weight:bold">Rp. {{ number_format($totalInsentif, 0, ',', '.') }},-</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="footer-ttd">
    <div class="tanggal">{{ $settings['kota'] ?? 'Palangka Raya' }}, {{ $jadwal->tanggal->translatedFormat('d F Y') }}</div>
    <div class="org">
        MAJELIS PEKERJA HARIAN<br>
        JEMAAT GKE {{ strtoupper($settings['nama_majelis'] ?? 'PALANGKA') }}
    </div>
    <div class="sign-area">
        <div class="sign-col">
            Ketua<br>
            <div class="sign-line"></div>
            <strong>{{ $settings['nama_ketua'] ?? '_____________' }}</strong>
        </div>
        <div class="sign-col">
            Bendahara,<br>
            <div class="sign-line"></div>
            <strong>{{ $settings['nama_bendahara'] ?? '_____________' }}</strong>
        </div>
    </div>
</div>

</body></html>
