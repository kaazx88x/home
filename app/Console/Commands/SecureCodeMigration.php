<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Customer;

class SecureCodeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:securecode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate un-hashed and not empty customer payment securecode';

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
            $imglist = $this->migrate();
            $this->info('reset complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function migrate()
    {
        $customers = Customer::whereNotNull('payment_secure_code')->get();
        foreach ($customers as $key => $customer) {
            if(is_numeric($customer->payment_secure_code)) {
                $customer->payment_secure_code = \Hash::make($customer->payment_secure_code);
                $customer->save();
                $this->info($customer->cus_id.' : updated');
            }
        }
    }
}
