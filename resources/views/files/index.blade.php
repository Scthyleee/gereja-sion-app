@extends('layouts.app')
@section('title', 'File Manager')

@section('content')
<div class="content-card mb-4">
    <div class="content-card__body d-flex justify-content-between align-items-center">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background:none;padding:0">
                    <li class="breadcrumb-item"><a href="{{ route('files.index') }}" class="text-decoration-none"><i class="fas fa-home me-1"></i>Root</a></li>
                    @foreach($breadcrumbs as $bc)
                    <li class="breadcrumb-item"><a href="{{ route('files.index', ['path' => $bc['path']]) }}" class="text-decoration-none">{{ $bc['name'] }}</a></li>
                    @endforeach
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFolder"><i class="fas fa-folder-plus me-1"></i>Folder Baru</button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalUpload"><i class="fas fa-upload me-1"></i>Upload</button>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th style="width:40px"></th><th>Nama</th><th style="width:100px">Ukuran</th><th style="width:150px">Diubah</th><th style="width:80px">Aksi</th></tr></thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="text-center">
                            @if($item['type'] === 'folder')
                                <i class="fas fa-folder fa-lg" style="color:#e67e22"></i>
                            @else
                                @php $ext = $item['ext'] ?? ''; @endphp
                                <i class="fas fa-{{ in_array($ext,['pdf'])?'file-pdf':(in_array($ext,['jpg','jpeg','png','gif'])?'file-image':(in_array($ext,['doc','docx'])?'file-word':(in_array($ext,['xls','xlsx'])?'file-excel':'file'))) }} fa-lg"
                                   style="color:{{ in_array($ext,['pdf'])?'#e74c3c':(in_array($ext,['jpg','jpeg','png','gif'])?'#3498db':(in_array($ext,['doc','docx'])?'#2980b9':'#95a5a6')) }}"></i>
                            @endif
                        </td>
                        <td>
                            @if($item['type'] === 'folder')
                                <a href="{{ route('files.index', ['path' => $item['path']]) }}" class="fw-semibold text-decoration-none">{{ $item['name'] }}</a>
                            @else
                                <a href="{{ $item['url'] }}" target="_blank" class="text-decoration-none">{{ $item['name'] }}</a>
                            @endif
                        </td>
                        <td class="small text-muted">
                            @if($item['type'] === 'file')
                                @if($item['size'] > 1048576) {{ number_format($item['size']/1048576, 1) }} MB
                                @else {{ number_format($item['size']/1024, 1) }} KB @endif
                            @else — @endif
                        </td>
                        <td class="small text-muted">{{ $item['modified'] ?? '-' }}</td>
                        <td>
                            <form action="{{ route('files.destroy') }}" method="POST" onsubmit="return confirm('Hapus {{ $item['name'] }}?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="target" value="{{ $item['path'] }}">
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-folder-open fa-2x mb-2 d-block"></i>Folder kosong</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modals --}}
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Upload File</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">@csrf
            <input type="hidden" name="path" value="{{ $path }}">
            <div class="modal-body"><div class="mb-3"><label class="form-label fw-semibold">Pilih File (max 20MB)</label><input type="file" name="file" class="form-control" required></div></div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="modalFolder" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Buat Folder</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('files.folder') }}" method="POST">@csrf
            <input type="hidden" name="path" value="{{ $path }}">
            <div class="modal-body"><div class="mb-3"><label class="form-label fw-semibold">Nama Folder</label><input type="text" name="folder_name" class="form-control" required></div></div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-folder-plus me-1"></i>Buat</button></div>
        </form>
    </div></div>
</div>
@endsection
