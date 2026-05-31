<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')
<style>body{font-family:'Times New Roman',serif;font-size:9pt;margin:8mm}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:2px 4px;font-size:7.5pt}th{background:#f0f0f0;font-weight:bold;text-align:center}.text-right{text-align:right}h2{text-align:center;font-size:12pt}</style>

@include('print._kop-surat')

<h2>DAFTAR JEMAAT{{ $status !== 'semua' ? ' (STATUS: ' . strtoupper($status) . ')' : '' }}</h2>
<p style="text-align:center">Per tanggal: {{ now()->format('d F Y') }} — Total: {{ $jemaat->count() }} jiwa</p>

<table>
    <thead><tr><th>No</th><th>Nama Lengkap</th><th>No KK</th><th>JK</th><th>TTL</th><th>Lingkungan</th><th>Alamat</th><th>Telepon</th><th>Pekerjaan</th><th>Nikah</th><th>Baptis</th><th>Sidi</th></tr></thead>
    <tbody>
        @foreach($jemaat as $i => $j)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $j->nama_lengkap }}</td>
            <td>{{ $j->no_kk ?? '-' }}</td>
            <td class="text-center">{{ $j->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
            <td>{{ $j->tempat_lahir }}, {{ $j->tanggal_lahir?->format('d/m/Y') }}</td>
            <td>{{ $j->lingkungan ?? '-' }}</td>
            <td>{{ $j->alamat ?? '-' }}</td>
            <td>{{ $j->no_telepon ?? '-' }}</td>
            <td>{{ $j->pekerjaan ?? '-' }}</td>
            <td>{{ $j->status_pernikahan ?? '-' }}</td>
            <td>{{ $j->status_baptis === 'Sudah Baptis' ? 'Ya' : '-' }}</td>
            <td>{{ $j->status_sidi === 'Sudah Sidi' ? 'Ya' : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
