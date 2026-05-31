<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['nama', 'email', 'password', 'role_id', 'foto', 'status', 'bio', 'no_telepon', 'alamat', 'warga_id', 'unit_pelayanan_id'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'last_login' => 'datetime'];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function unitPelayanan()
    {
        return $this->belongsTo(UnitPelayanan::class, 'unit_pelayanan_id', 'nomor_up');
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role->nama_role ?? '';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role_name, $roles);
    }

    /**
     * Check if user has a kategorial role (SPPer, SPA, SPRP, etc.)
     */
    public function isKategorial(): bool
    {
        return in_array($this->role_name, [
            'SPPer', 'SPA', 'SPRP', 'SPB', 'SPUI',
            'Pembangunan', 'PHBG', 'KPI/PKB', 'Ketua MPH',
        ]);
    }

    /**
     * Check if user can validate/approve input kategorial
     */
    public function canValidate(): bool
    {
        if ($this->hasRole('Super Admin')) return true;

        $approverRoleIds = \Illuminate\Support\Facades\DB::table('approval_settings')
            ->where('module', 'input_kategorial')
            ->where('aktif', true)
            ->whereNotNull('role_id')
            ->pluck('role_id')
            ->toArray();

        return in_array($this->role_id, $approverRoleIds);
    }

    /**
     * Check if user can view a page by page_slug
     */
    public function canAccess(string $pageSlug): bool
    {
        // Super Admin always has access
        if ($this->role_id === 1) return true;

        $perm = \Illuminate\Support\Facades\DB::table('page_permissions')
            ->where('role_id', $this->role_id)
            ->where('page_slug', $pageSlug)
            ->first();

        return $perm && $perm->can_view;
    }

    /**
     * Check if user can edit on a page by page_slug
     */
    public function canEdit(string $pageSlug): bool
    {
        if ($this->role_id === 1) return true;

        $perm = \Illuminate\Support\Facades\DB::table('page_permissions')
            ->where('role_id', $this->role_id)
            ->where('page_slug', $pageSlug)
            ->first();

        return $perm && $perm->can_edit;
    }
}
