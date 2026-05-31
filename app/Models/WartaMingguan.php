<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WartaMingguan extends Model
{
    protected $table = 'warta_mingguan';

    protected $fillable = [
        'minggu_ke', 'bulan', 'tahun', 'tanggal_awal', 'tanggal_akhir',
        'saldo_awal_minggu', 'total_penerimaan', 'total_pengeluaran',
        'saldo_akhir_minggu', 'generated_by', 'pdf_path',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
        'saldo_awal_minggu' => 'decimal:2',
        'total_penerimaan' => 'decimal:2',
        'total_pengeluaran' => 'decimal:2',
        'saldo_akhir_minggu' => 'decimal:2',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
