<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\EmailQueue;
use App\Models\PaymentUser;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Store;
use App\Models\OrderOffline;
use App\Models\Customer;

class EmailController extends Controller {

    protected $mailer;

    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
    }

    public function orderMailMerchant() {
        $orders = Order::leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'nm_order.order_cus_id')
        ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->leftJoin('nm_shipping', 'nm_shipping.ship_order_id', '=', 'nm_order.order_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_shipping.ship_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_shipping.ship_ci_id')
        ->where('order_type', '=', 1)
        ->where('notify_mer', '=', 0)
        ->get();

        $mailOrders = array();
        foreach ($orders as $key => $od) {
            $mailOrders[$od->mer_id]['details'] = array(
                'name' => $od->mer_fname,
                'email' => $od->email
            );
            $mailOrders[$od->mer_id]['order'][$od->order_id] = $od;
        }

        foreach ($mailOrders as $mer_id => $mo) {
            $orderdets = array();
            $order_ids = array();
            foreach ($mo['order'] as $order_id => $det) {
                $order_ids[] = $order_id;
                $orderdets[] = array(
                    'item' => $det->pro_title_en,
                    'trans_id' => $det->transaction_id,
                    'quantity' => $det->order_qty,
                    'vcoin' => number_format( ($det->order_credit/$det->order_qty) ,4 ),
                    'date' => $det->order_date,
                    'address' => $det->ship_address1.', '.$det->ship_address2.', '.$det->ship_postalcode.', '.$det->ci_name.', '.$det->co_name
                );
            }
            # send email ti merchant
            $this->mailer->send('front.emails.checkout_merchant', ['orders' => $orderdets], function (Message $m) use ($mo) {
                $m->to($mo['details']['email'], $mo['details']['name'])->subject('New Order!');
            });

            // if ($mail) {
            //     echo 'Email send!';
                $update = Order::whereIn('order_id', $order_ids)->update(['notify_mer' => 1]);
            // } else {
            //     echo 'Email not send!';
            // }
        }
    }

    public function orderMailCustomer() {
        $orders = Order::leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'nm_order.order_cus_id')
        ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
        ->leftJoin('nm_shipping', 'nm_shipping.ship_order_id', '=', 'nm_order.order_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_shipping.ship_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_shipping.ship_ci_id')
        ->where('order_type', '=', 1)
        ->where('notify_cus', '=', 0)
        ->where('nm_customer.email_verified', 1)
        ->get();

        $mailOrders = array();
        foreach ($orders as $key => $od) {
            $mailOrders[$od->transaction_id]['details'] = array(
                'name' => $od->cus_name,
                'email' => $od->email
            );
            $mailOrders[$od->transaction_id]['order'][$od->order_id] = $od;
        }

        foreach ($mailOrders as $mer_id => $mo) {
            $orderdets = array();
            $order_ids = array();
            foreach ($mo['order'] as $order_id => $det) {
                $order_ids[] = $order_id;
                $orderdets[] = array(
                    'item' => $det->pro_title_en,
                    'trans_id' => $det->transaction_id,
                    'quantity' => $det->order_qty,
                    'vcoin' => number_format( ($det->order_credit/$det->order_qty) ,4 ),
                    'date' => $det->order_date,
                    'address' => $det->ship_address1.', '.$det->ship_address2.', '.$det->ship_postalcode.', '.$det->ci_name.', '.$det->co_name
                );
            }
            # send email ti merchant
            $this->mailer->send('front.emails.checkout_customer', ['orders' => $orderdets], function (Message $m) use ($mo) {
                $m->to($mo['details']['email'], $mo['details']['name'])->subject('Order Success!');
            });

            // if ($mail) {
            //     echo 'Email send!';
                $update = Order::whereIn('order_id', $order_ids)->update(['notify_cus' => 1]);
            // } else {
            //     echo 'Email not send!';
            // }
        }
    }

    public function emailQueue()
    {
        $emails = EmailQueue::where('send', 0)->get();
        $success = collect([]);

        foreach ($emails as $email) {

            switch ($email->jobs) {
                case 'LimitAlert':
                    $result = $this->limitAlertEmailProcess($email);
                    if($result)
                        $success->push($email->id);
                    break;

                default:
                    $this->emailError('Invalid email jobs', $email);
                    break;
            }
        }

        EmailQueue::whereIn('id', $success->toArray())->delete();
    }

    protected function limitAlertEmailProcess($email) {

        $subject = $this->getEmailSubject($email->type);
        switch ($email->notifiable_type) {
            case 'Customer':
                $customer = Customer::find($email->notifiable_id);
                if(!$customer || !$customer->limit || $customer->limit->alerts->isEmpty())
                    $this->emailError('Either customer, limit or alerts not found', $email);

                $response = json_decode($email->data);
                $data = [
                    'user_type' => 'customer',
                    'customer' => $customer,
                    'currency' => $customer->country? $customer->country->co_curcode : null,
                    'detail' => [
                        'limit_type' => strtolower($response->limit_type),
                        'block_type' => strtolower($response->block_type), // amount | number
                        'current' => $response->current,
                        'alert_when' => $response->alert_when
                    ],
                ];

                $this->mailer->send('front.emails.limit.email', $data, function (Message $m) use ($customer, $subject) {
                    $m->to($customer->email, $customer->cus_name)
                    ->cc('operation@meihome.asia', 'MeiHome Operation')
                    ->subject($subject);
                });

                return true;
                break;

            case 'Store':
                $store = Store::find($email->notifiable_id);
                if(!$store || !$store->limit || $store->limit->alerts->isEmpty())
                    $this->emailError('Either store, limit or alerts not found', $email);

                $merchant = $store->merchant;
                if(!$merchant)
                    $this->emailError('Store merchant not found', $email);

                $response = json_decode($email->data);
                $data = [
                    'user_type' => 'merchant',
                    'merchant' => $merchant,
                    'store' => $store,
                    'currency' => $store->country? $store->country->co_curcode : null,
                    'detail' => [
                        'limit_type' => strtolower($response->limit_type),
                        'block_type' => strtolower($response->block_type), // amount | number
                        'current' => $response->current,
                        'alert_when' => $response->alert_when
                    ],
                ];

                $this->mailer->send('front.emails.limit.email', $data, function (Message $m) use ($merchant, $subject) {
                    $m->to($merchant->email, $merchant->merchantName())
                    ->cc('operation@meihome.asia', 'MeiHome Operation')
                    ->subject($subject);
                });

                return true;
                break;

            default:
                $this->emailError('Invalid email notifiable type', $email);
                break;
        }

        return false;
    }

    protected function getEmailSubject($type)
    {
        switch ($type) {
            case 'SingleLimitNotify':
                return 'Single Transaction Limit Alert Notification';
                break;

            case 'DailyLimitNotify':
            case 'DailyLimitNumberNotify':
                return 'Daily Transaction Limit Alert Notification';
                break;

            case 'WeeklyLimitNotify':
            case 'WeeklyLimitNumberNotify':
                return 'Weekly Transaction Limit Alert Notification';
                break;

            case 'MonthlyLimitNotify':
            case 'MonthlyLimitNumberNotify':
                return 'Monthly Transaction Limit Alert Notification';
                break;

            case 'YearlyLimitNotify':
            case 'YearlyLimitNumberNotify':
                return 'Yearly Transaction Limit Alert Notification';
                break;

            default:
                return '';
                break;
        }
    }

    protected function emailError($remarks, $email)
    {
        $email->send = 2;
        $email->remarks = $remarks;
        $email->save();
    }
}