<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add missing insentif_config for internal Pianis & Operator LCD
        $newConfigs = [
            ['tipe_event' => 'ibadah_biasa', 'jenis_tugas' => 'Pianis',        'nominal' => 75000],
            ['tipe_event' => 'perayaan',     'jenis_tugas' => 'Pianis',        'nominal' => 100000],
            ['tipe_event' => 'pernikahan',   'jenis_tugas' => 'Pianis',        'nominal' => 150000],
            ['tipe_event' => 'ibadah_biasa', 'jenis_tugas' => 'Operator LCD',  'nominal' => 50000],
            ['tipe_event' => 'perayaan',     'jenis_tugas' => 'Operator LCD',  'nominal' => 75000],
            ['tipe_event' => 'pernikahan',   'jenis_tugas' => 'Operator LCD',  'nominal' => 75000],
        ];
        foreach ($newConfigs as $cfg) {
            DB::table('insentif_config')->updateOrInsert(
                ['tipe_event' => $cfg['tipe_event'], 'jenis_tugas' => $cfg['jenis_tugas']],
                ['nominal' => $cfg['nominal'], 'aktif' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2. Standardize slot template names to match petugas_roles
        DB::table('petugas_slot_template')
            ->where('nama_slot', 'Pianis / Pemusik')
            ->update(['nama_slot' => 'Pianis', 'updated_at' => now()]);

        DB::table('petugas_slot_template')
            ->where('nama_slot', 'Operator LCD')
            ->update(['nama_slot' => 'Operator Slide', 'updated_at' => now()]);

        DB::table('petugas_slot_template')
            ->where('nama_slot', 'Operator Multimedia / Livestreaming')
            ->update(['nama_slot' => 'Multimedia', 'updated_at' => now()]);

        // 3. Standardize petugas_roles: Operator LCD → Operator Slide
        DB::table('petugas_roles')
            ->where('jenis_tugas', 'Operator LCD')
            ->update(['jenis_tugas' => 'Operator Slide', 'updated_at' => now()]);

        // 4. Standardize petugas_roles: Pendeta → Pendeta Pelayanan
        DB::table('petugas_roles')
            ->where('jenis_tugas', 'Pendeta')
            ->update(['jenis_tugas' => 'Pendeta Pelayanan', 'updated_at' => now()]);

        // 5. Mark Pendeta internal as is_pendeta_sion = 1
        //    (any petugas with role 'Pendeta Pelayanan' who is not explicitly marked as luar)
        $pendetaIds = DB::table('petugas_roles')
            ->where('jenis_tugas', 'Pendeta Pelayanan')
            ->pluck('petugas_id');
        DB::table('petugas')
            ->whereIn('id_petugas', $pendetaIds)
            ->where('is_pendeta_sion', 0)
            ->update(['is_pendeta_sion' => 1, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Remove added insentif configs (Pianis and Operator LCD internal)
        DB::table('insentif_config')
            ->whereIn('jenis_tugas', ['Pianis', 'Operator LCD'])
            ->delete();
    }
};
