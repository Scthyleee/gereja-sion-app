<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Only check for admin routes (non-Super Admin)
        if ($request->is('admin/*') || $request->is('admin')) {
            $maintenance = DB::table('cms_settings')
                ->where('key', 'maintenance_admin')
                ->value('value');

            if ($maintenance === '1') {
                // Super Admin (role_id 1) always bypasses
                if (auth()->check() && auth()->user()->role_id === 1) {
                    return $next($request);
                }

                // Non-super admin during maintenance
                if (auth()->check()) {
                    auth()->logout();
                    $request->session()->invalidate();
                }

                return redirect()->route('login')
                    ->withErrors(['email' => 'Admin panel sedang dalam maintenance. Silakan hubungi Super Admin.']);
            }
        }

        return $next($request);
    }
}
