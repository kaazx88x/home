<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\CustomerWallet;

class SpecialWalletMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:special_wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $imglist = $this->SpecialWallet();
            $this->info('migration complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function SpecialWallet()
    {
        // check & add special wallet if not exist
        $wallet = Wallet::where('name_en', 'AP')->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'name_en' => 'AP',
                'percentage' => 0
            ]);
        }

        $customers = Customer::all();
        foreach ($customers as $customer) {
            CustomerWallet::updateOrCreate(
                [
                    'customer_id' => $customer->cus_id,
                    'wallet_id' => $wallet->id
                ],
                [
                    'credit' => 0
                ]
            );
        }
    }
}
