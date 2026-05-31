<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ ucfirst($transaksi->tipe) }} #{{ $transaksi->id }}</title>
    <style>
        @page { size: A4; margin: 15mm 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; }

        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px; position: relative; }
        .header-logo { position: absolute; left: 0; top: 0; width: 65px; height: 65px; }
        .header h2 { font-size: 15pt; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .header h3 { font-size: 12pt; font-weight: bold; margin: 4px 0; }
        .header p { font-size: 9pt; margin: 2px 0; color: #333; }

        .kwitansi-title { text-align: center; font-size: 14pt; font-weight: bold; margin: 22px 0 18px;
            text-decoration: underline; letter-spacing: 3px; }

        .meta-table { width: 100%; margin-bottom: 18px; }
        .meta-table td { vertical-align: top; padding: 3px 0; font-size: 10pt; }
        .meta-right-table { border-collapse: collapse; float: right; margin-bottom: 10px; }
        .meta-right-table td { border: 1px solid #000; padding: 4px 10px; font-size: 9pt; }
        .meta-right-table td:first-child { font-weight: bold; background: #f5f5f5; }

        table.items { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.items th, table.items td { border: 1px solid #000; padding: 6px 10px; font-size: 10pt; }
        table.items th { background: #e8e8e8; text-align: center; font-weight: bold; font-size: 9pt; }
        table.items td.right { text-align: right; }
        table.items td.center { text-align: center; }
        .sub-item { padding-left: 25px !important; font-size: 9pt; color: #333; }

        .total-row td { font-weight: bold; background: #f0f0f0; }
        .terbilang { border: 1px solid #000; padding: 8px 14px; margin: 15px 0; font-size: 10pt; font-style: italic; background: #fafafa; }

        .sig-table { width: 100%; margin-top: 40px; text-align: center; }
        .sig-table td { width: 33%; vertical-align: top; padding: 0 15px; }
        .sig-title { font-size: 9pt; margin-bottom: 60px; }
        .sig-name { font-weight: bold; border-top: 1px solid #000; display: inline-block; padding-top: 5px; min-width: 160px; }

        .date-line { text-align: right; margin-top: 18px; font-size: 10pt; }
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>
    {{-- Header with Logo --}}
    <div class="header">
        @if(file_exists(public_path('images/logo-gke.png')))
        <img src="{{ public_path('images/logo-gke.png') }}" class="header-logo" alt="Logo GKE">
        @endif
        <h2>GEREJA KALIMANTAN EVANGELIS ( GKE )</h2>
        <h3>MAJELIS JEMAAT GKE PALANGKA</h3>
        <p>{{ $settings['alamat_gereja_lengkap'] ?? $settings['alamat'] ?? 'Jl. Tjilik Riwut KM 1 No.28, Palangka, Kec. Jekan Raya, Kota Palangka Raya, Kalimantan Tengah 74874' }}</p>
        <p>Telp. {{ $settings['telepon_gereja'] ?? '-' }} | Email: {{ $settings['email_gereja'] ?? '-' }}</p>
    </div>

    {{-- Title --}}
    <div class="kwitansi-title">K W I T A N S I&nbsp;&nbsp;{{ strtoupper($transaksi->tipe) }}</div>

    {{-- Meta --}}
    <div class="clearfix" style="margin-bottom:18px">
        <table class="meta-right-table">
            <tr><td>No. Bukti</td><td>{{ str_pad($transaksi->id, 4, '0', STR_PAD_LEFT) }}/{{ $transaksi->tipe === 'penerimaan' ? 'PM' : 'PG' }}/{{ $transaksi->bulan }}/{{ $transaksi->tahun }}</td></tr>
            <tr><td>Tanggal</td><td>{{ $transaksi->tanggal->format('d/m/Y') }}</td></tr>
            <tr><td>Kode Akun</td><td>{{ $transaksi->kodeAkun->kode ?? '-' }}</td></tr>
            <tr><td>Status</td><td>{{ ucfirst($transaksi->status) }}</td></tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            @if($transaksi->tipe === 'pengeluaran')
            <td><strong>Dibayarkan kepada :</strong> {{ $transaksi->keterangan ?: ($transaksi->kodeAkun->keterangan ?: $transaksi->kodeAkun->nama) }}</td>
            @else
            <td><strong>Diterima dari :</strong> {{ $transaksi->nama_penyetor ?: '-' }}</td>
            @endif
        </tr>
        <tr>
            <td><strong>Untuk pembayaran :</strong> {{ $transaksi->keterangan ?: ($transaksi->kodeAkun->keterangan ?: $transaksi->kodeAkun->nama) }}</td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Uraian / Keterangan</th>
                <th style="width:70px">Qty</th>
                <th style="width:110px">Harga Satuan</th>
                <th style="width:130px">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @if($transaksi->details->count() > 0)
                @foreach($transaksi->details as $di => $d)
                <tr>
                    <td class="center">{{ $di + 1 }}</td>
                    <td>{{ $d->uraian }}</td>
                    <td class="center">{{ $d->qty }} {{ $d->satuan }}</td>
                    <td class="right">{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td class="center">1</td>
                    <td>{{ $transaksi->keterangan ?: ($transaksi->kodeAkun->keterangan ?: $transaksi->kodeAkun->nama) }}</td>
                    <td class="center">1</td>
                    <td class="right">{{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                </tr>
            @endif
            {{-- Empty rows for better spacing --}}
            @for($i = max($transaksi->details->count(), 1); $i < 3; $i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            @endfor
            <tr class="total-row">
                <td colspan="4" style="text-align:right"><strong>TOTAL</strong></td>
                <td class="right">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Terbilang --}}
    <div class="terbilang">
        <strong>Terbilang :</strong> <em>{{ $terbilang }}</em>
    </div>

    {{-- Date --}}
    <div class="date-line">
        {{ $settings['kota'] ?? 'Palangka Raya' }}, {{ $transaksi->tanggal->translatedFormat('d F Y') }}
    </div>

    {{-- Signatures --}}
    <table class="sig-table">
        <tr>
            @if($transaksi->tipe === 'pengeluaran')
            <td>
                <div class="sig-title">Setuju dibayar,<br><strong>{{ $settings['jabatan_ketua'] ?? 'Ketua MPH' }}</strong></div>
                <div class="sig-name">{{ $settings['nama_ketua'] ?? '........................' }}</div>
            </td>
            <td>
                <div class="sig-title">Yang membayar,<br><strong>{{ $settings['jabatan_bendahara'] ?? 'Bendahara' }}</strong></div>
                <div class="sig-name">{{ $settings['nama_bendahara'] ?? '........................' }}</div>
            </td>
            <td>
                <div class="sig-title">Yang menerima,</div>
                <div class="sig-name">........................</div>
            </td>
            @else
            <td>
                <div class="sig-title">Mengetahui,<br><strong>{{ $settings['jabatan_ketua'] ?? 'Ketua MPH' }}</strong></div>
                <div class="sig-name">{{ $settings['nama_ketua'] ?? '........................' }}</div>
            </td>
            <td>
                <div class="sig-title">Yang menerima,<br><strong>{{ $settings['jabatan_bendahara'] ?? 'Bendahara' }}</strong></div>
                <div class="sig-name">{{ $settings['nama_bendahara'] ?? '........................' }}</div>
            </td>
            <td>
                <div class="sig-title">Yang menyetor,</div>
                <div class="sig-name">{{ $transaksi->nama_penyetor ?: '........................' }}</div>
            </td>
            @endif
        </tr>
    </table>

    <div style="text-align:center;margin-top:20px;font-size:7pt;color:#999;border-top:1px solid #eee;padding-top:5px">
        Dokumen ini dicetak secara otomatis oleh Sistem Informasi {{ $settings['nama_gereja'] ?? 'Gereja' }}
    </div>
</body>
</html>
