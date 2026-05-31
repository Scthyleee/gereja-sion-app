<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JadwalAmplopDetail extends Model
{
    protected $table = 'jadwal_amplop_detail';
    protected $fillable = ['amplop_id', 'kode_akun_id', 'nominal'];
    protected $casts = ['nominal' => 'decimal:2'];

    public function amplop() { return $this->belongsTo(JadwalAmplop::class, 'amplop_id'); }
    public function kodeAkun() { return $this->belongsTo(KodeAkun::class, 'kode_akun_id'); }
}
