<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send daily summaries at 7:00 AM South African time
        $schedule->command('activities:send-summaries')
            ->dailyAt('07:00')
            ->timezone('Africa/Johannesburg')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/daily-summaries.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
