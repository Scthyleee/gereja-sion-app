<?php

namespace App\Http\Controllers;

use App\Models\InputKategorial;
use App\Models\InputKategorialDetail;
use App\Models\KodeAkun;
use App\Models\Transaksi;
use App\Models\UnitPelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputKategorialController extends Controller
{
    /**
     * Role → UP mapping for kategorial users
     */
    private function roleUpMap(): array
    {
        return [
            'SPA'         => 2,
            'SPRP'        => 3,
            'SPPer'       => 4,
            'SPB'         => 5,
            'SPUI'        => 6,
            'KPI/PKB'     => 7,
            'PHBG'        => 8,
            'Pembangunan' => 9,
        ];
    }

    /**
     * Get UP for current user (from role mapping OR user.unit_pelayanan_id)
     */
    private function getUserUp(): ?int
    {
        $user = auth()->user();
        $map = $this->roleUpMap();
        return $map[$user->role_name] ?? $user->unit_pelayanan_id;
    }

    // ─── INDEX ──────────────────────────────────────
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status', '');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $filterUp = $request->get('up', '');

        $query = InputKategorial::with('unitPelayanan', 'pembuat', 'detail')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan);

        // Kategorial users only see their own UP
        if ($user->isKategorial() && !$user->canValidate()) {
            $up = $this->getUserUp();
            if ($up) $query->where('unit_pelayanan_id', $up);
            // Also only own records unless validator
            $query->where('dibuat_oleh', $user->id);
        } elseif ($filterUp) {
            $query->where('unit_pelayanan_id', $filterUp);
        }

        if ($status) $query->where('status', $status);

        $inputs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Pending count for validators
        $pendingCount = 0;
        if ($user->canValidate() || $user->hasRole(['Super Admin', 'Admin'])) {
            $pendingCount = InputKategorial::where('status', 'diajukan')->count();
        }

        $unitList = UnitPelayanan::where('aktif', true)->orderBy('nomor_up')->get();

        return view('input-kategorial.index', compact(
            'inputs', 'status', 'bulan', 'tahun', 'filterUp', 'unitList', 'pendingCount'
        ));
    }

    // ─── CREATE ─────────────────────────────────────
    public function create(Request $request)
    {
        $user = auth()->user();
        $upId = $request->get('up', $this->getUserUp());

        // Admin/Super Admin can select UP
        if ($user->hasRole(['Super Admin', 'Admin', 'Bendahara', 'Staf'])) {
            $upId = $request->get('up', 1); // Default MPH
        }

        $up = UnitPelayanan::where('nomor_up', $upId)->first();
        if (!$up) abort(404, 'Unit Pelayanan tidak ditemukan');

        // Get kode akun for this UP
        $akunPenerimaan = KodeAkun::where('nomor_up', $upId)->where('tipe', 'penerimaan')->where('aktif', true)->orderBy('kode')->get();
        $akunPengeluaran = KodeAkun::where('nomor_up', $upId)->where('tipe', 'pengeluaran')->where('aktif', true)->orderBy('kode')->get();
        $unitList = UnitPelayanan::where('aktif', true)->orderBy('nomor_up')->get();

        // Determine form type based on UP
        $formType = $this->getFormType($upId);

        return view('input-kategorial.create', compact(
            'up', 'akunPenerimaan', 'akunPengeluaran', 'unitList', 'formType'
        ));
    }

    // ─── STORE ──────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'unit_pelayanan_id' => 'required|integer',
            'jenis_input'       => 'required|in:kolekte_ibadah,amplop_ps,amplop_kk,pengeluaran',
            'tanggal'           => 'required|date',
            'akun_ids'          => 'required|array|min:1',
            'akun_ids.*'        => 'exists:kode_akun,id',
            'jumlah'            => 'required|array',
            'jumlah.*'          => 'numeric|min:0',
        ]);

        $tanggal = \Carbon\Carbon::parse($request->tanggal);

        $input = InputKategorial::create([
            'unit_pelayanan_id' => $request->unit_pelayanan_id,
            'jenis_input'       => $request->jenis_input,
            'tanggal'           => $tanggal,
            'minggu_ke'         => $tanggal->weekOfMonth,
            'bulan'             => $tanggal->month,
            'tahun'             => $tanggal->year,
            'jenis_ibadah'      => $request->jenis_ibadah,
            'status'            => 'draft',
            'catatan'           => $request->catatan,
            'dibuat_oleh'       => auth()->id(),
        ]);

        foreach ($request->akun_ids as $i => $akunId) {
            $jumlah = $request->jumlah[$i] ?? 0;
            if ($jumlah > 0) {
                InputKategorialDetail::create([
                    'input_kategorial_id' => $input->id,
                    'kode_akun_id'        => $akunId,
                    'nama_penyetor'       => $request->nama_penyetor[$i] ?? null,
                    'keterangan'          => $request->keterangan_detail[$i] ?? null,
                    'jumlah'              => $jumlah,
                ]);
            }
        }

        return redirect()->route('input-kategorial.show', $input->id)
            ->with('success', 'Input berhasil disimpan sebagai draft.');
    }

    // ─── SHOW ───────────────────────────────────────
    public function show(int $id)
    {
        $input = InputKategorial::with('detail.kodeAkun', 'unitPelayanan', 'pembuat', 'validator')
            ->findOrFail($id);

        $user = auth()->user();
        $canValidate = $user->canValidate() || $user->hasRole('Super Admin');
        $canEdit = $input->dibuat_oleh === $user->id && in_array($input->status, ['draft', 'ditolak']);
        $canDelete = $input->dibuat_oleh === $user->id && in_array($input->status, ['draft', 'ditolak']);

        return view('input-kategorial.show', compact('input', 'canValidate', 'canEdit', 'canDelete'));
    }

    // ─── AJUKAN ─────────────────────────────────────
    public function ajukan(int $id)
    {
        $input = InputKategorial::findOrFail($id);

        if ($input->dibuat_oleh !== auth()->id()) {
            return back()->with('error', 'Hanya pembuat yang bisa mengajukan.');
        }

        if ($input->detail()->count() === 0) {
            return back()->with('error', 'Tidak bisa mengajukan input tanpa detail item.');
        }

        $input->update(['status' => 'diajukan']);
        return back()->with('success', 'Input berhasil diajukan untuk validasi.');
    }

    // ─── APPROVE ────────────────────────────────────
    public function approve(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user->canValidate() && !$user->hasRole('Super Admin')) {
            return back()->with('error', 'Anda tidak berhak memvalidasi.');
        }

        $input = InputKategorial::with('detail.kodeAkun')->findOrFail($id);

        if ($input->status !== 'diajukan') {
            return back()->with('error', 'Hanya input dengan status "diajukan" yang bisa disetujui.');
        }

        DB::transaction(function () use ($input, $user) {
            // Update status
            $input->update([
                'status'          => 'disetujui',
                'divalidasi_oleh' => $user->id,
                'validated_at'    => now(),
            ]);

            // Auto-create transaksi for each detail
            foreach ($input->detail as $d) {
                $akun = $d->kodeAkun;
                if (!$akun || $d->jumlah <= 0) continue;

                // Determine kantong_dana
                $kantong = 'rutin';
                if ($akun->nomor_up == 8) $kantong = 'phbg';
                elseif ($akun->nomor_up == 9) $kantong = 'pembangunan';
                elseif ($akun->nomor_up == 10) $kantong = 'diakonia_sosial';
                elseif ($akun->nomor_up == 11) $kantong = 'diakonia_kedukaan';
                if ($akun->kode === 'I(APn).01.03') $kantong = 'cadangan';

                Transaksi::create([
                    'tanggal'        => $input->tanggal,
                    'kode_akun_id'   => $akun->id,
                    'tipe'           => $akun->tipe,
                    'jumlah'         => $d->jumlah,
                    'keterangan'     => ($d->keterangan ?: $akun->nama) . " — Input " . ($input->unitPelayanan->inisial ?? ''),
                    'nama_penyetor'  => $d->nama_penyetor,
                    'jenis_ibadah'   => $input->jenis_ibadah,
                    'bulan'          => $input->bulan,
                    'tahun'          => $input->tahun,
                    'kantong_dana'   => $kantong,
                    'status'         => 'verified',
                    'dibuat_oleh'    => $input->dibuat_oleh,
                    'diverifikasi_oleh' => $user->id,
                ]);
            }
        });

        return back()->with('success', 'Input disetujui. Transaksi telah dicatat otomatis.');
    }

    // ─── REJECT ─────────────────────────────────────
    public function reject(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user->canValidate() && !$user->hasRole('Super Admin')) {
            return back()->with('error', 'Anda tidak berhak memvalidasi.');
        }

        $request->validate(['catatan_penolakan' => 'required|string|max:500']);

        $input = InputKategorial::findOrFail($id);

        if ($input->status !== 'diajukan') {
            return back()->with('error', 'Hanya input dengan status "diajukan" yang bisa ditolak.');
        }

        $input->update([
            'status'             => 'ditolak',
            'catatan_penolakan'  => $request->catatan_penolakan,
            'divalidasi_oleh'    => $user->id,
            'validated_at'       => now(),
        ]);

        return back()->with('success', 'Input ditolak.');
    }

    // ─── DESTROY ────────────────────────────────────
    public function destroy(int $id)
    {
        $input = InputKategorial::findOrFail($id);

        if ($input->dibuat_oleh !== auth()->id() && !auth()->user()->hasRole('Super Admin')) {
            return back()->with('error', 'Hanya pembuat atau Super Admin yang bisa menghapus.');
        }

        if (!in_array($input->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Hanya input draft atau ditolak yang bisa dihapus.');
        }

        $input->detail()->delete();
        $input->delete();

        return back()->with('success', 'Input berhasil dihapus.');
    }

    // ─── EDIT ───────────────────────────────────────
    public function edit(int $id)
    {
        $input = InputKategorial::with('detail.kodeAkun', 'unitPelayanan')->findOrFail($id);
        $user = auth()->user();

        if ($input->dibuat_oleh !== $user->id) {
            return back()->with('error', 'Hanya pembuat yang bisa mengedit.');
        }
        if (!in_array($input->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Hanya input draft atau ditolak yang bisa diedit.');
        }

        $up = $input->unitPelayanan;
        $akunPenerimaan = KodeAkun::where('nomor_up', $up->nomor_up)->where('tipe', 'penerimaan')->where('aktif', true)->orderBy('kode')->get();
        $akunPengeluaran = KodeAkun::where('nomor_up', $up->nomor_up)->where('tipe', 'pengeluaran')->where('aktif', true)->orderBy('kode')->get();
        $formType = $this->getFormType($up->nomor_up);

        return view('input-kategorial.edit', compact('input', 'up', 'akunPenerimaan', 'akunPengeluaran', 'formType'));
    }

    // ─── UPDATE ─────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $input = InputKategorial::findOrFail($id);
        $user = auth()->user();

        if ($input->dibuat_oleh !== $user->id) {
            return back()->with('error', 'Hanya pembuat yang bisa mengedit.');
        }
        if (!in_array($input->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Hanya input draft atau ditolak yang bisa diedit.');
        }

        $request->validate([
            'jenis_input' => 'required|in:kolekte_ibadah,amplop_ps,amplop_kk,pengeluaran',
            'tanggal'     => 'required|date',
            'akun_ids'    => 'required|array|min:1',
            'jumlah'      => 'required|array',
        ]);

        $tanggal = \Carbon\Carbon::parse($request->tanggal);

        $input->update([
            'jenis_input'  => $request->jenis_input,
            'tanggal'      => $tanggal,
            'minggu_ke'    => $tanggal->weekOfMonth,
            'bulan'        => $tanggal->month,
            'tahun'        => $tanggal->year,
            'jenis_ibadah' => $request->jenis_ibadah,
            'catatan'      => $request->catatan,
            'status'       => 'draft', // Reset to draft after edit
            'catatan_penolakan' => null,
        ]);

        // Replace all details
        $input->detail()->delete();
        foreach ($request->akun_ids as $i => $akunId) {
            $jumlah = $request->jumlah[$i] ?? 0;
            if ($jumlah > 0) {
                InputKategorialDetail::create([
                    'input_kategorial_id' => $input->id,
                    'kode_akun_id'        => $akunId,
                    'nama_penyetor'       => $request->nama_penyetor[$i] ?? null,
                    'keterangan'          => $request->keterangan_detail[$i] ?? null,
                    'jumlah'              => $jumlah,
                ]);
            }
        }

        return redirect()->route('input-kategorial.show', $input->id)
            ->with('success', 'Input berhasil diperbarui.');
    }

    // ─── HELPERS ────────────────────────────────────
    private function getFormType(int $upId): string
    {
        if ($upId === 1) return 'amplop';
        if ($upId === 9) return 'pengeluaran';
        if ($upId === 8) return 'mixed';
        return 'kolekte';
    }
}
