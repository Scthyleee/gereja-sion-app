<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\PenandatanganDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'umum');

        $profil = DB::table('profil_gereja')->first();
        $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');
        $users = User::with('role')->orderBy('nama')->get();
        $roles = Role::all();
        // Tables dropped — use empty collections for backward view compatibility
        $pengurus = collect();
        $auditLog = collect();

        // Access control
        $pages = [
            'dashboard' => 'Dashboard',
            'jadwal' => 'Jadwal & Pelayanan',
            'jemaat' => 'Data Jemaat',
            'keuangan' => 'Keuangan',
            'transaksi' => 'Transaksi',
            'pendaftaran' => 'Pendaftaran',
            'persidangan' => 'Persidangan',
            'pedoman' => 'Pedoman & RAPP',
            'files' => 'File Manager',
            'laporan' => 'Cetak Laporan',
        ];
        $permissions = DB::table('page_permissions')->get();

        // Slot templates
        $slotTemplates = DB::table('petugas_slot_template')->orderBy('urutan')->get();

        // Unit pelayanan for RAPP access
        $unitList = DB::table('unit_pelayanan')->where('aktif', true)->orderBy('nomor_up')->get();

        // Approval settings
        $approvalSettings = DB::table('approval_settings')
            ->leftJoin('roles', 'approval_settings.role_id', '=', 'roles.id')
            ->select('approval_settings.*', 'roles.nama_role')
            ->orderBy('module')->orderBy('urutan')->get();

        // Penandatangan dokumen
        $penandatangan = PenandatanganDokumen::where('aktif', true)->orderBy('module')->orderBy('urutan')->get();

        // Handle delete_ttd via GET
        if ($request->has('delete_ttd')) {
            PenandatanganDokumen::where('id', $request->delete_ttd)->delete();
            return redirect()->route('settings.index', ['tab' => 'ttd'])->with('success', 'Penandatangan dihapus.');
        }

        // CMS settings for maintenance
        $cmsSettings = DB::table('cms_settings')->pluck('value', 'key');

        return view('settings.index', compact(
            'tab', 'profil', 'settings', 'users', 'roles', 'pengurus', 'auditLog',
            'pages', 'permissions', 'slotTemplates', 'unitList', 'approvalSettings',
            'penandatangan', 'cmsSettings'
        ));
    }

    public function updateProfil(Request $request)
    {
        DB::table('profil_gereja')->where('id', 1)->update([
            'nama_gereja' => $request->nama_gereja,
            'alamat' => $request->alamat,
            'telepon' => $request->no_telepon,
            'email' => $request->email,
            'website' => $request->website,
            'updated_at' => now(),
        ]);

        $keys = ['resort', 'nama_majelis', 'alamat_gereja_lengkap', 'kota', 'nama_bendahara'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                DB::table('system_settings')->updateOrInsert(
                    ['setting_key' => $key],
                    ['setting_value' => $request->$key, 'updated_at' => now()]
                );
            }
        }

        return back()->with('success', 'Profil gereja berhasil diupdate.');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => 'aktif',
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->only('nama', 'email', 'role_id', 'status');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return back()->with('success', 'User berhasil diupdate.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);

        // Only Super Admin (role_id 1) can reset others
        if (auth()->user()->role_id !== 1) {
            return back()->with('error', 'Hanya Super Admin yang bisa mereset password.');
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', "Password {$user->nama} berhasil direset.");
    }

    // --- ACCESS CONTROL ---
    public function updatePermissions(Request $request)
    {
        // Clear existing role permissions
        DB::table('page_permissions')->whereNotNull('role_id')->whereNull('user_id')->delete();

        if ($request->has('perms')) {
            foreach ($request->perms as $roleId => $pages) {
                foreach ($pages as $slug => $flags) {
                    DB::table('page_permissions')->insert([
                        'page_slug' => $slug,
                        'role_id' => $roleId,
                        'can_view' => isset($flags['view']),
                        'can_edit' => isset($flags['edit']),
                        'can_approve' => isset($flags['approve']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Hak akses berhasil diupdate.');
    }

    // --- PETUGAS SETTINGS ---
    public function updatePetugasSettings(Request $request)
    {
        // Quota
        if ($request->has('petugas_quota_per_bulan')) {
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => 'petugas_quota_per_bulan'],
                ['setting_value' => $request->petugas_quota_per_bulan, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Pengaturan petugas diupdate.');
    }

    // --- SLOT TEMPLATE ---
    public function storeSlot(Request $request)
    {
        $request->validate(['nama_slot' => 'required|string|max:100']);
        $maxUrut = DB::table('petugas_slot_template')->max('urutan') ?? 0;

        DB::table('petugas_slot_template')->insert([
            'nama_slot' => $request->nama_slot,
            'jumlah_default' => $request->jumlah_default ?? 1,
            'urutan' => $maxUrut + 1,
            'aktif' => true,
            'catatan' => $request->catatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Slot petugas ditambahkan.');
    }

    public function updateSlot(Request $request, int $id)
    {
        DB::table('petugas_slot_template')->where('id', $id)->update([
            'nama_slot' => $request->nama_slot,
            'jumlah_default' => $request->jumlah_default ?? 1,
            'aktif' => $request->has('aktif'),
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Slot diupdate.');
    }

    public function destroySlot(int $id)
    {
        DB::table('petugas_slot_template')->where('id', $id)->delete();
        return back()->with('success', 'Slot dihapus.');
    }

    // --- APPROVAL SETTINGS ---
    public function updateApprovalSettings(Request $request)
    {
        // Clear existing
        DB::table('approval_settings')->delete();

        $modules = ['input_kategorial', 'rapp'];

        foreach ($modules as $module) {
            $roleIds = $request->input("approval.{$module}", []);
            $urutan = 1;
            foreach ($roleIds as $roleId) {
                DB::table('approval_settings')->insert([
                    'module'  => $module,
                    'role_id' => $roleId,
                    'urutan'  => $urutan++,
                    'aktif'   => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('settings.index', ['tab' => 'validasi'])->with('success', 'Pengaturan validasi berhasil diupdate.');
    }

    // --- STORE USER (enhanced with unit_pelayanan_id) ---
    public function storeUserEnhanced(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role_id' => $request->role_id,
            'unit_pelayanan_id' => $request->unit_pelayanan_id,
            'status' => 'aktif',
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    // --- NIKAH BIAYA SETTINGS ---
    public function updateNikahBiaya(Request $request)
    {
        $keys = ['nikah_biaya_administrasi', 'nikah_biaya_fasilitas', 'nikah_insentif_pianis', 'nikah_insentif_lcd', 'nikah_insentif_sound', 'nikah_insentif_kebersihan'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                DB::table('system_settings')->updateOrInsert(
                    ['setting_key' => $key],
                    ['setting_value' => $request->$key]
                );
            }
        }
        return redirect()->route('settings.index', ['tab' => 'nikah'])->with('success', 'Biaya nikah berhasil diupdate.');
    }

    // --- TANDA TANGAN ---
    public function storeTtd(Request $request)
    {
        $request->validate(['nama_tampilan' => 'required', 'jabatan_tampilan' => 'required', 'module' => 'required']);
        PenandatanganDokumen::create($request->only(['module', 'urutan', 'nama_tampilan', 'jabatan_tampilan', 'qr_enabled']));
        return redirect()->route('settings.index', ['tab' => 'ttd'])->with('success', 'Penandatangan ditambahkan.');
    }

    // --- MAINTENANCE MODE (CMS-based) ---
    public function toggleMaintenance(Request $request)
    {
        $tipe = $request->input('tipe', 'public'); // 'public' or 'admin'
        $mainKey = "maintenance_{$tipe}";
        $durasiKey = "maintenance_{$tipe}_durasi";
        $startKey = "maintenance_{$tipe}_start";

        $currentVal = DB::table('cms_settings')->where('key', $mainKey)->value('value') ?? '0';
        $newVal = $currentVal === '1' ? '0' : '1';

        DB::table('cms_settings')->updateOrInsert(
            ['key' => $mainKey],
            ['value' => $newVal, 'type' => 'boolean', 'updated_at' => now()]
        );

        $label = $tipe === 'admin' ? 'Admin Panel' : 'Public Page';

        if ($newVal === '1') {
            $durasi = max(1, (int)$request->input('durasi', 2));
            DB::table('cms_settings')->updateOrInsert(
                ['key' => $durasiKey],
                ['value' => (string)$durasi, 'type' => 'text', 'updated_at' => now()]
            );
            DB::table('cms_settings')->updateOrInsert(
                ['key' => $startKey],
                ['value' => now()->toIso8601String(), 'type' => 'text', 'updated_at' => now()]
            );
            return redirect()->route('settings.index', ['tab' => 'website'])->with('warning', "Maintenance {$label} AKTIF selama {$durasi} jam.");
        }

        return redirect()->route('settings.index', ['tab' => 'website'])->with('success', "{$label} sudah ONLINE kembali.");
    }
}
