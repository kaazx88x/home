<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Limit;

class ResetLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetLimit {option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset limit and count';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info('starting reset now...  ' . date('d M Y h:i a'));

            $option = $this->argument('option');
            switch ($option) {
                case 'daily':
                    Limit::where('daily', '>', 0)->orWhere('daily_count', '>', 0)
                    ->update([
                        'daily' => 0,
                        'daily_count' => 0,
                    ]);
                    break;

                case 'weekly':
                    Limit::where('weekly', '>', 0)->orWhere('weekly_count', '>', 0)
                    ->update([
                        'weekly' => 0,
                        'weekly_count' => 0,
                    ]);
                    break;

                case 'monthly':
                    Limit::where('monthly', '>', 0)->orWhere('monthly_count', '>', 0)
                    ->update([
                        'monthly' => 0,
                        'monthly_count' => 0,
                    ]);
                    break;

                case 'yearly':
                    Limit::where('yearly', '>', 0)->orWhere('yearly_count', '>', 0)
                    ->update([
                        'yearly' => 0,
                        'yearly_count' => 0,
                    ]);
                    break;

                default:
                    $this->info('Invalid option.');
                    break;
            }
            $this->info('reset complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
