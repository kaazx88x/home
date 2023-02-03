<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\CustomerWallet;

class WalletMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Existing Customer Wallet Migration';

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
            $this->info('migration start...  ' . date('d M Y h:i a'));
            $imglist = $this->Wallet();
            $this->info('migration complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function Wallet()
    {
        $ids = Customer::selectRAW('distinct(nm_customer.cus_id)')->join('customer_wallets', 'customer_wallets.customer_id', '=', 'nm_customer.cus_id')->get()->toArray();
        $customers = Customer::select('cus_id', 'v_token')->whereNotIn('cus_id', $ids)->get();
        $wallets = Wallet::select('id', 'percentage')->get();

        foreach ($customers as $customer) {
            foreach ($wallets as $wallet) {
                $credit = ($customer->v_token > 0) ? ($customer->v_token * ($wallet->percentage/100)) : 0;

                CustomerWallet::updateOrCreate(
                    [
                        'customer_id' => $customer->cus_id,
                        'wallet_id' => $wallet->id
                    ],
                    [
                        'credit' => $credit
                    ]
                );

            }
        }
    }
}
