<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    protected $table = 'kode_akun';
    protected $fillable = ['kode', 'parent_kode', 'tipe', 'nomor_up', 'nomor_menu', 'nama', 'keterangan', 'sumber_lapangan', 'kategori_akun', 'aktif'];

    public function unitPelayanan()
    {
        return $this->belongsTo(UnitPelayanan::class, 'nomor_up', 'nomor_up');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_kode', 'kode');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_kode', 'kode');
    }

    public function scopeParentsOnly($query)
    {
        return $query->whereNull('parent_kode');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kode_akun_id');
    }

    // Scope: only penerimaan
    public function scopePenerimaan($query)
    {
        return $query->where('tipe', 'penerimaan');
    }

    // Scope: only pengeluaran
    public function scopePengeluaran($query)
    {
        return $query->where('tipe', 'pengeluaran');
    }

    // Scope: by UP number
    public function scopeByUp($query, int $nomor_up)
    {
        return $query->where('nomor_up', $nomor_up);
    }

    // Scope: insentif/honor codes only (II(APg).XX.01)
    public function scopeInsentif($query)
    {
        return $query->where('tipe', 'pengeluaran')->where('kode', 'LIKE', 'II(APg).%.01');
    }

    // Scope: kolekte ibadah (flagged via sumber_lapangan='kolekte')
    public function scopeKolekte($query)
    {
        return $query->where('tipe', 'penerimaan')->where('sumber_lapangan', 'kolekte');
    }

    // Scope: persembahan syukur (I(APn).XX.02)
    public function scopePersembahanSyukur($query)
    {
        return $query->where('tipe', 'penerimaan')->where('kode', 'LIKE', 'I(APn).%.02');
    }

    // Scope: filter by unit pelayanan
    public function scopeByUnitPelayanan($query, int $nomor_up)
    {
        return $query->where('nomor_up', $nomor_up);
    }

    // Get display label: "I(APn).01.01 — Kolekte ibadah"
    public function getLabelAttribute(): string
    {
        return "{$this->kode} — {$this->nama}";
    }
}
