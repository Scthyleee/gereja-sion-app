@extends('layouts.app')
@section('title', $title ?? 'Halaman')
@section('content')
<div class="content-card">
    <div class="content-card__body text-center py-5">
        <i class="fas fa-hard-hat fa-3x text-warning mb-3 d-block"></i>
        <h4 class="fw-bold">{{ $title ?? 'Halaman' }}</h4>
        <p class="text-muted">Modul ini sedang dalam pengembangan dan akan segera tersedia.</p>
    </div>
</div>
@endsection
