@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<ul class="nav nav-pills mb-4" style="gap:8px">
    @foreach(['umum' => ['fas fa-cog', 'Umum'], 'users' => ['fas fa-users', 'Staf Kantor'], 'akses' => ['fas fa-shield-alt', 'Hak Akses'], 'validasi' => ['fas fa-check-double', 'Validasi'], 'petugas' => ['fas fa-users-cog', 'Petugas & Slot'], 'nikah' => ['fas fa-ring', 'Biaya Nikah'], 'website' => ['fas fa-globe', 'Website'], 'log' => ['fas fa-history', 'Audit Log']] as $t => $info)
    <li class="nav-item"><a href="{{ route('settings.index', ['tab'=>$t]) }}" class="nav-link {{ $tab===$t ? 'active' : '' }}" style="{{ $tab===$t ? 'background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none' : 'color:var(--text)' }}"><i class="{{ $info[0] }} me-1"></i>{{ $info[1] }}</a></li>
    @endforeach
</ul>

@if($tab === 'umum')
{{-- Profil Gereja --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-church me-2"></i>Profil Gereja</h6></div>
    <div class="content-card__body">
        <form action="{{ route('settings.profil') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">Nama Gereja</label><input type="text" name="nama_gereja" class="form-control" value="{{ $profil->nama_gereja ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Resort</label><input type="text" name="resort" class="form-control" value="{{ $settings['resort'] ?? '' }}"></div>
                <div class="col-md-12"><label class="form-label fw-semibold">Alamat Lengkap</label><input type="text" name="alamat_gereja_lengkap" class="form-control" value="{{ $settings['alamat_gereja_lengkap'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Kota</label><input type="text" name="kota" class="form-control" value="{{ $settings['kota'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Telepon</label><input type="text" name="no_telepon" class="form-control" value="{{ $profil->telepon ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ $profil->email ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Nama Ketua Majelis</label><input type="text" name="nama_majelis" class="form-control" value="{{ $settings['nama_majelis'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Nama Bendahara</label><input type="text" name="nama_bendahara" class="form-control" value="{{ $settings['nama_bendahara'] ?? '' }}"></div>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>

@elseif($tab === 'users')
{{-- Staf Kantor Management --}}
<div class="content-card">
    <div class="content-card__header d-flex justify-content-between"><h6 class="mb-0"><i class="fas fa-users me-2"></i>Staf Kantor</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser"><i class="fas fa-plus me-1"></i>Tambah Staf</button>
    </div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Login Terakhir</th><th style="width:80px">Aksi</th></tr></thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->nama }}</td>
                        <td class="small">{{ $u->email }}</td>
                        <td><span class="badge bg-primary">{{ $u->role_name }}</span></td>
                        <td><span class="badge bg-{{ $u->status==='aktif' ? 'success' : 'secondary' }}">{{ ucfirst($u->status) }}</span></td>
                        <td class="small text-muted">{{ $u->last_login ? $u->last_login->diffForHumans() : 'Belum pernah' }}</td>
                        <td>
                            @if($u->id !== auth()->id())
                            <div class="d-flex gap-1">
                                @if(auth()->user()->role_id === 1)
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPw{{ $u->id }}" title="Reset Password"><i class="fas fa-key"></i></button>
                                @endif
                                <form action="{{ route('settings.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Reset Password Modals --}}
@if(auth()->user()->role_id === 1)
@foreach($users as $u)
@if($u->id !== auth()->id())
<div class="modal fade" id="resetPw{{ $u->id }}" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
        <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Reset Password: {{ $u->nama }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('settings.users.reset-password', $u) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Password Baru</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
            </div>
            <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-warning"><i class="fas fa-key me-1"></i>Reset Password</button></div>
        </form>
    </div></div>
</div>
@endif
@endforeach
@endif

<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Staf Kantor</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('settings.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Nama</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Jabatan / Role</label>
                        <select name="role_id" class="form-select" required>
                            @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->nama_role }}</option>@endforeach
                        </select>
                        <small class="text-muted">Pilih: Admin, Bendahara, Staf, atau Ketua MPH</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
            </form>
        </div>
    </div>
</div>

@elseif($tab === 'validasi')
{{-- Approval Settings --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-check-double me-2"></i>Pengaturan Validasi / Approval</h6></div>
    <div class="content-card__body">
        <p class="text-muted small mb-3">Pilih role yang berhak memvalidasi (approve/tolak) untuk setiap modul. Urutan centang menentukan prioritas validasi.</p>
        <form action="{{ route('settings.approval') }}" method="POST">
            @csrf @method('PUT')

            @foreach(['input_kategorial' => 'Input Kategorial (Kolekte, PS, KK, Pengeluaran)', 'rapp' => 'RAPP (Rencana Anggaran)'] as $module => $label)
            <div class="content-card mb-3" style="border:1px solid var(--border)">
                <div class="content-card__header" style="background:rgba(52,152,219,.05)">
                    <h6 class="mb-0 small fw-bold">{{ $label }}</h6>
                </div>
                <div class="content-card__body">
                    <div class="row g-2">
                        @foreach($roles as $r)
                        @if($r->id !== 1) {{-- Skip Super Admin, always has access --}}
                        <div class="col-md-4 col-lg-3">
                            <div class="form-check">
                                <input type="checkbox" name="approval[{{ $module }}][]" value="{{ $r->id }}" class="form-check-input" id="appr_{{ $module }}_{{ $r->id }}"
                                    {{ $approvalSettings->where('module', $module)->where('role_id', $r->id)->count() ? 'checked' : '' }}>
                                <label class="form-check-label small" for="appr_{{ $module }}_{{ $r->id }}">{{ $r->nama_role }}</label>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Super Admin selalu memiliki hak validasi.</small>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Pengaturan Validasi</button>
            </div>
        </form>
    </div>
</div>

@elseif($tab === 'akses')
{{-- Access Control Matrix --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Hak Akses per Role</h6></div>
    <div class="content-card__body">
        <form action="{{ route('settings.permissions') }}" method="POST">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th style="width:200px">Halaman</th>
                            @foreach($roles as $r)
                            <th class="text-center" colspan="3"><small>{{ $r->nama_role }}</small></th>
                            @endforeach
                        </tr>
                        <tr>
                            <th></th>
                            @foreach($roles as $r)
                            <th class="text-center small text-muted" style="width:40px">V</th>
                            <th class="text-center small text-muted" style="width:40px">E</th>
                            <th class="text-center small text-muted" style="width:40px">A</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $slug => $label)
                        <tr>
                            <td class="fw-semibold small">{{ $label }}</td>
                            @foreach($roles as $r)
                            @php
                                $perm = $permissions->where('page_slug', $slug)->where('role_id', $r->id)->first();
                                $isSuper = $r->id === 1; // Super Admin always has all
                            @endphp
                            <td class="text-center"><input type="checkbox" name="perms[{{ $r->id }}][{{ $slug }}][view]" {{ $isSuper || ($perm && $perm->can_view) ? 'checked' : '' }} {{ $isSuper ? 'disabled' : '' }}></td>
                            <td class="text-center"><input type="checkbox" name="perms[{{ $r->id }}][{{ $slug }}][edit]" {{ $isSuper || ($perm && $perm->can_edit) ? 'checked' : '' }} {{ $isSuper ? 'disabled' : '' }}></td>
                            <td class="text-center"><input type="checkbox" name="perms[{{ $r->id }}][{{ $slug }}][approve]" {{ $isSuper || ($perm && $perm->can_approve) ? 'checked' : '' }} {{ $isSuper ? 'disabled' : '' }}></td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <small class="text-muted"><strong>V</strong>=View, <strong>E</strong>=Edit, <strong>A</strong>=Approve. Super Admin selalu memiliki semua hak akses.</small>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Hak Akses</button>
            </div>
        </form>
    </div>
</div>

@elseif($tab === 'petugas')
{{-- Petugas Settings --}}
<div class="row g-4">
    <div class="col-md-4">
        <div class="content-card">
            <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-cog me-2"></i>Pengaturan Quota</h6></div>
            <div class="content-card__body">
                <form action="{{ route('settings.petugas') }}" method="POST">
                    @csrf @method('PUT')
                    <label class="form-label fw-semibold">Batas Tugas per Bulan</label>
                    <div class="input-group">
                        <input type="number" name="petugas_quota_per_bulan" class="form-control" value="{{ $settings['petugas_quota_per_bulan'] ?? 4 }}" min="1" max="20">
                        <span class="input-group-text">x/bulan</span>
                    </div>
                    <small class="text-muted d-block mt-1">Petugas tidak bisa ditambahkan ke jadwal jika sudah melebihi batas ini di bulan yang sama.</small>
                    <button type="submit" class="btn btn-primary mt-3 w-100"><i class="fas fa-save me-1"></i>Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="content-card">
            <div class="content-card__header d-flex justify-content-between">
                <h6 class="mb-0"><i class="fas fa-th-list me-2"></i>Template Slot Petugas</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSlot"><i class="fas fa-plus me-1"></i>Tambah Slot</button>
            </div>
            <div class="content-card__body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>#</th><th>Nama Slot</th><th class="text-center">Qty Default</th><th>Catatan</th><th>Status</th><th style="width:100px">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($slotTemplates as $s)
                        <tr>
                            <td class="small text-muted">{{ $s->urutan }}</td>
                            <td class="fw-semibold">{{ $s->nama_slot }}</td>
                            <td class="text-center"><span class="badge bg-primary">{{ $s->jumlah_default }}x</span></td>
                            <td class="small text-muted">{{ Str::limit($s->catatan, 40) ?: '-' }}</td>
                            <td><span class="badge bg-{{ $s->aktif ? 'success' : 'secondary' }}">{{ $s->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <form action="{{ route('settings.slot.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus slot ini?')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSlot" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Slot Petugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('settings.slot.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Nama Slot</label><input type="text" name="nama_slot" class="form-control" required placeholder="e.g. Pembawa Bendera"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Jumlah Default</label><input type="number" name="jumlah_default" class="form-control" value="1" min="1"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Catatan</label><input type="text" name="catatan" class="form-control" placeholder="(opsional)"></div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

@elseif($tab === 'nikah')
{{-- Biaya Nikah Settings --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-ring me-2"></i>Pengaturan Biaya Pernikahan</h6></div>
    <div class="content-card__body">
        <form action="{{ route('settings.nikah-biaya') }}" method="POST">@csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12"><h6 class="fw-bold text-muted"><i class="fas fa-file-invoice-dollar me-1"></i>Biaya Administrasi & Fasilitas</h6></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Biaya Administrasi (Rp)</label><input type="number" name="nikah_biaya_administrasi" class="form-control" value="{{ $settings['nikah_biaya_administrasi'] ?? 500000 }}" min="0"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Biaya Fasilitas Gereja (Rp)</label><input type="number" name="nikah_biaya_fasilitas" class="form-control" value="{{ $settings['nikah_biaya_fasilitas'] ?? 300000 }}" min="0"></div>
                <div class="col-12 mt-3"><h6 class="fw-bold text-muted"><i class="fas fa-hand-holding-usd me-1"></i>Default Insentif Pelayanan</h6></div>
                <div class="col-md-3"><label class="form-label">Pianis (Rp)</label><input type="number" name="nikah_insentif_pianis" class="form-control" value="{{ $settings['nikah_insentif_pianis'] ?? 200000 }}" min="0"></div>
                <div class="col-md-3"><label class="form-label">LCD (Rp)</label><input type="number" name="nikah_insentif_lcd" class="form-control" value="{{ $settings['nikah_insentif_lcd'] ?? 100000 }}" min="0"></div>
                <div class="col-md-3"><label class="form-label">Sound System (Rp)</label><input type="number" name="nikah_insentif_sound" class="form-control" value="{{ $settings['nikah_insentif_sound'] ?? 150000 }}" min="0"></div>
                <div class="col-md-3"><label class="form-label">Kebersihan (Rp)</label><input type="number" name="nikah_insentif_kebersihan" class="form-control" value="{{ $settings['nikah_insentif_kebersihan'] ?? 100000 }}" min="0"></div>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Simpan Biaya</button>
        </form>
    </div>
</div>



@elseif($tab === 'log')
{{-- Audit Log --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-history me-2"></i>Audit Log (50 terbaru)</h6></div>
    <div class="content-card__body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead><tr><th style="width:150px">Waktu</th><th>User</th><th>Aksi</th><th>Modul</th><th>Detail</th></tr></thead>
                <tbody>
                    @forelse($auditLog as $log)
                    <tr>
                        <td class="small text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="small fw-semibold">{{ $log->user_nama ?? 'System' }}</td>
                        <td class="small">{{ $log->aksi }}</td>
                        <td><span class="badge bg-secondary">{{ $log->modul ?? '-' }}</span></td>
                        <td class="small text-muted">{{ Str::limit($log->detail, 60) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas tercatat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@elseif($tab === 'website')
{{-- Website / Maintenance Mode (CMS-based) --}}
<div class="content-card">
    <div class="content-card__header"><h6 class="mb-0"><i class="fas fa-tools me-2"></i>Mode Maintenance</h6></div>
    <div class="content-card__body">
        <div class="alert" style="background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);color:var(--text)">
            <i class="fas fa-info-circle text-warning me-1"></i>
            <strong>Super Admin</strong> selalu bisa mengakses admin panel dan public page meskipun maintenance aktif.
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-7">
                <div class="p-4 rounded" style="border:1px solid var(--border);background:rgba(0,0,0,.15)">
                    <h6 class="fw-bold mb-3"><i class="fas fa-globe me-2"></i>Public Page</h6>

                    @if(($cmsSettings['maintenance_public'] ?? '0') === '1')
                    {{-- Maintenance is ON --}}
                    <div class="p-3 rounded mb-3" style="background:rgba(255,152,0,.1);border:1px solid rgba(255,152,0,.2)">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span style="width:10px;height:10px;border-radius:50%;background:#ff9800;display:inline-block;animation:pulse 1.5s infinite"></span>
                            <strong style="color:var(--warning)">Maintenance Mode AKTIF</strong>
                            <span class="badge bg-danger ms-1">AKTIF</span>
                        </div>
                        @if(isset($cmsSettings['maintenance_durasi']) && isset($cmsSettings['maintenance_start']))
                            @php
                                $startTime = \Carbon\Carbon::parse($cmsSettings['maintenance_start']);
                                $durasi = (int)$cmsSettings['maintenance_durasi'];
                                $endTime = $startTime->copy()->addHours($durasi);
                                $elapsed = now()->diffForHumans($startTime, true);
                                $remaining = now()->lt($endTime) ? now()->diffForHumans($endTime, true) : 'Sudah melebihi durasi';
                            @endphp
                            <div class="small text-muted mt-2">
                                <div><i class="fas fa-clock me-1"></i>Dimulai: {{ $startTime->format('d M Y H:i') }}</div>
                                <div><i class="fas fa-hourglass-half me-1"></i>Durasi: {{ $durasi }} jam</div>
                                <div><i class="fas fa-stopwatch me-1"></i>Berlangsung: {{ $elapsed }}</div>
                                <div><i class="fas fa-hourglass-end me-1"></i>Sisa: {{ $remaining }}</div>
                            </div>
                        @endif
                        <p class="small text-muted mt-2 mb-0">Pengunjung melihat halaman maintenance. Klik tombol di bawah untuk mengaktifkan kembali.</p>
                    </div>
                    <form action="{{ route('settings.maintenance') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fas fa-power-off me-1"></i>Aktifkan Website (Go Online)</button>
                    </form>
                    @else
                    {{-- Website is Online --}}
                    <div class="p-3 rounded mb-3" style="background:rgba(82,138,94,.08);border:1px solid rgba(82,138,94,.15)">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:10px;height:10px;border-radius:50%;background:#4caf50;display:inline-block"></span>
                            <strong style="color:var(--success)">Website sedang Online</strong>
                            <span class="badge bg-success ms-1">NORMAL</span>
                        </div>
                    </div>
                    <form action="{{ route('settings.maintenance') }}" method="POST" class="d-flex align-items-end gap-3" id="formMaintenance">
                        @csrf
                        <div>
                            <label class="form-label fw-semibold small">Durasi Maintenance (Jam)</label>
                            <input type="number" name="durasi" class="form-control" value="2" min="1" max="72" style="width:120px">
                        </div>
                        <button type="button" class="btn btn-warning" id="btnMaintenance" onclick="confirmMaintenance(this)">
                            <i class="fas fa-wrench me-1"></i>Aktifkan Maintenance
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="col-md-5 text-center">
                <div class="p-4" style="background:var(--bg);border-radius:16px">
                    <i class="fas fa-server fa-3x mb-3" style="color:var(--primary);opacity:.5"></i>
                    <p class="small text-muted mb-2">Saat maintenance aktif, pengunjung akan melihat halaman maintenance.</p>
                    <p class="small text-muted mb-0"><strong>Super Admin</strong> tetap bisa mengakses semuanya.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

@push('scripts')
<script>
function confirmMaintenance(btn) {
    if (btn.dataset.confirmed) {
        document.getElementById('formMaintenance').submit();
        return;
    }
    btn.dataset.confirmed = 'true';
    btn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Klik Lagi untuk Konfirmasi!';
    btn.classList.replace('btn-warning', 'btn-danger');
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-wrench me-1"></i>Aktifkan Maintenance';
        btn.classList.replace('btn-danger', 'btn-warning');
        delete btn.dataset.confirmed;
    }, 3000);
}
</script>
@endpush
