<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    protected $fillable = ['transaksi_id', 'uraian', 'qty', 'satuan', 'harga_satuan', 'jumlah'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
