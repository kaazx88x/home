<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;

class EmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emailQueue:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending queue emails';

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
            $this->info('Starting...  ' . date('d M Y h:i a'));
            $this->info(\App::call('App\Http\Controllers\Cron\EmailController@emailQueue'));
            $this->info('Complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
