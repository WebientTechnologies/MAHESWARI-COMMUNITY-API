<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        // ...
        \App\Console\Commands\DeleteExpiredNews::class,
        \App\Console\Commands\SendBirthdayWishes::class,
        \App\Console\Commands\SendBirthdayNotifications::class,
        \App\Console\Commands\SendAnniversaryNotifications::class,
        \App\Console\Commands\SendAnniversaryWishes::class,
    ];

    
    protected function schedule(Schedule $schedule)
    {
        
        $schedule->command('send:birthday-wishes')->daily();
        $schedule->command('send:birthday-notification')->daily();
        $schedule->command('send:anniversary-notification')->daily();
        $schedule->command('send:anniversary-wishes')->daily();
        $schedule->command('news:delete-expired')->daily();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
