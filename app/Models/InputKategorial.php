<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputKategorial extends Model
{
    protected $table = 'input_kategorial';

    protected $fillable = [
        'unit_pelayanan_id', 'jenis_input', 'tanggal', 'minggu_ke',
        'bulan', 'tahun', 'jenis_ibadah', 'status', 'catatan',
        'catatan_penolakan', 'dibuat_oleh', 'divalidasi_oleh', 'validated_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'validated_at' => 'datetime',
    ];

    public function unitPelayanan()
    {
        return $this->belongsTo(UnitPelayanan::class, 'unit_pelayanan_id', 'nomor_up');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    public function detail()
    {
        return $this->hasMany(InputKategorialDetail::class, 'input_kategorial_id');
    }

    // Scopes
    public function scopeByUnit($query, int $up)
    {
        return $query->where('unit_pelayanan_id', $up);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriode($query, int $tahun, ?int $bulan = null)
    {
        $query->where('tahun', $tahun);
        if ($bulan) $query->where('bulan', $bulan);
        return $query;
    }

    public function getTotalAttribute(): float
    {
        return $this->detail->sum('jumlah');
    }

    public function getUpInisialAttribute(): string
    {
        return $this->unitPelayanan->inisial ?? '-';
    }
}
