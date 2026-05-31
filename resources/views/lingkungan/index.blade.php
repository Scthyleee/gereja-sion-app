@extends('layouts.app')
@section('title', 'Lingkungan Sion')

@section('content')
<div class="row g-3 mb-4">
    @foreach($lingkungan as $l)
    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="content-card__header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2" style="color:var(--primary)"></i>{{ $l->nama }}</h6>
                <span class="badge bg-primary">{{ $l->warga_count }} Jiwa</span>
            </div>
            <div class="content-card__body">
                <div class="row g-2 text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-2" style="border-color:var(--border)!important">
                            <div class="fs-4 fw-bold" style="color:var(--primary)">{{ $l->keluarga_count }}</div>
                            <div class="small text-muted">KK</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2" style="border-color:var(--border)!important">
                            <div class="fs-4 fw-bold" style="color:var(--primary)">{{ $l->warga_count }}</div>
                            <div class="small text-muted">Jiwa</div>
                        </div>
                    </div>
                </div>
                @if($l->wilayah_deskripsi)<div class="small text-muted mb-2"><i class="fas fa-map me-1"></i>{{ $l->wilayah_deskripsi }}</div>@endif
                <a href="{{ route('lingkungan.show', $l) }}" class="btn btn-sm btn-primary w-100"><i class="fas fa-eye me-1"></i>Detail</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
