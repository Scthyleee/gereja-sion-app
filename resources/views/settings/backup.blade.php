@extends('layouts.app')
@section('title', 'Backup & Arsip Data')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card stat-card--blue"><div class="stat-card__icon"><i class="fas fa-database"></i></div><div class="stat-card__body"><div class="stat-card__label">Total Backup</div><div class="stat-card__value">{{ $backups->total() }}</div></div></div></div>
    <div class="col-md-4"><div class="stat-card stat-card--green"><div class="stat-card__icon"><i class="fas fa-check-circle"></i></div><div class="stat-card__body"><div class="stat-card__label">Berhasil</div><div class="stat-card__value">{{ DB::table('data_backups')->where('status','completed')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="stat-card stat-card--orange"><div class="stat-card__icon"><i class="fas fa-broom"></i></div><div class="stat-card__body"><div class="stat-card__label">Sudah Dibersihkan</div><div class="stat-card__value">{{ DB::table('data_backups')->where('dibersihkan',true)->count() }}</div></div></div></div>
</div>

{{-- Generate Backup --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-plus-circle me-2" style="color:var(--primary)"></i>Generate Backup Baru</h6></div>
    <div class="content-card__body">
        <form action="{{ route('backup.generate') }}" method="POST" class="row g-3 align-items-end" onsubmit="return confirm('Generate backup untuk tahun ini? Proses ini bisa memakan waktu beberapa menit.')">@csrf
            <div class="col-md-3"><label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    @foreach($years as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach
                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary"><i class="fas fa-cogs me-1"></i>Generate Backup (SQL + PDF)</button></div>
        </form>
    </div>
</div>

{{-- Import SQL --}}
<div class="content-card mb-4">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-upload me-2"></i>Import SQL → Generate PDF</h6></div>
    <div class="content-card__body">
        <p class="small text-muted mb-3">Upload file SQL backup untuk mengimport data kembali. Setelah import, gunakan menu <strong>Cetak Laporan</strong> untuk generate PDF dari data yang diimport.</p>
        <form action="{{ route('backup.upload-sql') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">@csrf
            <div class="col-md-6"><label class="form-label fw-semibold">File SQL</label><input type="file" name="sql_file" class="form-control" accept=".sql" required></div>
            <div class="col-auto"><button class="btn btn-warning"><i class="fas fa-file-import me-1"></i>Import SQL</button></div>
        </form>
    </div>
</div>

{{-- List Backups --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Backup</h6></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Tahun</th><th>Jenis</th><th>Tanggal</th><th>Files</th><th>Ukuran</th><th>Status</th><th>Dibersihkan</th><th style="width:200px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($backups as $b)
                    <tr>
                        <td class="fw-semibold">{{ $b->tahun }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($b->jenis) }}</span></td>
                        <td class="small">{{ \Carbon\Carbon::parse($b->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $b->total_files }} file</td>
                        <td>{{ $b->total_size_mb }} MB</td>
                        <td><span class="badge bg-{{ match($b->status) { 'completed' => 'success', 'generating' => 'warning', 'failed' => 'danger', default => 'secondary' } }}">{{ ucfirst($b->status) }}</span></td>
                        <td>
                            @if($b->dibersihkan)
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>{{ \Carbon\Carbon::parse($b->tanggal_bersih)->format('d/m/Y') }}</span>
                            @else
                            <span class="badge bg-secondary">Belum</span>
                            @endif
                        </td>
                        <td>
                            @if($b->status === 'completed')
                            <a href="{{ route('backup.download', $b->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Download ZIP</a>
                            @if(!$b->dibersihkan)
                            <form action="{{ route('backup.cleanup', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('PERHATIAN: Ini akan menghapus semua data transaksi tahun {{ $b->tahun }}. Saldo akan tetap. Lanjutkan?')">@csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-broom me-1"></i>Bersihkan</button>
                            </form>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada backup</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($backups->hasPages())<div class="content-card__body border-top">{{ $backups->links() }}</div>@endif
</div>
@endsection
