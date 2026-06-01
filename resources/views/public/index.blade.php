<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['meta_title'] ?? 'GKE Sion' }}</title>
    <meta name="description" content="{{ $settings['meta_description'] ?? '' }}">
    <meta property="og:title" content="{{ $settings['meta_title'] ?? 'GKE Sion' }}">
    <meta property="og:description" content="{{ $settings['meta_description'] ?? '' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --gold: {{ $settings['primary_color'] ?? '#c9a84c' }};
            --gold-dark: #a68935;
            --bg: #0a0e17;
            --bg-card: #111827;
            --bg-card-hover: #1a2332;
            --text: #e8e6e3;
            --text-muted: #8b95a5;
            --border: rgba(201,168,76,.15);
            --radius: 16px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
        a { color: var(--gold); text-decoration: none; transition: .3s; }
        a:hover { color: var(--gold-dark); }

        /* === NAVBAR === */
        .pub-nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; backdrop-filter: blur(20px); background: rgba(10,14,23,.8); border-bottom: 1px solid var(--border); transition: .3s; }
        .pub-nav.scrolled { padding: .6rem 2rem; background: rgba(10,14,23,.95); }
        .pub-nav__brand { display: flex; align-items: center; gap: 12px; }
        .pub-nav__brand img { height: 40px; }
        .pub-nav__brand span { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.2rem; color: var(--gold); }
        .pub-nav__links { display: flex; gap: 2rem; align-items: center; }
        .pub-nav__links a { color: var(--text-muted); font-weight: 500; font-size: .9rem; position: relative; }
        .pub-nav__links a:hover { color: var(--gold); }
        .pub-nav__btn { padding: .5rem 1.2rem; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #0a0e17; font-weight: 700; border-radius: 8px; font-size: .85rem; }
        .pub-nav__btn:hover { color: #0a0e17; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(201,168,76,.3); }

        /* === HERO === */
        .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 50% 30%, rgba(201,168,76,.08) 0%, transparent 60%), radial-gradient(ellipse at 80% 80%, rgba(59,130,246,.05) 0%, transparent 50%); }
        .hero::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: conic-gradient(from 0deg at 50% 50%, transparent 0deg, rgba(201,168,76,.03) 60deg, transparent 120deg); animation: heroRotate 20s linear infinite; }
        @keyframes heroRotate { to { transform: rotate(360deg); } }
        .hero__content { position: relative; z-index: 2; max-width: 700px; }
        .hero__badge { display: inline-flex; align-items: center; gap: .5rem; padding: .4rem 1rem; background: rgba(201,168,76,.1); border: 1px solid rgba(201,168,76,.2); border-radius: 30px; font-size: .8rem; color: var(--gold); margin-bottom: 1.5rem; letter-spacing: 2px; text-transform: uppercase; }
        .hero__title { font-family: 'Playfair Display', serif; font-size: clamp(2.5rem, 6vw, 4rem); font-weight: 700; line-height: 1.1; margin-bottom: 1rem; background: linear-gradient(135deg, var(--gold), #fff, var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero__subtitle { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 2rem; line-height: 1.6; }
        .hero__actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .hero__actions .btn-primary { padding: .8rem 2rem; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #0a0e17; font-weight: 700; border-radius: 12px; border: none; cursor: pointer; font-size: 1rem; transition: .3s; display: inline-flex; align-items: center; gap: .5rem; }
        .hero__actions .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(201,168,76,.3); }
        .hero__actions .btn-outline { padding: .8rem 2rem; background: transparent; border: 1px solid rgba(201,168,76,.3); color: var(--gold); font-weight: 600; border-radius: 12px; cursor: pointer; font-size: 1rem; transition: .3s; display: inline-flex; align-items: center; gap: .5rem; }
        .hero__actions .btn-outline:hover { background: rgba(201,168,76,.1); }

        /* === SECTIONS === */
        .pub-section { padding: 5rem 2rem; max-width: 1200px; margin: 0 auto; }
        .pub-section__title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; margin-bottom: .5rem; color: var(--gold); }
        .pub-section__subtitle { color: var(--text-muted); margin-bottom: 2.5rem; }
        .pub-section__divider { width: 60px; height: 3px; background: linear-gradient(90deg, var(--gold), transparent); margin-bottom: 2.5rem; border-radius: 3px; }

        /* === VERSE === */
        .verse-section { padding: 4rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(201,168,76,.05), rgba(10,14,23,1)); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .verse-text { font-family: 'Playfair Display', serif; font-size: clamp(1.3rem, 3vw, 1.8rem); font-weight: 400; font-style: italic; max-width: 800px; margin: 0 auto 1rem; line-height: 1.6; color: var(--text); }
        .verse-ref { color: var(--gold); font-weight: 600; font-size: 1rem; }

        /* === CARDS === */
        .pub-card { background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border); padding: 1.5rem; transition: .3s; position: relative; overflow: hidden; }
        .pub-card:hover { transform: translateY(-4px); border-color: rgba(201,168,76,.3); box-shadow: 0 20px 40px rgba(0,0,0,.3); }
        .pub-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--gold), transparent); opacity: 0; transition: .3s; }
        .pub-card:hover::before { opacity: 1; }
        .pub-card__badge { display: inline-block; padding: .2rem .6rem; border-radius: 6px; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .pub-card__badge--scheduled { background: rgba(59,130,246,.15); color: #60a5fa; }
        .pub-card__badge--done { background: rgba(46,204,113,.15); color: #4ade80; }
        .pub-card__badge--live { background: rgba(239,68,68,.15); color: #f87171; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .6; } }
        .pub-card__title { font-weight: 700; font-size: 1.05rem; margin: .8rem 0 .3rem; }
        .pub-card__meta { font-size: .85rem; color: var(--text-muted); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .pub-card__meta i { color: var(--gold); width: 16px; }

        .grid-3 { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }

        /* === STATS === */
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 3rem 2rem; max-width: 1200px; margin: 0 auto; }
        .stat-item { text-align: center; padding: 2rem 1rem; background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border); }
        .stat-item__number { font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 700; color: var(--gold); margin-bottom: .3rem; }
        .stat-item__label { color: var(--text-muted); font-size: .85rem; text-transform: uppercase; letter-spacing: 1px; }

        /* === GALLERY === */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .gallery-item { position: relative; border-radius: var(--radius); overflow: hidden; aspect-ratio: 4/3; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: .5s; }
        .gallery-item:hover img { transform: scale(1.08); }
        .gallery-item__overlay { position: absolute; inset: 0; background: linear-gradient(transparent 50%, rgba(0,0,0,.8)); display: flex; align-items: flex-end; padding: 1rem; opacity: 0; transition: .3s; }
        .gallery-item:hover .gallery-item__overlay { opacity: 1; }
        .gallery-item__caption { font-size: .85rem; color: #fff; }

        /* === LIVESTREAM === */
        .livestream-wrap { background: var(--bg-card); border-radius: var(--radius); overflow: hidden; border: 1px solid var(--border); }
        .livestream-wrap iframe { width: 100%; aspect-ratio: 16/9; border: none; }

        /* === FOOTER === */
        .pub-footer { background: #060912; border-top: 1px solid var(--border); padding: 3rem 2rem 1.5rem; }
        .pub-footer__inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .pub-footer__col h4 { color: var(--gold); font-family: 'Playfair Display', serif; margin-bottom: 1rem; }
        .pub-footer__col p, .pub-footer__col a { color: var(--text-muted); font-size: .9rem; line-height: 1.8; }
        .pub-footer__col a:hover { color: var(--gold); }
        .pub-footer__social { display: flex; gap: 1rem; margin-top: .5rem; }
        .pub-footer__social a { width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); transition: .3s; }
        .pub-footer__social a:hover { background: var(--gold); color: #0a0e17; border-color: var(--gold); }
        .pub-footer__copy { text-align: center; color: var(--text-muted); font-size: .8rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,.05); max-width: 1200px; margin-left: auto; margin-right: auto; }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .pub-nav__links { display: none; }
            .hero { padding: 6rem 1.5rem 3rem; }
            .pub-section { padding: 3rem 1.5rem; }
            .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    {{-- NAVBAR --}}
    <nav class="pub-nav" id="pubNav">
        <div class="pub-nav__brand">
            @if($profil && isset($profil->logo_path) && $profil->logo_path)
            <img src="{{ asset('storage/' . $profil->logo_path) }}" alt="Logo">
            @endif
            <span>{{ $settings['hero_subtitle'] ?? 'GKE Sion' }}</span>
        </div>
        <div class="pub-nav__links">
            <a href="#jadwal">Jadwal</a>
            <a href="#warta">Warta</a>
            <a href="#pengumuman">Pengumuman</a>
            <a href="#galeri">Galeri</a>
            <a href="#kontak">Kontak</a>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero">
        <div class="hero__content" data-aos="fade-up">
            <div class="hero__badge"><i class="fas fa-church"></i> Selamat Datang</div>
            <h1 class="hero__title">{{ $settings['hero_title'] ?? 'Gereja Kalimantan Evangelis' }}</h1>
            <p class="hero__subtitle">{{ $settings['hero_subtitle'] ?? 'Majelis Jemaat GKE Palangka - Sion' }}</p>
            <div class="hero__actions">
                <a href="#jadwal" class="btn-primary"><i class="fas fa-calendar-alt"></i> Jadwal Ibadah</a>
                <a href="#warta" class="btn-outline"><i class="fas fa-newspaper"></i> Warta Jemaat</a>
                <a href="#kontak" class="btn-outline"><i class="fas fa-envelope"></i> Hubungi Kami</a>
            </div>
        </div>
    </section>

    {{-- AYAT HARIAN --}}
    @if($ayat && ($settings['section_ayat'] ?? '1') === '1')
    <section class="verse-section" data-aos="fade-up">
        <div class="verse-text">"{{ $ayat->teks }}"</div>
        <div class="verse-ref">— {{ $ayat->referensi }} ({{ $ayat->versi }})</div>
    </section>
    @endif

    {{-- STATISTIK --}}
    @if($stats && ($settings['section_statistik'] ?? '1') === '1')
    <div class="stats-bar" data-aos="fade-up">
        <div class="stat-item">
            <div class="stat-item__number">{{ number_format($stats->jemaat) }}</div>
            <div class="stat-item__label">Warga Jemaat</div>
        </div>
        <div class="stat-item">
            <div class="stat-item__number">{{ number_format($stats->keluarga) }}</div>
            <div class="stat-item__label">Keluarga</div>
        </div>
        <div class="stat-item">
            <div class="stat-item__number">{{ $stats->kegiatan_bulan_ini }}</div>
            <div class="stat-item__label">Kegiatan Bulan Ini</div>
        </div>
    </div>
    @endif

    {{-- IBADAH MINGGU INI --}}
    @if($ibadahMinggu && ($settings['section_ibadah_minggu'] ?? '1') === '1')
    <section class="pub-section">
        <h2 class="pub-section__title" data-aos="fade-right"><i class="fas fa-bible" style="margin-right:.5rem"></i>Ibadah Hari Minggu</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Informasi pelayanan ibadah Minggu terdekat</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>

        <div class="pub-card" data-aos="fade-up" style="max-width:700px;margin:0 auto;border:1px solid rgba(201,168,76,.3);background:linear-gradient(135deg, rgba(201,168,76,.05), var(--bg-card))">
            <div style="text-align:center;margin-bottom:1.5rem">
                <span class="pub-card__badge pub-card__badge--scheduled" style="font-size:.75rem">{{ \Carbon\Carbon::parse($ibadahMinggu->tanggal)->translatedFormat('l, d F Y') }}</span>
                <div style="margin-top:.5rem;color:var(--text-muted);font-size:.9rem">
                    <i class="fas fa-clock" style="color:var(--gold)"></i> {{ $ibadahMinggu->waktu ?? '-' }}
                    &nbsp;&nbsp;
                    <i class="fas fa-map-marker-alt" style="color:var(--gold)"></i> {{ $ibadahMinggu->lokasi ?? 'Gereja Sion' }}
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px,1fr));gap:1.2rem">
                @if($ibadahMinggu->pendeta)
                <div style="text-align:center;padding:1rem;border-radius:12px;background:rgba(201,168,76,.08);border:1px solid var(--border)">
                    <i class="fas fa-user-tie" style="font-size:1.5rem;color:var(--gold);margin-bottom:.5rem;display:block"></i>
                    <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Pendeta Pelayanan</div>
                    <div style="font-weight:700;font-size:1.05rem;margin-top:.3rem">{{ $ibadahMinggu->pendeta }}</div>
                </div>
                @endif

                @if($ibadahMinggu->tema)
                <div style="text-align:center;padding:1rem;border-radius:12px;background:rgba(59,130,246,.08);border:1px solid var(--border)">
                    <i class="fas fa-book-open" style="font-size:1.5rem;color:#60a5fa;margin-bottom:.5rem;display:block"></i>
                    <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Tema Khotbah</div>
                    <div style="font-weight:700;font-size:1.05rem;margin-top:.3rem">{{ $ibadahMinggu->tema }}</div>
                </div>
                @endif

                @if($ibadahMinggu->ayat_khotbah)
                <div style="text-align:center;padding:1rem;border-radius:12px;background:rgba(46,204,113,.08);border:1px solid var(--border)">
                    <i class="fas fa-quote-right" style="font-size:1.5rem;color:#4ade80;margin-bottom:.5rem;display:block"></i>
                    <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Ayat Khotbah</div>
                    <div style="font-weight:700;font-size:1.05rem;margin-top:.3rem">{{ $ibadahMinggu->ayat_khotbah }}</div>
                </div>
                @endif

                @if($ibadahMinggu->pembacaan)
                <div style="text-align:center;padding:1rem;border-radius:12px;background:rgba(168,85,247,.08);border:1px solid var(--border)">
                    <i class="fas fa-microphone" style="font-size:1.5rem;color:#c084fc;margin-bottom:.5rem;display:block"></i>
                    <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Pembacaan Alkitab</div>
                    <div style="font-weight:700;font-size:1.05rem;margin-top:.3rem">{{ $ibadahMinggu->pembacaan }}</div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ULANG TAHUN JEMAAT --}}
    @if(($settings['section_ultah'] ?? '1') === '1' && $ultahJemaat->count())
    <section class="pub-section" id="ultah">
        <h2 class="pub-section__title" data-aos="fade-right"><i class="fas fa-birthday-cake" style="margin-right:.5rem"></i>Selamat Ulang Tahun</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Jemaat yang berulang tahun minggu ini</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:1rem;max-width:900px;margin:0 auto">
            @foreach($ultahJemaat as $u)
            <div class="pub-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}" style="text-align:center;padding:1.2rem;position:relative;overflow:hidden{{ $u->is_today ? ';border:2px solid var(--gold);box-shadow:0 0 20px rgba(201,168,76,.2)' : '' }}">
                @if($u->is_today)
                <div style="position:absolute;top:8px;right:8px;background:var(--gold);color:#fff;font-size:.65rem;padding:2px 8px;border-radius:10px;font-weight:700">HARI INI!</div>
                @endif
                <div style="font-size:2.5rem;margin-bottom:.5rem">🎂</div>
                <div style="font-weight:700;font-size:1.05rem;margin-bottom:.3rem">{{ $u->nama_lengkap }}</div>
                <div style="color:var(--text-muted);font-size:.85rem"><i class="fas fa-calendar-day" style="color:var(--gold);margin-right:.3rem"></i>{{ $u->tanggal_ultah }}</div>
                <div style="color:var(--gold);font-weight:600;font-size:.85rem;margin-top:.3rem">Usia ke-{{ $u->usia }} tahun</div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- SUMBANGAN / REKENING --}}
    @if(($settings['section_sumbangan'] ?? '1') === '1' && ($settings['rekening_nomor'] ?? '') !== '')
    <section class="pub-section" id="sumbangan">
        <h2 class="pub-section__title" data-aos="fade-right"><i class="fas fa-hand-holding-heart" style="margin-right:.5rem"></i>Sumbangan</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Anda dapat memberikan sumbangan melalui transfer bank berikut</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>

        <div class="pub-card" data-aos="fade-up" style="max-width:500px;margin:0 auto;text-align:center;border:1px solid rgba(201,168,76,.3)">
            <i class="fas fa-university" style="font-size:2.5rem;color:var(--gold);margin-bottom:1rem;display:block"></i>
            <div style="font-size:.85rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:2px;margin-bottom:.5rem">{{ $settings['rekening_bank'] ?? 'BRI' }}</div>
            <div style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:var(--gold);letter-spacing:3px;margin-bottom:.5rem">{{ $settings['rekening_nomor'] }}</div>
            <div style="font-size:1rem;font-weight:600">a/n {{ $settings['rekening_atas_nama'] ?? '' }}</div>

            @if($settings['whatsapp_sumbangan'] ?? '')
            <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border)">
                <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.8rem">Kirim bukti transfer via WhatsApp:</p>
                @php $waNum = preg_replace('/[^0-9]/', '', $settings['whatsapp_sumbangan']); @endphp
                <a href="https://wa.me/{{ $waNum }}?text={{ urlencode('Shalom, saya ingin mengirimkan bukti transfer sumbangan.') }}" target="_blank" style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.5rem;background:#25D366;color:#fff;font-weight:700;border-radius:12px;font-size:.95rem;transition:.3s">
                    <i class="fab fa-whatsapp" style="font-size:1.2rem"></i> Kirim Bukti Transfer
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- JADWAL IBADAH --}}
    @if($jadwal->count() && ($settings['section_jadwal'] ?? '1') === '1')
    <section class="pub-section" id="jadwal">
        <h2 class="pub-section__title" data-aos="fade-right">Jadwal Ibadah Mendatang</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Jadwal pelayanan ibadah yang akan datang</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>
        <div class="grid-3">
            @foreach($jadwal as $j)
            <div class="pub-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                @php
                    $tgl = \Carbon\Carbon::parse($j->tanggal);
                    $isToday = $tgl->isToday();
                @endphp
                <span class="pub-card__badge {{ $isToday ? 'pub-card__badge--live' : 'pub-card__badge--scheduled' }}">
                    {{ $isToday ? '● Hari Ini' : 'Terjadwal' }}
                </span>
                <div class="pub-card__title">{{ $j->judul_ibadah ?? $j->jenis_ibadah }}</div>
                <div class="pub-card__meta">
                    <span><i class="fas fa-calendar-day"></i> {{ $tgl->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="pub-card__meta" style="margin-top:.3rem">
                    <span><i class="fas fa-clock"></i> {{ $j->waktu ?? '-' }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $j->lokasi ?? 'Gereja Sion' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- WARTA JEMAAT --}}
    @if($warta->count() && ($settings['section_warta'] ?? '1') === '1')
    <section class="pub-section" id="warta">
        <h2 class="pub-section__title" data-aos="fade-right">Warta Jemaat</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Laporan keuangan dan informasi mingguan jemaat</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>
        <div class="grid-3">
            @foreach($warta as $w)
            <div class="pub-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <span class="pub-card__badge pub-card__badge--done">Tersedia</span>
                <div class="pub-card__title">Warta Jemaat</div>
                <div class="pub-card__meta">
                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($w->tanggal_awal)->format('d M') }} — {{ \Carbon\Carbon::parse($w->tanggal_akhir)->format('d M Y') }}</span>
                </div>
                <div style="margin-top:1rem">
                    <a href="{{ route('warta.public.download', $w->id) }}" target="_blank" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:8px;font-size:.85rem;font-weight:600">
                        <i class="fas fa-file-pdf"></i> Unduh PDF
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- PENGUMUMAN --}}
    @if($pengumuman->count() && ($settings['section_pengumuman'] ?? '1') === '1')
    <section class="pub-section" id="pengumuman">
        <h2 class="pub-section__title" data-aos="fade-right">Pengumuman</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Informasi terkini dari majelis jemaat</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>
        <div class="grid-3">
            @foreach($pengumuman as $p)
            <div class="pub-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                @if($p->gambar_url)
                <img src="{{ asset('storage/' . $p->gambar_url) }}" style="width:100%;border-radius:12px;margin-bottom:1rem;aspect-ratio:16/9;object-fit:cover">
                @endif
                <div class="pub-card__title">{{ $p->judul }}</div>
                <p style="color:var(--text-muted);font-size:.9rem;margin-top:.5rem;line-height:1.6">{!! Str::limit(strip_tags($p->isi), 150) !!}</p>
                @if($p->tanggal_mulai)
                <div class="pub-card__meta" style="margin-top:.5rem">
                    <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- LIVESTREAM --}}
    @if(($settings['section_livestream'] ?? '1') === '1' && ($settings['youtube_live_url'] ?? '') !== '')
    <section class="pub-section">
        <h2 class="pub-section__title" data-aos="fade-right">
            @if(($settings['is_live'] ?? '0') === '1')
            <span class="pub-card__badge pub-card__badge--live" style="font-size:.7rem;vertical-align:middle;margin-right:8px">● LIVE</span>
            @endif
            Ibadah Online
        </h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Saksikan ibadah secara live atau tonton rekaman terakhir</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>
        <div class="livestream-wrap" data-aos="fade-up">
            @php
                $ytUrl = ($settings['is_live'] ?? '0') === '1' ? ($settings['youtube_live_url'] ?? '') : ($settings['youtube_rekaman_url'] ?? $settings['youtube_live_url'] ?? '');
                // Extract video ID — supports: v=ID, youtu.be/ID, embed/ID, live/ID
                $videoId = '';
                if (preg_match('/(?:v=|youtu\.be\/|embed\/|live\/)([\w-]{11})/', $ytUrl, $m)) $videoId = $m[1];
                $embedUrl = $videoId ? 'https://www.youtube.com/embed/' . $videoId : '';
            @endphp
            @if($embedUrl)
            <iframe src="{{ $embedUrl }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="no-referrer-when-downgrade" loading="lazy"></iframe>
            @else
            <div style="padding:3rem;text-align:center;color:var(--text-muted)"><i class="fas fa-video" style="font-size:2rem;margin-bottom:1rem;display:block;color:var(--gold)"></i>Video belum tersedia</div>
            @endif
        </div>

        {{-- VIDEO ARCHIVE --}}
        @if(isset($videoArchive) && $videoArchive->count())
        <div style="margin-top:2.5rem">
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;color:var(--text)"><i class="fas fa-history" style="color:var(--gold);margin-right:.5rem"></i>Video Sebelumnya</h3>
            <div style="margin-bottom:1rem">
                <form method="GET" action="#" style="display:inline-flex;gap:.5rem;align-items:center" id="videoFilterForm">
                    <select id="videoMonthFilter" style="padding:.4rem .8rem;border-radius:8px;background:var(--bg-card);color:var(--text);border:1px solid var(--border);font-size:.85rem">
                        @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ (request('video_bulan', now()->month) == $m) ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="grid-3">
                @foreach($videoArchive as $vid)
                @php
                    $vidId = '';
                    if (preg_match('/(?:v=|youtu\.be\/|embed\/|live\/)([\w-]{11})/', $vid->youtube_url, $vm)) $vidId = $vm[1];
                @endphp
                @if($vidId)
                <div class="pub-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                    <div style="border-radius:12px;overflow:hidden;margin-bottom:.8rem">
                        <iframe src="https://www.youtube.com/embed/{{ $vidId }}" style="width:100%;aspect-ratio:16/9;border:none" loading="lazy" allowfullscreen></iframe>
                    </div>
                    <div class="pub-card__title" style="font-size:.9rem">{{ $vid->judul ?? 'Ibadah' }}</div>
                    <div class="pub-card__meta"><span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($vid->tanggal)->translatedFormat('d F Y') }}</span></div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </section>
    @endif

    {{-- GALERI --}}
    @if($galeri->count() && ($settings['section_galeri'] ?? '1') === '1')
    <section class="pub-section" id="galeri">
        <h2 class="pub-section__title" data-aos="fade-right">Galeri Kegiatan</h2>
        <p class="pub-section__subtitle" data-aos="fade-right" data-aos-delay="100">Dokumentasi kegiatan jemaat</p>
        <div class="pub-section__divider" data-aos="fade-right" data-aos-delay="150"></div>
        <div class="gallery-grid">
            @foreach($galeri as $g)
            <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
                <img src="{{ asset('storage/' . $g->foto_url) }}" alt="{{ $g->keterangan }}" loading="lazy">
                <div class="gallery-item__overlay">
                    <div class="gallery-item__caption">{{ $g->keterangan }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- FOOTER --}}
    <footer class="pub-footer" id="kontak">
        <div class="pub-footer__inner">
            <div class="pub-footer__col">
                <h4>{{ $settings['hero_subtitle'] ?? 'GKE Sion' }}</h4>
                <p>{{ $settings['alamat'] ?? '' }}</p>
                @if($settings['telepon'] ?? '')<p><i class="fas fa-phone" style="color:var(--gold);margin-right:8px"></i>{{ $settings['telepon'] }}</p>@endif
                @if($settings['email_kontak'] ?? '')<p><i class="fas fa-envelope" style="color:var(--gold);margin-right:8px"></i>{{ $settings['email_kontak'] }}</p>@endif
            </div>
            <div class="pub-footer__col">
                <h4>Jam Pelayanan</h4>
                <p>{{ $settings['jam_pelayanan'] ?? 'Senin - Jumat: 08.00 - 15.00 WIB' }}</p>
            </div>
            <div class="pub-footer__col">
                <h4>Media Sosial</h4>
                <div class="pub-footer__social">
                    @if($settings['facebook_url'] ?? '')<a href="{{ $settings['facebook_url'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>@endif
                    @if($settings['instagram_url'] ?? '')<a href="{{ $settings['instagram_url'] }}" target="_blank"><i class="fab fa-instagram"></i></a>@endif
                    @if($settings['youtube_url'] ?? '')<a href="{{ $settings['youtube_url'] }}" target="_blank"><i class="fab fa-youtube"></i></a>@endif
                </div>
            </div>
            @if($settings['google_maps_embed'] ?? '')
            <div class="pub-footer__col">
                <h4>Lokasi</h4>
                <div style="border-radius:12px;overflow:hidden;border:1px solid var(--border)">
                    @php
                        $mapSrc = $settings['google_maps_embed'];
                        // Extract src from full iframe embed code
                        if (preg_match('/src=["\']([^"\']+)/', $mapSrc, $mm)) {
                            $mapSrc = $mm[1];
                        }
                        // Convert share links (maps.app.goo.gl, goo.gl/maps) to embed format
                        if (!str_contains($mapSrc, '/embed') && !str_contains($mapSrc, 'output=embed')) {
                            // Use Maps Embed API with the URL as a query
                            $mapSrc = 'https://www.google.com/maps?q=' . urlencode($mapSrc) . '&output=embed';
                        }
                    @endphp
                    <iframe src="{{ $mapSrc }}" style="width:100%;height:200px;border:none" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen sandbox="allow-scripts allow-same-origin allow-popups"></iframe>
                </div>
            </div>
            @endif
        </div>
        <div class="pub-footer__copy">
            &copy; {{ date('Y') }} {{ $settings['hero_subtitle'] ?? 'GKE Sion' }} — Sistem Informasi Gereja SION
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
        window.addEventListener('scroll', () => {
            document.getElementById('pubNav').classList.toggle('scrolled', window.scrollY > 50);
        });
        // Video month filter
        const vf = document.getElementById('videoMonthFilter');
        if (vf) {
            vf.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('video_bulan', this.value);
                url.hash = '';
                window.location.href = url.toString();
            });
        }
    </script>
</body>
</html>
