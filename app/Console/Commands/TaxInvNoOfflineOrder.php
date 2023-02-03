<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\OrderOffline;

class TaxInvNoOfflineOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tax-inv-order-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Tax Invoice Offline Number';

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
        //
         try {
            $this->info('starting update now...  ' . date('d M Y h:i a'));
            $imglist = $this->migrate();
            $this->info('update complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function migrate()
    {

        $orders = OrderOffline::whereIn('status',[1,4])->get();

        $year = 0;
        $month = 0;
        $order_no = 0;

        foreach ($orders as $key => $order){
            if($month != $order->created_at->format('m') || $year != $order->created_at->format('y')){
                $order_no = 1;
                $year = $order->created_at->format('y');
                $month = $order->created_at->format('m');
            }

            $no_inv = $year.$month.str_pad($order_no, 4, '0', STR_PAD_LEFT);

            $order->tax_inv_no = $no_inv;
            $order->save();

            $order_no++;
        }

    }
}
