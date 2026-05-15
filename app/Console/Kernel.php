<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string|string>
     */
    protected $commands = [
        // Register your command here
        \App\Console\Commands\CreateMonthlyBills::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run the monthly bills command on the 1st of every month at midnight
        $schedule->command('bills:create-monthly')->monthlyOn(1, '00:00');
        $schedule->command('cyber:cleanup')->daily();

        // Optional: add other scheduled tasks here
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