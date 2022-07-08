<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:delete-rooms')
            ->daily();

        $schedule->command('command:update-user-rank')
            ->daily();

        $schedule->command('command:update-room-ranking')
            ->monthlyOn(1, '00:00');

        $schedule->command('command:update-wowza-status')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('command:update-room-status')
            ->everyFiveMinutes();

        $schedule->command('command:update-room-image')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('command:update-movie-views')
            ->everyMinute();
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
