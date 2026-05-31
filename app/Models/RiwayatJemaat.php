<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RiwayatJemaat extends Model
{
    protected $table = 'riwayat_jemaat';
    public $timestamps = false;
    protected $fillable = ['jemaat_id', 'tahun', 'bulan', 'jenis_ps', 'kode_akun', 'jumlah', 'transaksi_ps_id'];

    public function jemaat() { return $this->belongsTo(WargaJemaat::class, 'jemaat_id', 'id_warga'); }
    public function transaksiPs() { return $this->belongsTo(TransaksiPS::class, 'transaksi_ps_id'); }
}
