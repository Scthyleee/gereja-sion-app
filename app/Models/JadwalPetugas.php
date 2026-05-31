<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPetugas extends Model
{
    protected $table = 'jadwal_petugas';
    protected $fillable = [
        'jadwal_id', 'petugas_id', 'jenis_tugas', 'slot_nama', 'nominal_insentif', 'is_override',
        'kategori_insentif', 'status_bayar', 'tanggal_bayar', 'sumber',
        'is_external', 'nama_external', 'kategori',
    ];
    protected $casts = ['nominal_insentif' => 'decimal:2', 'is_override' => 'boolean', 'is_external' => 'boolean'];

    public function jadwal()
    {
        return $this->belongsTo(JadwalIbadah::class, 'jadwal_id', 'id_jadwal');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id', 'id_petugas');
    }
}
