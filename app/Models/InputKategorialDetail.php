<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputKategorialDetail extends Model
{
    protected $table = 'input_kategorial_detail';

    protected $fillable = [
        'input_kategorial_id', 'kode_akun_id', 'nama_penyetor',
        'keterangan', 'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function inputKategorial()
    {
        return $this->belongsTo(InputKategorial::class, 'input_kategorial_id');
    }

    public function kodeAkun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_id');
    }
}
