<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset saldo tahunan: 31 Desember jam 23:59
Schedule::command('saldo:reset-yearly')->yearlyOn(12, 31, '23:59');
