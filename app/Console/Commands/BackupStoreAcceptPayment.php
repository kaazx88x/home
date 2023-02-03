<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Store;
use DB;

class BackupStoreAcceptPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acceptpayment:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup accept_payment field in store';

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
            $this->info('starting backup...  ' . date('d M Y h:i a'));
            $imglist = $this->backup();
            $this->info('backup complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function backup()
    {
        DB::table('nm_store')->update([
            'backup_accept_payment' => DB::raw('accept_payment')
        ]);

        DB::table('nm_store')
        ->where('stor_country', '<>', 12)
        ->update([
            'accept_payment' => 0
        ]);
    }
}
