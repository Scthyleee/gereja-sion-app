<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TransaksiKK extends Model
{
    protected $table = 'transaksi_kk';
    protected $fillable = [
        'tanggal', 'bulan', 'minggu_ke', 'jenis_ibadah', 'kode_akun_id',
        'jumlah', 'jml_kantong', 'petugas_hitung', 'diterima_oleh', 'keterangan',
        'status', 'jemaat_id', 'jadwal_ibadah_id', 'transaksi_id', 'created_by',
    ];
    protected $casts = ['tanggal' => 'date', 'jumlah' => 'decimal:2'];

    public function kodeAkun() { return $this->belongsTo(KodeAkun::class, 'kode_akun_id'); }
    public function jemaat() { return $this->belongsTo(WargaJemaat::class, 'jemaat_id', 'id_warga'); }
    public function jadwal() { return $this->belongsTo(JadwalIbadah::class, 'jadwal_ibadah_id', 'id_jadwal'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Calculate minggu_ke from a date: 1-7 = week 1, 8-14 = week 2, etc.
     */
    public static function calculateMingguKe(\Carbon\Carbon $date): int
    {
        $day = $date->day;
        if ($day <= 7) return 1;
        if ($day <= 14) return 2;
        if ($day <= 21) return 3;
        if ($day <= 28) return 4;
        return 5;
    }
}
