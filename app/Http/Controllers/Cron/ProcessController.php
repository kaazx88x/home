<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepo;

class ProcessController extends Controller
{

    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function online_orders_daily()
    {
        $today = date('Y-m-d 00:00:00');
        $endtoday = date('Y-m-d 23:59:59');
        //order notification reminder
        $sql = "SELECT mer_fname, mer_lname, email, count(order_id) as total FROM nm_order a  join nm_product b on a.order_pro_id = b.pro_id " .
                "join nm_merchant c on b.pro_mr_id = c.mer_id " .
                "where DATE_ADD(order_date,INTERVAL 3 DAY) >='" . $today . "' and DATE_ADD(order_date,INTERVAL 3 DAY) <= '" . $endtoday . "' and a.order_status = 1 and a.order_type = 1 group by mer_id; ";
        $merchants = \DB::select($sql);

        foreach ($merchants as $merchant)
        {
            //send email to merchant
            $data = array(
                // 'type'=>'gamepoint',
                'firstname' => $merchant->mer_fname,
                'lastname' => $merchant->mer_lname,
                'total' => $merchant->total
            );
            $this->mailer->send('front.emails.merchant_order_reminder', $data, function($message) use ($merchant)
            {

                $message->to($merchant->email, $merchant->mer_fname)->subject('Reminder: You have ' . $merchant->total . ' Order(s) On Pending');
            });
        }

        //Update order status = 1 to 5, refund to vcoin to customer
        // $sql = "SELECT d.cus_id, d.cus_name, d.email as cus_email, c.mer_fname, c.mer_lname, c.email as mer_email, a.order_id, a.transaction_id, b.pro_id, b.pro_title_en, a.order_qty, a.order_vtokens, d.email_verified " .
        //         "FROM nm_order a  " .
        //         "left join nm_product b on a.order_pro_id = b.pro_id  " .
        //         "left join nm_merchant c on b.pro_mr_id = c.mer_id  " .
        //         "left join nm_customer d on a.order_cus_id = d.cus_id  " .
        //         "where DATE_ADD(order_date,INTERVAL 4 DAY) >= '" . $today . "'  " .
        //         "and DATE_ADD(order_date,INTERVAL 4 DAY) <= '" . $endtoday . "'  " .
        //         "and a.order_status = 1 and a.order_type = 1;";
        // $orders = \DB::select($sql);

        // foreach ($orders as $order)
        // {
        //     \DB::statement("UPDATE nm_order SET order_status=5 WHERE order_id=" . $order->order_id);
        //     \DB::statement("UPDATE nm_customer SET v_token=v_token + " . $order->order_vtokens . " WHERE cus_id=" . $order->cus_id);
        //     \DB::statement("INSERT INTO v_token_log ( `cus_id`,`credit_amount`,`debit_amount`,`order_id`,`remark`,`created_at`,`updated_at`) "
        //             . "VALUES (" . $order->cus_id . ", " . $order->order_vtokens . ",0," . $order->order_id . ",'Refund due to Order Cancellation', now(), now())");
        //     \DB::statement("UPDATE nm_product SET pro_no_of_purchase =pro_no_of_purchase - " . $order->order_qty . " WHERE pro_id=" . $order->pro_id . " AND pro_no_of_purchase>0");

        //     $data = array(
        //         // 'type'=>'gamepoint',
        //         'merchant_firstname' => $order->mer_fname,
        //         'merchant_lastname' => $order->mer_lname,
        //         'customer_name' => $order->cus_name,
        //         'product_title' => $order->pro_title_en,
        //         'order_qty' => $order->order_qty,
        //         'order_vtokens' => $order->order_vtokens,
        //         'transaction_id' => $order->transaction_id
        //     );

        //     $this->mailer->send('front.emails.merchant_order_cancelled', $data, function($message) use ($order)
        //     {

        //         $message->to($order->mer_email, $order->mer_fname)->subject('Order is Cancelled!');
        //     });

        //     if ($order->email_verified) {
        //         $this->mailer->send('front.emails.customer_order_cancelled', $data, function($message) use ($order)
        //         {

        //             $message->to($order->cus_email, $order->cus_name)->subject('Your Order is Cancelled');
        //         });
        //     }
        // }

        //update the order status from 3 to 4, credit vcoin to merchant account. send email to merchant that order is completed
        $sql = "SELECT d.cus_id, d.cus_name, d.email as cus_email, c.mer_id, c.mer_fname, c.mer_lname, c.email as mer_email, a.order_id, a.transaction_id, b.pro_id, b.pro_title_en, a.order_qty, a.order_vtokens, a.merchant_charge_percentage, a.merchant_charge_vtoken " .
                "FROM nm_order a  " .
                "left join nm_product b on a.order_pro_id = b.pro_id  " .
                "left join nm_merchant c on b.pro_mr_id = c.mer_id  " .
                "left join nm_customer d on a.order_cus_id = d.cus_id  " .
                // "where DATE_ADD(order_shipment_date,INTERVAL 7 DAY)>='" . $today . "'  " .
                "where DATE_ADD(order_shipment_date,INTERVAL 7 DAY) <= '" . $endtoday . "'  " .
                "and a.order_status = 3 and a.order_type = 1 and a.product_shipping_fees_type <> 3;";
        $orders = \DB::select($sql);

        foreach ($orders as $order)
        {
//            \DB::statement("UPDATE nm_order SET order_status=4 WHERE order_id=".$order->order_id);
//            \DB::statement("UPDATE nm_merchant SET mer_vtoken=mer_vtoken + ".$order->order_vtokens. " WHERE mer_id=".$order->mer_id);
//            \DB::statement("INSERT INTO merchant_vtoken_log ( `mer_id`,`credit_amount`,`debit_amount`,`order_id`,`remark`,`created_at`,`updated_at`) "
//                    . "VALUES (".$order->mer_id.", ".$order->order_vtokens.",0,".$order->order_id.", 'Order delivered' ,now(), now())");

            $result = OrderRepo::completing_merchant_order($order->order_id);

            if ($result)
            {
                $data = array(
                    // 'type'=>'gamepoint',
                    'merchant_firstname' => $order->mer_fname,
                    'merchant_lastname' => $order->mer_lname,
                    'product_title' => $order->pro_title_en,
                    'order_qty' => $order->order_qty,
                    'order_vtokens' => $order->order_vtokens,
                    'transaction_id' => $order->transaction_id,
                    'order_commission' => $order->merchant_charge_percentage,
                    'merchant_earn_vtokens' => $order->order_vtokens-$order->merchant_charge_vtoken
                );

                $this->mailer->send('front.emails.merchant_order_completed', $data, function($message) use ($order)
                {

                    $message->to($order->mer_email, $order->mer_fname)->subject('Order is Completed!');
                });
            }
        }
    }

    public function serial_number_orders_daily()
    {
        //update the order status from 3 to 4, credit vcoin to merchant account. send email to merchant that order is completed
        $sql = "SELECT d.cus_id, d.cus_name, d.email as cus_email, c.mer_id, c.mer_fname, c.mer_lname, c.email as mer_email, a.order_id, a.transaction_id, b.pro_id, b.pro_title_en, a.order_qty, a.order_vtokens, a.merchant_charge_percentage, a.merchant_charge_vtoken " .
                "FROM nm_order a  " .
                "left join nm_product b on a.order_pro_id = b.pro_id  " .
                "left join nm_merchant c on b.pro_mr_id = c.mer_id  " .
                "left join nm_customer d on a.order_cus_id = d.cus_id  " .
                "where a.order_status = 2 and a.order_type in (3, 4, 5);";
        $orders = \DB::select($sql);

        foreach ($orders as $order)
        {
            $result = OrderRepo::completing_merchant_order($order->order_id);

            if ($result)
            {
                $data = array(
                    // 'type'=>'gamepoint',
                    'merchant_firstname' => $order->mer_fname,
                    'merchant_lastname' => $order->mer_lname,
                    'product_title' => $order->pro_title_en,
                    'order_qty' => $order->order_qty,
                    'order_vtokens' => $order->order_vtokens,
                    'transaction_id' => $order->transaction_id,
                    'order_commission' => $order->merchant_charge_percentage,
                    'merchant_earn_vtokens' => $order->order_vtokens-$order->merchant_charge_vtoken
                );

                $this->mailer->send('front.emails.merchant_order_completed', $data, function($message) use ($order)
                {

                    $message->to($order->mer_email, $order->mer_fname)->subject('Order is Completed!');
                });
            }
        }
    }

}
