<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderOfflineRepo;
use App\Repositories\OrderRepo;
use Carbon\Carbon;
use Helper;

class ExportController extends Controller
{
    public function product_orders($operation)
    {
        $input = \Request::only('tid', 'name', 'status', 'sort', 'start','end', 'code', 'action', 'export_as');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        if(!\Auth::guard('merchants')->check()) {
            return back()->with('error', 'You are not authorized.');
        }

        $mer_id = \Auth::guard('merchants')->user()->mer_id;

        switch ($operation) {
            case 'orders':
                $type = 1;
                break;

            // case 'coupons':
            //     $type = 3;
            //     break;

            // case 'tickets':
            //     $type = 4;
            //     break;

            case 'ecards':
                $type = 5;
                break;

            default:
                return back()->with('error', 'Invalid Transaction');
                break;
        }

        $product_orders = OrderRepo::get_orders_by_status($mer_id, $type, $input);
        $charges = OrderRepo::get_online_total_transaction($mer_id, $type, $input);

        if($product_orders->isEmpty())
            return back()->with('error','No data to export');

        $header = ['Order ID', 'Transaction ID', 'Product', 'Option', 'Customer', 'Quantity', 'Amount', 'Order Mei Point Total', 'Merchant Commission', 'Merchant Earning', 'Order Date', 'Status', 'Shipping Phone Number', 'Shipping Address'];

        $contents = [];
        foreach ($product_orders->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $order) {

                switch ($order->order_status) {
                    case '1':
                        $status = 'Processing';
                        break;
                    case '2':
                        $status = 'Packaging';
                        if($order->order_type == '3');
                            $status = 'Pending';
                        break;
                    case '3':
                        $status = 'Shipped';
                        if($order->order_type == '3');
                            $status = 'Redeemed';
                        break;
                    case '4':
                        $status = 'Completed';
                        break;
                    case '5':
                        $status = 'Canceled';
                        break;
                    case '6':
                        $status = 'Refunded';
                        break;
                }

                $attribute_string = "";
                if($order->order_attributes != null)
                {
                    $attributes = (array)json_decode($order->order_attributes);
                    $last = count($attributes);
                    $index = 1;
                    foreach ($attributes as $attribute => $attribute_item) {
                        $attribute_string .= $attribute." : ".$attribute_item;
                        if($index < $last)
                            $attribute_string .= "\n";

                        $index++;
                    }
                }

                $content = [
                    'order_id' => $order->order_id,
                    'transaction_id' => $order->transaction_id,
                    'product' => $order->pro_title_en,
                    'option' => $attribute_string,
                    'customer' => $order->cus_name,
                    'quantity' => $order->order_qty,
                    'amount' => $order->currency.' '.number_format($order->total_product_price, 2),
                    'order_total' => number_format(($order->order_vtokens - $order->cus_service_charge_value - $order->cus_platform_charge_value), 4),
                    'merchant_fee' => number_format($order->merchant_charge_percentage) . '% - ' . round($order->merchant_charge_vtoken, 4),
                    'balance' => number_format(($order->order_vtokens - $order->merchant_charge_vtoken - $order->cus_service_charge_value - $order->cus_platform_charge_value), 4),
                    'transaction_date' => Helper::UTCtoTZ($order->order_date),
                    'status' => $status,
                    'shipping_phone' => $order->ship_phone,
                    'shipping_address' => implode(', ', array_filter([$order->ship_address1, $order->ship_address2, $order->ship_postalcode,$order->ship_city_name, (!empty($order->ship_state_id) ? $order->name : $order->ci_name), $order->co_name]))
                ];

                $contents[$file][] = $content;
            }

            $contents[$file][] = [
                '', '', '', '', '', '', 'Subtotal',
                'order_total' => number_format($chunks->sum('order_vtokens') - $chunks->sum('cus_service_charge_value') - $chunks->sum('cus_platform_charge_value'), 4),
                'merchant_fee' => number_format($chunks->sum('merchant_charge_vtoken'), 4),
                'balance' => number_format($chunks->sum('order_vtokens') - $chunks->sum('merchant_charge_vtoken') - $chunks->sum('cus_service_charge_value') - $chunks->sum('cus_platform_charge_value'), 4),
            ];
            $contents[$file][] = [
                '', '', '', '', '', '', 'Total',
                'order_total' => number_format($charges->total_credit, 4),
                'merchant_fee' => number_format($charges->merchant_charge, 4),
                'balance' => number_format($charges->merchant_earned, 4),
            ];
        }

        $download = Helper::export('Product_Orders', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

    public function order_offline()
    {
        $input = \Request::only('id', 'cid', 'mid', 'search', 'start', 'end', 'type', 'status', 'sort', 'action', 'createdby', 'export_as');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        if(!\Auth::guard('merchants')->check()) {
            return back()->with('error', 'You are not authorized.');
        }

        $mer_id = \Auth::guard('merchants')->user()->mer_id;

        $orders = OrderOfflineRepo::get_orders_offline($mer_id, $input);
        $total = OrderOfflineRepo::get_grand_total($mer_id, $input);

        if($orders->isEmpty())
            return back()->with('error','No data to export');

        $header = ['#ID', 'Invoice No.', 'Customer', 'Merchant' ,'Amount', trans('localize.credit'), trans('localize.merchant_charge'), 'Merchant Earning', 'Paid Date', 'Transaction Date', 'Status'];


        $contents = [];
        foreach ($orders->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $order) {

                switch ($order->status) {
                    case 0:
                        $status = 'Unpaid';
                        break;
                    case 1:
                        $status = 'Paid';
                        break;
                    case 2:
                        $status = 'Cancel By Member';
                        break;
                    case 3:
                        $status = 'Cancel By Merchant';
                        break;
                    case 4:
                        $status = 'Refunded';
                        break;
                }

                $contents[$file][] = [
                    'id' => $order->id,
                    'inv_no' => $order->inv_no,
                    'customer' => (!empty($order->cus_id))?$order->cus_id.'-'.$order->cus_name : 'Customer not found',
                    'merchant' => (!empty($order->mer_id))? $order->mer_id.'-'.$order->mer_fname : 'Merchant not found',
                    'amount' => $order->currency.' '.$order->amount,
                    'order_total' => $order->v_token,
                    'merchant_fee' => round($order->merchant_charge_percentage) . '% - ' . $order->merchant_charge_token,
                    'balance' => number_format(($order->v_token - $order->merchant_charge_token), 4),
                    'paid_date' => Carbon::createFromTimestamp(strtotime($order->paid_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                    'transaction_date' => Carbon::createFromTimestamp(strtotime($order->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                    'status' => $status,
                ];
            }

            $contents[$file][] = [
                '','','','','Subtotal',
                number_format($orders->sum('v_token'), 4),
                number_format($orders->sum('merchant_charge_token'), 4),
                number_format($orders->sum('v_token') - $orders->sum('merchant_charge_token'), 4)
            ];

            $contents[$file][] = [
                '','','','','Total',
                number_format($total->v_credit, 4),
                number_format($total->merchant_charge_token, 4),
                number_format(($total->v_credit - $total->merchant_charge_token), 4)
            ];
        }

        $download = Helper::export('Order_Offline', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }
}
