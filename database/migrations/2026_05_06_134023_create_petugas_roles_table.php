<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('petugas_id');
            $table->string('jenis_tugas');
            $table->unsignedBigInteger('kode_akun_id')->nullable();
            $table->decimal('insentif', 12, 0)->default(0);
            $table->date('berlaku_mulai')->nullable()->comment('Tanggal mulai berlaku insentif ini');
            $table->date('berlaku_sampai')->nullable()->comment('Null = masih aktif');
            $table->timestamps();

            $table->foreign('petugas_id')->references('id_petugas')->on('petugas')->onDelete('cascade');
            $table->index(['petugas_id', 'jenis_tugas']);
        });

        // Migrate existing data from petugas table to petugas_roles
        $existing = DB::table('petugas')->select('id_petugas', 'jenis_tugas', 'kode_akun_id', 'insentif')->get();
        foreach ($existing as $p) {
            DB::table('petugas_roles')->insert([
                'petugas_id' => $p->id_petugas,
                'jenis_tugas' => $p->jenis_tugas,
                'kode_akun_id' => $p->kode_akun_id,
                'insentif' => $p->insentif ?? 0,
                'berlaku_mulai' => '2026-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas_roles');
    }
};
