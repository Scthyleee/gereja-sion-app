<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $__profil->nama_gereja ?? 'Gereja' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme === 'dark' || (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
    <style>
        .dark .ts-wrapper .ts-control { background:var(--bg-card)!important; color:var(--text)!important; border-color:var(--border)!important; }
        .dark .ts-wrapper .ts-dropdown { background:var(--bg-card)!important; color:var(--text)!important; border-color:var(--border)!important; }
        .dark .ts-wrapper .ts-dropdown .active { background:var(--primary)!important; color:#fff!important; }
        .dark .ts-wrapper .ts-dropdown .optgroup-header { color:var(--gold)!important; font-weight:700; }
        .ts-wrapper.form-select-sm .ts-control { min-height:31px; padding:2px 8px; font-size:.875rem; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-gereja.svg') }}" alt="Logo" class="brand-logo">
            <div class="brand-text">{{ $__profil->nama_gereja ?? 'Gereja' }}<small>Sistem Informasi</small></div>
        </div>
        <div class="sidebar-menu">
            <div class="sidebar-label">Menu Utama</div>
            @if(auth()->user()->canAccess('dashboard'))
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i><span>Dashboard</span>
            </a>
            @endif

            @if(auth()->user()->canAccess('jadwal'))
            <a href="{{ route('jadwal.index') }}" class="sidebar-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i><span>Jadwal & Pelayanan</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('jemaat'))
            <a href="{{ route('jemaat.index') }}" class="sidebar-link {{ request()->routeIs('jemaat.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i><span>Data Jemaat</span>
            </a>
            @endif

            @if(auth()->user()->canAccess('keuangan'))
            <div class="sidebar-label">Keuangan</div>
            <a href="{{ route('keuangan.index') }}" class="sidebar-link {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i><span>Manajemen Keuangan</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('transaksi'))
            <a href="{{ route('keuangan.transaksi') }}" class="sidebar-link {{ request()->routeIs('keuangan.transaksi') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i><span>Transaksi</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('keuangan'))
            <a href="{{ route('keuangan.master') }}" class="sidebar-link {{ request()->routeIs('keuangan.master') ? 'active' : '' }}">
                <i class="fas fa-book"></i><span>Master Akun (ZA6)</span>
            </a>
            <a href="{{ route('keuangan.saldo') }}" class="sidebar-link {{ request()->routeIs('keuangan.saldo') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i><span>Saldo</span>
            </a>
            <a href="{{ route('ps.index') }}" class="sidebar-link {{ request()->routeIs('ps.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-heart"></i><span>Persembahan Syukur</span>
            </a>
            @endif

            @if(auth()->user()->canAccess('keuangan'))
            <div class="sidebar-label">Warta & Dokumen</div>
            <a href="{{ route('warta.index') }}" class="sidebar-link {{ request()->routeIs('warta.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i><span>Warta Jemaat</span>
            </a>
            @endif

            @if(auth()->user()->canAccess('pendaftaran'))
            <div class="sidebar-label">Pendaftaran</div>
            <a href="{{ route('keluarga.index') }}" class="sidebar-link {{ request()->routeIs('keluarga.*') ? 'active' : '' }}">
                <i class="fas fa-home"></i><span>Pendaftaran Keluarga</span>
            </a>
            <a href="{{ route('baptis.index') }}" class="sidebar-link {{ request()->routeIs('baptis.*') ? 'active' : '' }}">
                <i class="fas fa-water"></i><span>Pendaftaran Baptis</span>
            </a>
            <a href="{{ route('nikah.index') }}" class="sidebar-link {{ request()->routeIs('nikah.*') ? 'active' : '' }}">
                <i class="fas fa-ring"></i><span>Pendaftaran Nikah</span>
            </a>
            @endif

            @if(auth()->user()->canAccess('persidangan') || auth()->user()->canAccess('pedoman') || auth()->user()->canAccess('files'))
            <div class="sidebar-label">Data & Dokumen</div>
            @if(auth()->user()->canAccess('persidangan'))
            <a href="{{ route('persidangan.index') }}" class="sidebar-link {{ request()->routeIs('persidangan.*') ? 'active' : '' }}">
                <i class="fas fa-gavel"></i><span>Bahan Persidangan</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('pedoman'))
            <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i><span>Pedoman Umum</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('files'))
            <a href="{{ route('files.index') }}" class="sidebar-link {{ request()->routeIs('files.*') ? 'active' : '' }}">
                <i class="fas fa-folder-open"></i><span>File Manager</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('jemaat'))
            <a href="{{ route('lingkungan.index') }}" class="sidebar-link {{ request()->routeIs('lingkungan.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i><span>Lingkungan Sion</span>
            </a>
            @endif
            @endif

            @if(auth()->user()->canAccess('laporan'))
            <div class="sidebar-label">Laporan</div>
            <a href="{{ route('laporan.index') }}" class="sidebar-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="fas fa-print"></i><span>Cetak Laporan</span>
            </a>
            @endif

            @if(auth()->user()->role_id <= 2)
            <div class="sidebar-label">Sistem</div>
            <a href="{{ route('cms.index') }}" class="sidebar-link {{ request()->routeIs('cms.*') ? 'active' : '' }}">
                <i class="fas fa-globe"></i><span>CMS Public Page</span>
            </a>
            <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i><span>Pengaturan</span>
            </a>
            <a href="{{ route('backup.index') }}" class="sidebar-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i><span>Backup & Arsip</span>
            </a>
            @endif
        </div>
        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2 px-3 py-2">
                <div class="avatar-circle">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</div>
                <div class="flex-grow-1">
                    <a href="{{ route('profile.index') }}" class="fw-semibold small text-decoration-none" style="color:var(--text)">{{ auth()->user()->nama }}</a>
                    <div class="text-muted" style="font-size:.7rem">{{ auth()->user()->role_name }}</div>
                </div>
                <a href="{{ route('profile.index') }}" class="btn btn-sm btn-link text-muted p-0 me-1" title="Edit Profil"><i class="fas fa-user-edit"></i></a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="main-content">
        <header class="topbar">
            <button class="btn btn-link text-muted p-0 me-3" id="sidebarToggle">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h5 class="mb-0 fw-bold flex-grow-1">@yield('title', 'Dashboard')</h5>
            <button class="btn btn-link p-0 me-3" id="themeToggle" title="Toggle tema" style="color:var(--primary)">
                <i class="fas fa-moon fa-lg"></i>
            </button>
            <span class="text-muted small"><i class="fas fa-calendar me-1"></i>{{ now()->translatedFormat('d M Y') }}</span>
        </header>

        <div class="content-area">
            @yield('content')
        </div>
    </main>

    {{-- Toast Notifications --}}
    @include('components.toast')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        });

        // Theme toggle
        const themeBtn = document.getElementById('themeToggle');
        themeBtn.addEventListener('click', () => {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeBtn.querySelector('i').className = isDark ? 'fas fa-sun fa-lg' : 'fas fa-moon fa-lg';
        });
        if (document.documentElement.classList.contains('dark')) {
            themeBtn.querySelector('i').className = 'fas fa-sun fa-lg';
        }

        // Button ripple effect coords
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mousemove', e => {
                const r = btn.getBoundingClientRect();
                btn.style.setProperty('--x', ((e.clientX - r.left) / r.width * 100) + '%');
                btn.style.setProperty('--y', ((e.clientY - r.top) / r.height * 100) + '%');
            });
        });
    </script>
    <script>
    // Global: Convert all delete forms with confirm() to two-step buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
            form.removeAttribute('onsubmit');
            const btn = form.querySelector('button[type="submit"], button:not([type])');
            if (!btn) return;
            btn.type = 'button';
            const origHTML = btn.innerHTML;
            const origClasses = btn.className;
            btn.addEventListener('click', function() {
                if (this.dataset.confirmed) {
                    form.submit();
                    return;
                }
                this.dataset.confirmed = 'true';
                this.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Yakin?';
                this.className = this.className.replace(/btn-outline-danger|btn-danger|btn-warning/g, '') + ' btn-danger';
                setTimeout(() => {
                    this.innerHTML = origHTML;
                    this.className = origClasses;
                    delete this.dataset.confirmed;
                }, 2500);
            });
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('select.select-search').forEach(function(el){
            new TomSelect(el, {
                plugins: ['remove_button','dropdown_input'],
                create: false,
                allowEmptyOption: true,
                sortField: { field: 'text', direction: 'asc' }
            });
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
