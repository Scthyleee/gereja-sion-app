<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalAmplop extends Model
{
    protected $table = 'jadwal_amplop';
    protected $fillable = ['jadwal_id', 'warga_id', 'nama_pemberi', 'catatan'];

    public function jadwal()
    {
        return $this->belongsTo(JadwalIbadah::class, 'jadwal_id', 'id_jadwal');
    }

    public function warga()
    {
        return $this->belongsTo(WargaJemaat::class, 'warga_id', 'id_warga');
    }

    public function detail()
    {
        return $this->hasMany(JadwalAmplopDetail::class, 'amplop_id');
    }

    public function getTotalAttribute(): float
    {
        return $this->detail->sum('nominal');
    }
}
