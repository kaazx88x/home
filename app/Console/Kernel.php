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
        // eg.
        // Commands\Inspire::class,

        Commands\ImportFaveChic::class,
        Commands\UploadImgToS3::class,
        Commands\SecureCodeMigration::class,
        Commands\WalletMigration::class,
        Commands\OfflineCategoryUpdate::class,
        Commands\ResetLimit::class,
        Commands\Migration::class,
        Commands\EmailQueue::class,
        Commands\DailyOrderUpdate::class,
        Commands\BackupStoreAcceptPayment::class,
        Commands\RestoreStoreAcceptPayment::class,
        Commands\OfflineOrderWalletMigration::class,
        Commands\SpecialWalletMigration::class,
        Commands\TaxInvNoOfflineOrder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//         $schedule->command('favechic:import 1')->weekly()->saturdays()->at('17:00');
//         $schedule->command('favechic:import 61')->weekly()->sundays()->at('17:00');
//         $schedule->command('favechic:import 121')->weekly()->mondays()->at('17:00');
//         $schedule->command('favechic:import 181')->weekly()->tuesdays()->at('17:00');
//         $schedule->command('favechic:import 241')->weekly()->wednesdays()->at('17:00');
//         $schedule->command('favechic:import 301')->weekly()->thursdays()->at('17:00');
//         $schedule->command('favechic:import 361')->weekly()->fridays()->at('17:00');

        // send checkout email to merchant
        $schedule->call(function() {
            \App::call('App\Http\Controllers\Cron\EmailController@orderMailMerchant');
        })->everyFiveMinutes();
        // send checkout email to customer
        $schedule->call(function() {
            \App::call('App\Http\Controllers\Cron\EmailController@orderMailCustomer');
        })->everyFiveMinutes();

         // Order Process
        $schedule->call(function() {
            \App::call('App\Http\Controllers\Cron\ProcessController@online_orders_daily');
        })->dailyAt('16:30');

        $schedule->call(function() {
            \DB::select("CALL company_daily_report()");
        })->dailyAt('17:00');

        $schedule->call(function() {
            \App::call('App\Http\Controllers\Cron\ProcessController@serial_number_orders_daily');
        })->dailyAt('16:35');

		$schedule->call(function() {
            \DB::select("CALL sp_report_daily_sale_by_date()");
        })->dailyAt('17:00');

		$schedule->call(function() {
            \DB::select("CALL sp_report_month_sale_by_date()");
        })->monthlyOn(1, '17:00');

		$schedule->call(function() {
            \DB::select("CALL sp_report_daily_credit()");
        })->dailyAt('17:00');

		$schedule->call(function() {
            \DB::select("CALL sp_report_monthly_credit()");
        })->monthlyOn(1, '17:00');

        // Reset Limit
        $schedule->command('resetLimit daily')->dailyAt('00:00');

        $schedule->command('resetLimit weekly')->weekly()->sundays()->at('00:00');

        $schedule->command('resetLimit monthly')->monthlyOn(1, '00:00');

        $schedule->command('resetLimit yearly')->yearly();

        //email queue
        $schedule->command('emailQueue:send')->everyFiveMinutes();

        // backup & restore accept payment
        $schedule->command('acceptpayment:backup')->dailyAt('15:59')->when(function () {
            $now = \Carbon\Carbon::now()->startOfDay();
            $backup = \Carbon\Carbon::parse('2017-12-31');
            if ($now->eq($backup)) {
                return true;
            }
        });

        $schedule->command('acceptpayment:restore')->dailyAt('15:59')->when(function () {
            $now = \Carbon\Carbon::now()->startOfDay();
            $restore = \Carbon\Carbon::parse('2018-01-07');
            if ($now->eq($restore)) {
                return true;
            }
        });
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
