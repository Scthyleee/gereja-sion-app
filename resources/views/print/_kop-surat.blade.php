{{-- Kop Surat dengan Logo GKE --}}
@php
    $profil = $profil ?? (object)['nama_gereja' => 'PALANGKA', 'alamat' => ''];
    $settings = $settings ?? [];
@endphp
<div class="kop">
    @if(file_exists(public_path('images/logo-gke.png')))
    <img src="{{ public_path('images/logo-gke.png') }}" class="kop-logo" alt="Logo GKE">
    @endif
    <h2>GEREJA KALIMANTAN EVANGELIS (GKE)</h2>
    <h3>( Anggota PGI )</h3>
    <h3>{{ $settings['resort'] ?? 'Resort Palangka Raya Tengah' }}</h3>
    <h4>MAJELIS JEMAAT GKE {{ strtoupper($profil->nama_gereja ?? 'PALANGKA') }}</h4>
    <p>{{ $profil->alamat ?? ($settings['alamat_gereja_lengkap'] ?? '') }}</p>
</div>
