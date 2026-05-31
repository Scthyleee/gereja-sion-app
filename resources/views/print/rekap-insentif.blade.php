<!DOCTYPE html><html><head><meta charset="utf-8">@include('print._styles')</head><body>
@include('print._kop-surat')
<div class="title">REKAP INSENTIF PETUGAS</div>
<div class="subtitle">Bulan {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</div>

<table class="data">
    <thead><tr><th style="width:30px">No</th><th>Nama Petugas</th><th>Jenis Tugas</th><th class="text-center">Jml Tugas</th><th class="text-right">Total Insentif</th></tr></thead>
    <tbody>
        @foreach($data as $i => $d)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td class="fw-bold">{{ $d->nama_petugas }}</td>
            <td>{{ $d->jenis_tugas }}</td>
            <td class="text-center">{{ $d->jumlah_tugas }}x</td>
            <td class="text-right">Rp {{ number_format($d->total_insentif, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot><tr class="total-row"><td colspan="3" class="text-right">TOTAL</td><td class="text-center">{{ $data->sum('jumlah_tugas') }}x</td><td class="text-right">Rp {{ number_format($data->sum('total_insentif'), 0, ',', '.') }}</td></tr></tfoot>
</table>

<div class="signature"><table><tr>
    <td>Disetujui,<br>{{ $settings['jabatan_ketua'] ?? 'Ketua MPH' }}<br><br><br><br><span class="nama">{{ $settings['nama_ketua'] ?? '.............................' }}</span></td>
    <td>&nbsp;</td>
    <td>Dibuat oleh,<br>{{ $settings['jabatan_bendahara'] ?? 'Bendahara' }}<br><br><br><br><span class="nama">{{ $settings['nama_bendahara'] ?? '.............................' }}</span></td>
</tr></table></div>
</body></html>
