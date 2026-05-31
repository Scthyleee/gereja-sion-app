<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PendaftaranNikahInsentif extends Model
{
    protected $table = 'pendaftaran_nikah_insentif';
    protected $fillable = ['pendaftaran_nikah_id', 'jenis_insentif', 'nominal', 'keterangan'];
    protected $casts = ['nominal' => 'decimal:2'];

    public function pendaftaranNikah() { return $this->belongsTo(PendaftaranNikah::class, 'pendaftaran_nikah_id'); }
}
