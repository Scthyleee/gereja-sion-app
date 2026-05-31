<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    protected $fillable = ['warga_id', 'nama_petugas', 'no_telepon', 'status', 'kategori_petugas', 'is_pendeta_sion'];
    protected $casts = ['is_pendeta_sion' => 'boolean'];

    public function warga() { return $this->belongsTo(WargaJemaat::class, 'warga_id', 'id_warga'); }
    public function jadwalPetugas() { return $this->hasMany(JadwalPetugas::class, 'petugas_id', 'id_petugas'); }
    public function roles() { return $this->hasMany(PetugasRole::class, 'petugas_id', 'id_petugas'); }
    public function activeRoles() { return $this->roles()->aktif(); }
}
