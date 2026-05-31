@include('print._styles')
<style>body{font-family:'Times New Roman',serif;font-size:9pt;margin:8mm}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:2px 4px;font-size:8pt}th{background:#f0f0f0;font-weight:bold;text-align:center}h2{text-align:center;font-size:12pt}</style>

@include('print._kop-surat')

<h2>DAFTAR KELUARGA JEMAAT</h2>
<p style="text-align:center">Per tanggal: {{ now()->format('d F Y') }} — Total: {{ $keluarga->count() }} KK</p>

@foreach($keluarga as $i => $k)
<div style="margin-bottom:15px;{{ $i > 0 ? 'page-break-inside:avoid' : '' }}">
    <p style="font-weight:bold;margin:5px 0">{{ $i+1 }}. {{ $k->nama_kepala_keluarga }} — No KK: {{ $k->no_kk ?? '-' }} — {{ $k->lingkunganSion?->nama ?? '-' }}</p>
    <p style="font-size:8pt;margin:0 0 3px">Alamat: {{ $k->alamat }}, RT {{ $k->rt }}/RW {{ $k->rw }}, {{ $k->kelurahan }}, {{ $k->kecamatan }}, {{ $k->kota }}</p>
    @if($k->anggota->count())
    <table>
        <thead><tr><th>#</th><th>Nama</th><th>Hubungan</th><th>JK</th><th>TTL</th><th>Pekerjaan</th><th>Baptis</th><th>Sidi</th></tr></thead>
        <tbody>
            @foreach($k->anggota as $ai => $a)
            <tr>
                <td class="text-center">{{ $ai+1 }}</td>
                <td>{{ $a->nama_lengkap }}</td>
                <td>{{ $a->hubungan_keluarga ?? '-' }}</td>
                <td class="text-center">{{ $a->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                <td>{{ $a->tempat_lahir }}, {{ $a->tanggal_lahir?->format('d/m/Y') }}</td>
                <td>{{ $a->pekerjaan ?? '-' }}</td>
                <td>{{ $a->status_baptis === 'Sudah Baptis' ? 'Ya' : '-' }}</td>
                <td>{{ $a->status_sidi === 'Sudah Sidi' ? 'Ya' : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endforeach
