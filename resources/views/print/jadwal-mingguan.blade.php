<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')
<style>
    .day-header { background: #f5f0e1; padding: 6px 10px; font-weight: bold; font-size: 11px; border-top: 2px solid #333; margin-top: 12px; }
    .jadwal-item { border: 1px solid #ddd; border-radius: 4px; margin: 6px 0; padding: 8px; page-break-inside: avoid; }
    .jadwal-title { font-size: 11px; font-weight: bold; margin-bottom: 4px; }
    .jadwal-meta { font-size: 9px; color: #666; margin-bottom: 6px; }
    .petugas-table { width: 100%; border-collapse: collapse; font-size: 8px; }
    .petugas-table td, .petugas-table th { padding: 2px 5px; border: 1px solid #ccc; }
    .petugas-table th { background: #f9f9f9; font-weight: bold; }
    .section-label { font-size: 9px; font-weight: bold; margin-top: 5px; margin-bottom: 2px; color: #333; }
</style>
</head><body>
@include('print._kop-surat')

<div class="title">JADWAL IBADAH MINGGUAN</div>
<div class="subtitle">{{ \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d F Y') }} — {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}</div>

@php
    $grouped = $jadwalList->groupBy(fn($j) => $j->tanggal->format('Y-m-d'));
@endphp

@forelse($grouped as $tanggal => $jadwals)
<div class="day-header">
    <i class="fas fa-calendar"></i>
    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
    ({{ $jadwals->count() }} ibadah)
</div>

@foreach($jadwals as $jd)
<div class="jadwal-item">
    <div class="jadwal-title">{{ $jd->judul_ibadah ?: $jd->jenis_ibadah }}</div>
    <div class="jadwal-meta">
        @if($jd->waktu){{ substr($jd->waktu, 0, 5) }}@if($jd->jam_selesai)–{{ substr($jd->jam_selesai, 0, 5) }}@endif WIB · @endif
        {{ $jd->lokasi ?: '-' }}
        @if($jd->tema_ibadah) · Tema: {{ $jd->tema_ibadah }}@endif
        @if($jd->ayat_khotbah) · Ayat: {{ $jd->ayat_khotbah }}@endif
         · Status: {{ ucfirst($jd->status) }}
    </div>

    @if($jd->petugas->count())
    <div class="section-label">Petugas ({{ $jd->petugas->count() }})</div>
    <table class="petugas-table">
        <thead><tr><th>No</th><th>Nama</th><th>Tugas</th><th>Kategori</th></tr></thead>
        <tbody>
            @foreach($jd->petugas as $jp)
            <tr>
                <td style="text-align:center">{{ $loop->iteration }}</td>
                <td>{{ $jp->is_external ? $jp->nama_external : ($jp->petugas->nama_petugas ?? $jp->nama_external ?? '-') }}</td>
                <td>{{ $jp->jenis_tugas }}</td>
                <td>{{ ucfirst($jp->kategori) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endforeach
@empty
<p style="text-align:center;color:#999;margin-top:30px">Tidak ada jadwal ibadah pada periode ini.</p>
@endforelse

<p style="text-align:center;margin-top:30px;font-size:9px;color:#666">— Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} —</p>
</body></html>
