{{-- Modal Petugas Internal --}}
<div class="modal fade" id="modalPetugas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Petugas (Internal)</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.petugas.store', $jadwal) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slot Tugas</label>
                        <select name="slot_nama" class="form-select" id="selSlot">
                            <option value="">— Custom —</option>
                            @foreach($slotTemplates as $s)
                                <option value="{{ $s->nama_slot }}">{{ $s->nama_slot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="customTugasRow">
                        <label class="form-label fw-semibold">Jenis Tugas</label>
                        <input type="text" name="jenis_tugas" class="form-control" id="inpJenisTugas">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Petugas</label>
                        <select name="petugas_id" class="form-select select-search" required id="selPetugas">
                            <option value="">Pilih petugas...</option>
                            @foreach($petugasList as $p)
                                @php $cnt = $petugasCount[$p->id_petugas] ?? 0; $roles = $p->roles->pluck('jenis_tugas')->join(', '); @endphp
                                <option value="{{ $p->id_petugas }}" data-roles="{{ $roles }}">
                                    {{ $p->nama_petugas }}{{ $p->is_pendeta_sion ? ' (Sion)' : '' }} — {{ $roles ?: 'Belum ada job' }} ({{ $cnt }}x)
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Quota: {{ $quota }}x/bulan</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Insentif</label>
                        <input type="number" name="nominal_insentif" id="inpInsentif" class="form-control" min="0">
                        <small class="text-muted">Nominal diambil dari Setting Insentif berdasarkan tipe event. Kosongkan untuk auto-calculate. Pendeta Sion = Rp 0 otomatis</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Tambah</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Petugas External (Pendeta/Pianis Luar) --}}
<div class="modal fade" id="modalExternal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Petugas Luar</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.petugas-external.store', $jadwal) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Petugas (Luar)</label>
                        <input type="text" name="nama_external" class="form-control" required placeholder="Nama pendeta/pianis luar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas</label>
                        <select name="jenis_tugas" class="form-select" required>
                            <option value="Pendeta Pelayanan (Luar)">Pendeta Pelayanan (Luar)</option>
                            <option value="Pianis (Luar)">Pianis (Luar)</option>
                            <option value="Organis (Luar)">Organis (Luar)</option>
                            @foreach($insentifConfig as $ic)
                                @if(str_contains($ic->jenis_tugas, 'Luar'))
                                @else
                                <option value="{{ $ic->jenis_tugas }}">{{ $ic->jenis_tugas }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Insentif (Rp)</label>
                        <input type="number" name="nominal_insentif" class="form-control" min="0" required>
                        <small class="text-muted">Tipe event: {{ $tipeEvent }} — Isi nominal sesuai kesepakatan</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-warning">Tambah Luar</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Petugas Pelayanan (Diakon/Penetua) --}}
<div class="modal fade" id="modalPelayanan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Petugas Pelayanan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.petugas-pelayanan.store', $jadwal) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas</label>
                        <select name="jenis_tugas" class="form-select" required>
                            <option value="Pengedar Kolekte">Pengedar Kolekte</option>
                            <option value="Penghitung Jemaat">Penghitung Jemaat</option>
                            <option value="Pembacaan Alkitab">Pembacaan Alkitab</option>
                            <option value="Liturgos">Liturgos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Diakon/Penetua</label>
                        <select name="warga_id" class="form-select" required>
                            <option value="">Pilih Diakon/Penetua...</option>
                            @foreach($diakonPenetuaList as $dp)
                                <option value="{{ $dp->id_warga }}">{{ $dp->jabatan_gereja === 'Diakon' ? 'Dkn.' : 'Pnt.' }} {{ $dp->nama_lengkap }} — {{ $dp->lingkungan ?? '-' }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Tanpa insentif (pelayanan wajib)</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Tambah</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Random --}}
<div class="modal fade" id="modalRandom" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold"><i class="fas fa-random me-2"></i>Random Petugas Pelayanan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.petugas-random', $jadwal) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas</label>
                        <select name="jenis_tugas" class="form-select" required>
                            <option value="Pengedar Kolekte">Pengedar Kolekte</option>
                            <option value="Penghitung Jemaat">Penghitung Jemaat</option>
                            <option value="Pembacaan Alkitab">Pembacaan Alkitab</option>
                            <option value="Liturgos">Liturgos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" value="2" min="1" max="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mode</label>
                        <select name="mode" class="form-select" id="selRandomMode">
                            <option value="all">Random Semua Diakon/Penetua</option>
                            <option value="lingkungan">Random Per Lingkungan</option>
                        </select>
                    </div>
                    <div class="mb-3" id="rowLingkungan" style="display:none">
                        <label class="form-label fw-semibold">Lingkungan</label>
                        <input type="text" name="lingkungan" class="form-control" placeholder="Nama lingkungan">
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-info">Random!</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Kantong --}}
<div class="modal fade" id="modalKantong" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Kantong Kolekte</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.kantong.store', $jadwal) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Akun Kolekte</label>
                        <select name="kode_akun_id" class="form-select select-search" required>
                            <option value="">Pilih kolekte...</option>
                            @foreach($akunKolekte as $a)
                                <option value="{{ $a->id }}">{{ $a->keterangan ?: $a->nama }} ({{ $a->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Total (Rp)</label>
                        <input type="number" name="total" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Tambah</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Amplop PS (using jenis_ps_config) --}}
<div class="modal fade" id="modalAmplop" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title fw-bold">Tambah Amplop Persembahan Syukur</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('jadwal.amplop.store', $jadwal) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pemberi</label>
                            <input type="text" name="nama_pemberi" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warga (opsional — pilih untuk auto-isi nama)</label>
                            <select name="warga_id" class="form-select select-search" id="selWargaAmplop" onchange="autoFillNamaPemberi(this)">
                                <option value="">Non-jemaat</option>
                                @foreach($wargaList as $w)
                                    <option value="{{ $w->id_warga }}" data-nama="{{ $w->nama_lengkap }}">{{ $w->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label class="form-label fw-semibold">Rincian per Jenis PS</label>
                    <div id="amplopRows">
                        <div class="row g-2 mb-2 amplop-row">
                            <div class="col-md-8">
                                <select name="jenis_ps_ids[]" class="form-select form-select-sm" required>
                                    <option value="">Pilih jenis PS...</option>
                                    @foreach($jenisPS as $jp)
                                        <option value="{{ $jp->id }}">{{ $jp->nama }} ({{ $jp->kode_akun }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="nominals[]" class="form-control form-control-sm" placeholder="Nominal" min="0" required>
                            </div>
                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.amplop-row').remove()"><i class="fas fa-times"></i></button></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addAmplopRow()"><i class="fas fa-plus me-1"></i>Tambah Jenis PS</button>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <input type="text" name="catatan" class="form-control" placeholder="(opsional)">
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border)"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('selRandomMode')?.addEventListener('change', function() {
    document.getElementById('rowLingkungan').style.display = this.value === 'lingkungan' ? 'block' : 'none';
});

// Filter petugas dropdown by selected slot/jenis_tugas
const selSlot = document.getElementById('selSlot');
const selPetugas = document.getElementById('selPetugas');
const inpJenisTugas = document.getElementById('inpJenisTugas');
const inpInsentif = document.getElementById('inpInsentif');
const customRow = document.getElementById('customTugasRow');

if (selSlot && selPetugas) {
    // Store all original options
    const allOptions = Array.from(selPetugas.querySelectorAll('option'));

    selSlot.addEventListener('change', function() {
        const slotName = this.value;
        // Show/hide custom jenis_tugas field
        if (customRow) customRow.style.display = slotName ? 'none' : 'block';
        if (slotName && inpJenisTugas) inpJenisTugas.value = slotName;

        // Filter petugas options by matching jenis_tugas
        selPetugas.innerHTML = '';
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Pilih petugas...';
        selPetugas.appendChild(defaultOpt);

        allOptions.forEach(opt => {
            if (!opt.value) return; // skip placeholder
            const tugas = opt.dataset.tugas || '';
            if (!slotName || tugas.toLowerCase().includes(slotName.toLowerCase()) || slotName.toLowerCase().includes(tugas.toLowerCase())) {
                selPetugas.appendChild(opt.cloneNode(true));
            }
        });
    });

    // Auto-fill insentif when petugas is selected
    selPetugas.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.insentif && inpInsentif) {
            inpInsentif.value = opt.dataset.insentif;
        }
    });
}
</script>

