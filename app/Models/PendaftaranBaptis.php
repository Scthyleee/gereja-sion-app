<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PendaftaranBaptis extends Model
{
    protected $table = 'pendaftaran_baptis';
    protected $fillable = [
        'warga_id', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'no_telepon', 'pekerjaan',
        'lingkungan_sion_id', 'nama_ayah', 'nama_ibu',
        'saksi_1', 'saksi_2', 'agama_sebelum', 'biaya_baptis',
        'katekisasi_mulai', 'katekisasi_selesai', 'nilai_ujian',
        'status', 'tanggal_baptis_rencana', 'jadwal_ibadah_id',
        'catatan', 'catatan_penolakan', 'scan_formulir_path',
        'disetujui_oleh', 'disetujui_at', 'file_manager_id', 'created_by',
    ];
    protected $casts = [
        'tanggal_lahir' => 'date', 'katekisasi_mulai' => 'date',
        'katekisasi_selesai' => 'date', 'tanggal_baptis_rencana' => 'date',
        'disetujui_at' => 'datetime', 'nilai_ujian' => 'decimal:2',
    ];

    public function warga() { return $this->belongsTo(WargaJemaat::class, 'warga_id', 'id_warga'); }
    public function lingkunganSion() { return $this->belongsTo(LingkunganSion::class, 'lingkungan_sion_id'); }
    public function jadwal() { return $this->belongsTo(JadwalIbadah::class, 'jadwal_ibadah_id', 'id_jadwal'); }
    public function approver() { return $this->belongsTo(User::class, 'disetujui_oleh'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
