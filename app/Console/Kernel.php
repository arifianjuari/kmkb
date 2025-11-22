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
        // $schedule->command('inspire')->hourly();
        
        // Schedule SIMRS cost references sync daily at 2 AM
        $schedule->command('cost-references:sync-from-simrs --limit=1000')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->description('Sync cost references from SIMRS');
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
