<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PenandatanganDokumen extends Model
{
    protected $table = 'penandatangan_dokumen';
    protected $fillable = ['module', 'urutan', 'user_id', 'nama_tampilan', 'jabatan_tampilan', 'foto_ttd', 'qr_enabled', 'aktif'];

    public function user() { return $this->belongsTo(User::class); }
}
