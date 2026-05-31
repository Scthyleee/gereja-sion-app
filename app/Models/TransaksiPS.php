<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TransaksiPS extends Model
{
    protected $table = 'transaksi_ps';
    protected $fillable = [
        'jemaat_id', 'tanggal', 'bulan', 'jenis_ps', 'kode_akun_id', 'kantong_dana',
        'jumlah', 'diterima_oleh', 'no_kuitansi', 'status', 'sumber',
        'jadwal_ibadah_id', 'transaksi_id', 'created_by',
    ];
    protected $casts = ['tanggal' => 'date', 'jumlah' => 'decimal:2'];

    public function jemaat() { return $this->belongsTo(WargaJemaat::class, 'jemaat_id', 'id_warga'); }
    public function kodeAkun() { return $this->belongsTo(KodeAkun::class, 'kode_akun_id'); }
    public function jadwal() { return $this->belongsTo(JadwalIbadah::class, 'jadwal_ibadah_id', 'id_jadwal'); }
    public function transaksi() { return $this->belongsTo(Transaksi::class, 'transaksi_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
