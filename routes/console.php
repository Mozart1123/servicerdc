<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('kpay:reconcile')->everyFifteenMinutes();
\Illuminate\Support\Facades\Schedule::command('app:process-subscription-reminders')->dailyAt('00:05');
