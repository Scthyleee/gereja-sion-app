<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'tanggal', 'kode_akun_id', 'tipe', 'jenis_ibadah', 'jadwal_ibadah_id', 'nama_penyetor',
        'jumlah', 'keterangan', 'bulan', 'tahun', 'kantong_dana',
        'status', 'bukti', 'dibuat_oleh', 'diverifikasi_oleh',
    ];
    protected $casts = ['tanggal' => 'date', 'jumlah' => 'decimal:2'];

    public function kodeAkun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_id');
    }

    public function pembuatUser()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function verifikatorUser()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function scopeByPeriode($query, int $tahun, ?int $bulan = null)
    {
        $query->where('tahun', $tahun);
        if ($bulan) $query->where('bulan', $bulan);
        return $query;
    }

    public function scopeByKantong($query, string $kantong)
    {
        return $query->where('kantong_dana', $kantong);
    }
}
