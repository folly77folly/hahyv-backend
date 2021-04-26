<?php

namespace App\Console;

use App\Console\Commands\UnsubscribeUser;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\SubscriptionExpiry;
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
        Commands\UnsubscribeUser::class,
        Commands\SubscriptionExpiry::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:unSubscribe')->everyMinute();
        $schedule->command('command:subscription_expiry')->daily('07:00');
    }

    /**
     * Register the commands for the application.
     * 
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
