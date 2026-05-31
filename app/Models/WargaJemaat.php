<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WargaJemaat extends Model
{
    protected $table = 'warga_jemaat';
    protected $primaryKey = 'id_warga';
    protected $fillable = [
        'nama_lengkap', 'no_kk', 'lingkungan', 'jenis_kelamin', 'tempat_lahir',
        'tanggal_lahir', 'alamat', 'no_telepon', 'email', 'pekerjaan',
        'status_pernikahan', 'nama_pasangan', 'pasangan_id', 'status_sidi',
        'is_petugas', 'status_jemaat', 'jabatan_gereja', 'tanggal_bergabung', 'foto',
        'keluarga_id', 'hubungan_keluarga', 'lingkungan_sion_id',
        'golongan_darah', 'agama_sebelum', 'status_baptis',
        'tanggal_baptis', 'tanggal_sidi', 'nama_ayah', 'nama_ibu',
    ];
    protected $casts = [
        'tanggal_lahir' => 'date', 'tanggal_bergabung' => 'date',
        'tanggal_baptis' => 'date', 'tanggal_sidi' => 'date',
        'is_petugas' => 'boolean',
    ];

    public function petugas() { return $this->hasOne(Petugas::class, 'warga_id', 'id_warga'); }
    public function pasangan() { return $this->belongsTo(self::class, 'pasangan_id', 'id_warga'); }
    public function keluarga() { return $this->belongsTo(Keluarga::class, 'keluarga_id'); }
    public function lingkunganSion() { return $this->belongsTo(LingkunganSion::class, 'lingkungan_sion_id'); }
}
