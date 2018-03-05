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
        //每10分钟刷新淘宝token
        $schedule->command('refresh_taobao_token')->everyTenMinutes();
//        //每2分钟增量同步订单
//        $schedule->command('sync_order', ['--increase'=> 1])->cron("*/2 * * * * *");
//        //每5分钟更新未结算订单
//        $schedule->command('sync_order', ['--update'=> 1])->everyFiveMinutes();

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
