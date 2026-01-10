<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Send daily summaries at 7:00 AM South African time
Schedule::command('activities:send-summaries')
    ->dailyAt('07:00')
    ->timezone('Africa/Johannesburg')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/daily-summaries.log'));
