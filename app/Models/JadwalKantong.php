<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKantong extends Model
{
    protected $table = 'jadwal_kantong';
    protected $fillable = ['jadwal_id', 'kode_akun_id', 'label', 'total'];
    protected $casts = ['total' => 'decimal:2'];

    public function jadwal()
    {
        return $this->belongsTo(JadwalIbadah::class, 'jadwal_id', 'id_jadwal');
    }

    public function kodeAkun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_id');
    }
}
