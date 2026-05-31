<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsentifConfig extends Model
{
    protected $table = 'insentif_config';
    protected $fillable = ['tipe_event', 'jenis_tugas', 'nominal', 'aktif'];
    protected $casts = ['nominal' => 'decimal:2', 'aktif' => 'boolean'];

    /**
     * Get nominal for a specific event type + job type
     */
    public static function getNominal(string $tipeEvent, string $jenisTugas): float
    {
        $config = self::where('tipe_event', $tipeEvent)
            ->where('jenis_tugas', $jenisTugas)
            ->where('aktif', true)
            ->first();

        return $config ? (float) $config->nominal : 0;
    }
}
