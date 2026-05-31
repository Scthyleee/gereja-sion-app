@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="row g-4">
    {{-- Info Profil --}}
    <div class="col-lg-6">
        <div class="content-card">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Akun</h6></div>
            <div class="content-card__body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                        @error('nama')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <input type="text" class="form-control" value="{{ $user->role->nama_role ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Login Terakhir</label>
                        <input type="text" class="form-control" value="{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d M Y H:i') : 'Belum pernah' }}" disabled>
                    </div>
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="col-lg-6">
        <div class="content-card">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-key me-2"></i>Ganti Password</h6></div>
            <div class="content-card__body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Lama</label>
                        <input type="password" name="current_password" class="form-control" required>
                        @error('current_password')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                    </div>
                    <button class="btn btn-warning"><i class="fas fa-lock me-1"></i>Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
