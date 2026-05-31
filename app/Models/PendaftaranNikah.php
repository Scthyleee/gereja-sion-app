<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PendaftaranNikah extends Model
{
    protected $table = 'pendaftaran_nikah';
    protected $guarded = ['id'];
    protected $casts = [
        'pria_tanggal_lahir' => 'date', 'wanita_tanggal_lahir' => 'date',
        'tanggal_nikah_rencana' => 'date', 'divalidasi_at' => 'datetime',
        'biaya_administrasi' => 'decimal:2', 'biaya_fasilitas' => 'decimal:2',
    ];

    public function priaWarga() { return $this->belongsTo(WargaJemaat::class, 'pria_warga_id', 'id_warga'); }
    public function wanitaWarga() { return $this->belongsTo(WargaJemaat::class, 'wanita_warga_id', 'id_warga'); }
    public function jadwal() { return $this->belongsTo(JadwalIbadah::class, 'jadwal_ibadah_id', 'id_jadwal'); }
    public function validator() { return $this->belongsTo(User::class, 'divalidasi_oleh'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function insentif() { return $this->hasMany(PendaftaranNikahInsentif::class, 'pendaftaran_nikah_id'); }
    public function lampiran() { return $this->hasMany(PendaftaranNikahLampiran::class, 'pendaftaran_nikah_id'); }

    public function getTotalBiayaAttribute() { return $this->biaya_administrasi + $this->biaya_fasilitas; }
    public function getTotalInsentifAttribute() { return $this->insentif->sum('nominal'); }
    public function getJudulJadwalAttribute() {
        return "Ibadah Pernikahan, {$this->pria_nama} dan {$this->wanita_nama} — Gereja Sion";
    }
}
