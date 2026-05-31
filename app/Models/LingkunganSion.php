<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LingkunganSion extends Model
{
    protected $table = 'lingkungan_sion';
    protected $fillable = ['nama', 'ketua_id', 'bendahara_id', 'sekretaris_id', 'wilayah_deskripsi', 'wilayah', 'keterangan', 'aktif'];

    public function ketua() { return $this->belongsTo(User::class, 'ketua_id'); }
    public function bendahara() { return $this->belongsTo(User::class, 'bendahara_id'); }
    public function sekretaris() { return $this->belongsTo(User::class, 'sekretaris_id'); }
    public function warga() { return $this->hasMany(WargaJemaat::class, 'lingkungan_sion_id'); }
    public function keluarga() { return $this->hasMany(Keluarga::class, 'lingkungan_sion_id'); }
}
