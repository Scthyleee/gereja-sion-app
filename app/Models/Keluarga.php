<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    protected $table = 'keluarga';
    protected $fillable = [
        'no_kk', 'no_kk_gereja', 'nama_kepala_keluarga', 'alamat', 'rt', 'rw',
        'kelurahan', 'kecamatan', 'kota', 'lingkungan_sion_id',
        'no_telepon', 'keterangan', 'created_by',
    ];

    public function lingkunganSion() { return $this->belongsTo(LingkunganSion::class, 'lingkungan_sion_id'); }
    public function anggota() { return $this->hasMany(WargaJemaat::class, 'keluarga_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getJumlahAnggotaAttribute() { return $this->anggota()->count(); }
}
