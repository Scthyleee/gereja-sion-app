<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPelayanan extends Model
{
    protected $table = 'unit_pelayanan';
    protected $fillable = ['nomor_up', 'kode_apn', 'kode_apg', 'inisial', 'nama', 'aktif'];

    public function kodeAkun()
    {
        return $this->hasMany(KodeAkun::class, 'nomor_up', 'nomor_up');
    }

    public function kodeAkunPenerimaan()
    {
        return $this->hasMany(KodeAkun::class, 'nomor_up', 'nomor_up')->where('tipe', 'penerimaan');
    }

    public function kodeAkunPengeluaran()
    {
        return $this->hasMany(KodeAkun::class, 'nomor_up', 'nomor_up')->where('tipe', 'pengeluaran');
    }
}
