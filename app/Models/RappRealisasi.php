<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RappRealisasi extends Model
{
    protected $table = 'rapp_realisasi';

    protected $fillable = [
        'pedoman_rapp_detail_id', 'transaksi_id', 'bulan', 'jumlah', 'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function rappDetail()
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'pedoman_rapp_detail_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
