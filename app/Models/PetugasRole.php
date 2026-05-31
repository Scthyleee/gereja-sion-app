<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetugasRole extends Model
{
    protected $table = 'petugas_roles';
    protected $fillable = ['petugas_id', 'jenis_tugas', 'berlaku_mulai', 'berlaku_sampai'];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id', 'id_petugas');
    }

    /**
     * Scope: only active roles (berlaku_sampai is null or future)
     */
    public function scopeAktif($query)
    {
        return $query->where(function($q) {
            $q->whereNull('berlaku_sampai')->orWhere('berlaku_sampai', '>=', now());
        });
    }
}
