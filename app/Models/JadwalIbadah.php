<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalIbadah extends Model
{
    protected $table = 'jadwal_ibadah';
    protected $primaryKey = 'id_jadwal';
    protected $fillable = [
        'tanggal', 'jenis_ibadah', 'waktu', 'keterangan', 'lokasi', 'jumlah_hadir', 'status',
        'kategori', 'lokasi_tipe', 'lokasi_alamat', 'warga_terkait_id',
        'pendaftaran_nikah_id', 'pendaftaran_baptis_id',
        'penanggung_jawab', 'contact_person', 'no_telepon_cp',
        'jam_selesai', 'dress_code', 'tema_ibadah', 'ayat_khotbah', 'foto_ttd_path',
        'judul_ibadah', 'kode_akun_insentif_id',
    ];
    protected $casts = ['tanggal' => 'date'];

    /**
     * Display name: judul_ibadah if set, otherwise jenis_ibadah
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->judul_ibadah ?: $this->jenis_ibadah;
    }

    public function petugas()
    {
        return $this->hasMany(JadwalPetugas::class, 'jadwal_id', 'id_jadwal');
    }

    public function kantong()
    {
        return $this->hasMany(JadwalKantong::class, 'jadwal_id', 'id_jadwal');
    }

    public function amplop()
    {
        return $this->hasMany(JadwalAmplop::class, 'jadwal_id', 'id_jadwal');
    }

    public function getTotalKantongAttribute(): float
    {
        return $this->kantong->sum('total');
    }

    public function getTotalAmplopAttribute(): float
    {
        return $this->amplop->sum(fn($a) => $a->detail->sum('nominal'));
    }

    public function getTotalPersembahanAttribute(): float
    {
        return $this->total_kantong + $this->total_amplop;
    }

    public function getTotalInsentifAttribute(): float
    {
        return $this->petugas->sum('nominal_insentif');
    }

}
