<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RappTarget extends Model
{
    protected $table = 'rapp_target';
    protected $fillable = ['tahun', 'jemaat_id', 'jenis_ps', 'target_per_bulan', 'kode_akun_id'];
    protected $casts = ['target_per_bulan' => 'decimal:2'];

    public function jemaat() { return $this->belongsTo(WargaJemaat::class, 'jemaat_id', 'id_warga'); }
    public function kodeAkun() { return $this->belongsTo(KodeAkun::class, 'kode_akun_id'); }
}
