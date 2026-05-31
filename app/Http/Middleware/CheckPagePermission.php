<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPagePermission
{
    /**
     * Route-prefix → page_slug mapping
     */
    private array $routeMap = [
        'dashboard' => 'dashboard',
        'jadwal' => 'jadwal',
        'petugas' => 'jadwal',
        'insentif' => 'jadwal',
        'jemaat' => 'jemaat',
        'keuangan' => 'keuangan',
        'ps' => 'keuangan',
        'warta' => 'keuangan',
        'baptis' => 'pendaftaran',
        'nikah' => 'pendaftaran',
        'keluarga' => 'pendaftaran',
        'persidangan' => 'persidangan',
        'pedoman' => 'pedoman',
        'rapp' => 'pedoman',
        'files' => 'files',
        'laporan' => 'laporan',
        'lingkungan' => 'jemaat',
        'input-kategorial' => 'keuangan',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        // Super Admin always passes
        if ($user->role_id === 1) return $next($request);

        // Get the route name or first URI segment
        $routeName = $request->route()->getName() ?? '';
        $firstSeg = explode('.', $routeName)[0] ?? '';

        // Find page_slug
        $pageSlug = $this->routeMap[$firstSeg] ?? null;

        // System routes (settings, cms, backup, print) = Super Admin / Admin only
        if (in_array($firstSeg, ['settings', 'cms', 'backup', 'print'])) {
            if (!in_array($user->role_id, [1, 2])) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
            return $next($request);
        }

        // If mapped, check permission
        if ($pageSlug) {
            if (!$user->canAccess($pageSlug)) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }

            // For POST/PUT/DELETE, check can_edit
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if (!$user->canEdit($pageSlug)) {
                    abort(403, 'Anda tidak memiliki hak edit untuk halaman ini.');
                }
            }
        }

        return $next($request);
    }
}
