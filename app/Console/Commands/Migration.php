<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Limit;
use App\Models\Order;
use App\Models\LimitAction;
use App\Models\MerchantVTokenLog;

class Migration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:migration {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migration for selected module';

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
            $option = $this->argument('module');

            switch ($option) {
                case 'limit':
                    $this->migrate_store_limit();
                    break;

                case 'online_refunded':
                    $this->migrate_order_refunded_status();
                    break;

                default:
                $this->info('Invalid module');
                    break;
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function migrate_store_limit()
    {
        $stores = Store::where('stor_type', '>', 0)
        ->orWhere('single_limit', '>', 0)
        // ->orWhere('daily_limit', '>', 0)
        // ->orWhere('daily_trans', '>', 0)
        ->orWhere('monthly_limit', '>', 0)
        ->orWhere('monthly_trans', '>', 0)
        ->orWhere('accept_payment', 1)
        ->get();

        foreach ($stores as $store) {
            $data = [
                'daily' => 0.00,
                'weekly' => 0.00,
                'monthly' => 0.00,
                'yearly'=> 0.00,
            ];

            // if($store->daily_trans > 0.00) {
            //     $data['daily'] = $store->daily_trans;
            //     $data['weekly'] = $store->daily_trans;
            //     $data['monthly'] = $store->daily_trans;
            //     $data['yearly'] = $store->daily_trans;
            // }

            if($store->monthly_trans > 0.00) {
                $data['monthly'] += $store->monthly_trans;
                $data['yearly'] += $store->monthly_trans;
            }

            $limit = Limit::create($data);
            $store->limit_id = $limit->id;
            $store->save();

            if($store->single_limit > 0.00) {
                LimitAction::create([
                    'limit_id' => $limit->id,
                    'type' => 1,
                    'action' => 2,
                    'amount' => $store->single_limit,
                    'number_transaction' => 0,
                    'order' => '1.2'
                ]);
            }

            // if($store->daily_limit > 0.00) {
            //     LimitAction::create([
            //         'limit_id' => $limit->id,
            //         'type' => 2,
            //         'action' => 2,
            //         'amount' => $store->daily_limit,
            //         'number_transaction' => 0,
            //         'order' => '2.2'
            //     ]);
            // }

            if($store->monthly_limit > 0.00) {
                LimitAction::create([
                    'limit_id' => $limit->id,
                    'type' => 4,
                    'action' => 2,
                    'amount' => $store->monthly_limit,
                    'number_transaction' => 0,
                    'order' => '4.2'
                ]);
            }

            $this->info('Store id : ' . $store->stor_id . ' updated');
        }
    }

    public function migrate_order_refunded_status()
    {
        $this->info('Start migration...' . date('d M Y h:i a'));
        $orders = Order::select('nm_order.order_id', 'nm_order.order_status', 'nm_merchant.mer_id')
        ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->where('nm_order.order_status', 5)
        ->get();

        $update = [];
        foreach ($orders as $order) {
            $refunded = false;
            $check = MerchantVTokenLog::where('mer_id', $order->mer_id)
            ->where('order_id', $order->order_id)
            ->where('debit_amount', '>', 0)
            ->count();

            if($check > 0) {
                $this->info('Order id : '.$order->order_id.' insert into update queue');
                array_push($update, $order->order_id);
            }
        }

        if(!empty($update)) {
            Order::whereIn('order_id', $update)->update(['order_status' => 6]);
            $this->info('Finish update for online order!');
        } else {
            $this->info('Nothing to be updated for online order.');
        }

        $this->info('Migration complete!  ' . date('d M Y h:i a'));
    }
}
