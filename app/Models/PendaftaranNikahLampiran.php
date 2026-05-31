<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PendaftaranNikahLampiran extends Model
{
    protected $table = 'pendaftaran_nikah_lampiran';
    protected $fillable = ['pendaftaran_nikah_id', 'jenis_lampiran', 'file_manager_id', 'keterangan'];

    public function pendaftaranNikah() { return $this->belongsTo(PendaftaranNikah::class, 'pendaftaran_nikah_id'); }
}
