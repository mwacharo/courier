<?php

namespace App\Console;

use App\Http\Controllers\Util\EmailUtil;
use App\ManagementCommission;
use App\PresenterCommission;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $schedule->call(function () {

            $order_count = DB::table('orders')
                ->where('order_status', 'scheduled')
                ->whereDate('scheduled_date', Carbon::tomorrow())
                ->count();

            $email_util = new EmailUtil();
            $send_email = $email_util->scheduledOrderNotification($order_count);

            Log::info("*********crone 1*********");
        })->dailyAt('14:00');
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
