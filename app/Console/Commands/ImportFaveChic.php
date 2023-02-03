<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;

class ImportFaveChic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'favechic:import {start? : Starting page} {end? : Last page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from FaveChic';

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
            $start = $this->argument('start');
            $end = $this->argument('end');

            if (!isset($start) && (null === $this->argument('start')))
                $start = $this->ask('Enter start page');

            if ((null === $this->argument('start')) && (null === $this->argument('end')))                
                $end = $this->ask('Enter end page');

            $this->info('Importing now...  ' . date('d M Y h:i a'));
            $this->info(\App::call('App\Http\Controllers\Api\FaveChic\ImportController@import', ['startPage' => $start, 'endPage' => $end]));
            $this->info('Import complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
