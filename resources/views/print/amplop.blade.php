<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
    @page { margin: 8mm; size: A4 }
    body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #1a1a1a; margin: 0; padding: 0 }
    .amplop-page { page-break-after: always; padding: 15mm 20mm; }
    .amplop-page:last-child { page-break-after: auto; }
    .kop { display: table; width: 100%; margin-bottom: 5px; }
    .kop-logo { display: table-cell; width: 80px; vertical-align: top; }
    .kop-logo img { width: 70px; height: auto; }
    .kop-text { display: table-cell; vertical-align: middle; text-align: center; font-size: 10pt; }
    .kop-text .nama { font-weight: bold; font-size: 12pt; letter-spacing: 1px; }
    .kop-text .resort { font-size: 9pt; }
    .kop-text .majelis { font-weight: bold; font-size: 11pt; letter-spacing: 3px; }
    .kop-text .alamat { font-size: 8pt; }
    .kop-line { border-top: 3px solid #000; border-bottom: 1px solid #000; padding: 1px 0; margin: 8px 0 20px 0; }
    .info { font-size: 12pt; font-weight: bold; line-height: 1.6; }
    .info .label { font-size: 11pt; text-transform: uppercase; }
    .info .nama-petugas { font-weight: bold; font-size: 13pt; }
    .info .tanggal { font-size: 10pt; text-transform: uppercase; }
    .info .pukul { font-size: 10pt; font-style: italic; }
</style>
</head><body>

@foreach($jadwal->petugas->where('kategori', 'insentif')->where('nominal_insentif', '>', 0) as $jp)
<div class="amplop-page">
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

    <div class="info">
        <div class="label">PETUGAS IBADAH MINGGU</div>
        <div class="label">{{ strtoupper($jp->jenis_tugas) }}{{ $jp->is_external ? ' :' : ' :' }}</div>
        @if($jp->is_external)
        <div class="nama-petugas">{{ $jp->nama_external }} (Luar)</div>
        @elseif($jp->petugas)
        <div class="nama-petugas">{{ $jp->petugas->nama_petugas }}{{ $jp->petugas->is_pendeta_sion ? ' (Sion)' : '' }}</div>
        @else
        <div class="nama-petugas">{{ $jp->nama_external ?? 'N/A' }}</div>
        @endif
        <div class="tanggal">TANGGAL, {{ strtoupper($jadwal->tanggal->translatedFormat('d F Y')) }}</div>
        <div class="pukul">Pukul : {{ $jadwal->waktu ? substr($jadwal->waktu, 0, 5) : '00.00' }} Wib</div>
    </div>
</div>
@endforeach

</body></html>
