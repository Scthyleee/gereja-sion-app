<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add I(APn).01.02t kode akun if not exists
        $exists = DB::table('kode_akun')->where('kode', 'I(APn).01.02t')->exists();
        if (!$exists) {
            DB::table('kode_akun')->insert([
                'kode'           => 'I(APn).01.02t',
                'parent_kode'    => 'I(APn).01.02',
                'nama'           => 'PS Perpuluhan',
                'tipe'           => 'penerimaan',
                'nomor_up'       => 1,
                'nomor_menu'     => 1,
                'kategori_akun'  => 'mph',
                'sumber_lapangan'=> 'kolekte',
                'aktif'          => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } else {
            // Update name if it was inserted with wrong name
            DB::table('kode_akun')
                ->where('kode', 'I(APn).01.02t')
                ->update(['nama' => 'PS Perpuluhan', 'updated_at' => now()]);
        }

        // Add to jenis_ps_config if not exists
        $psExists = DB::table('jenis_ps_config')->where('kode_akun', 'I(APn).01.02t')->exists();
        if (!$psExists) {
            DB::table('jenis_ps_config')->insert([
                'nama'        => 'PS Perpuluhan',
                'kode_akun'   => 'I(APn).01.02t',
                'kantong_dana'=> 'rutin',
                'urutan'      => 20,
                'aktif'       => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('jenis_ps_config')->where('kode_akun', 'I(APn).01.02t')->delete();
        DB::table('kode_akun')->where('kode', 'I(APn).01.02t')->delete();
    }
};
