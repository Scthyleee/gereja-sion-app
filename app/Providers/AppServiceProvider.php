<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $profil = null;
            $logo = '';
            try {
                if (Schema::hasTable('profil_gereja')) {
                    $profil = DB::table('profil_gereja')->first();
                }
                if (Schema::hasTable('system_settings')) {
                    $logoRow = DB::table('system_settings')->where('setting_key', 'logo_gereja')->first();
                    $logo = $logoRow->setting_value ?? '';
                }
            } catch (\Exception $e) {}

            $view->with('__profil', $profil);
            $view->with('__logo', $logo);
        });
    }
}
