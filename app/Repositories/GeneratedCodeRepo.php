<?php

namespace App\Repositories;
use DB;
use App\Models\GeneratedCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPricing;
use Carbon\Carbon;

class GeneratedCodeRepo
{
    public static function find_coupon($coupon_code)
    {
        return GeneratedCode::where('serial_number', $coupon_code)->where('type', 2)->first();
    }

    public static function find_ticket($ticket_number)
    {
        return GeneratedCode::where('serial_number', $ticket_number)->where('type', 3)->first();
    }

    public static function find_ecard($ecard_number, $pro_id, $mer_id)
    {
        return GeneratedCode::where('serial_number', $ecard_number)->where('product_id', $pro_id)->where('merchant_id', $mer_id)->where('type', 4)->first();
    }

    public static function redeem_code($type, $serial_number, $method = null, $pro_id = null, $mer_id = null)
    {
        try {
            switch ($type) {
                case 'coupon':
                    $code = self::find_coupon($serial_number);
                    break;

                case 'ticket':
                    $code = self::find_ticket($serial_number);
                    break;

                case 'ecard':
                    if(!$mer_id || !$pro_id)
                        return false;

                    $code = self::find_ecard($serial_number, $pro_id, $mer_id);
                    break;

                default:
                    return false;
                    break;
            }

            if(!$code)
                return false;

            $order = Order::find($code->order_id);
            if(!$order || in_array($order->order_status, [1,5,6]))
                return false;

            $code->status = 2;
            $code->redeem_method = $method? $method : 0;
            $code->redeemed_at = Carbon::now('UTC')->toDateTimeString();
            $code->save();

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public static function cancel_code($type, $serial_number)
    {
        try {
            switch ($type) {
                case 'coupon':
                    $code = self::find_coupon($serial_number);
                    break;

                case 'ticket':
                    $code = self::find_ticket($serial_number);
                    break;

                default:
                    return false;
                    break;
            }

            if(!$code)
                return false;

            $code->status = 3;
            $code->save();

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public static function cancel_code_by_order($order_id)
    {
        try {

            return GeneratedCode::where('status', 1)
            ->where('order_id', $order_id)->update([
                'status' => 3
            ]);

        } catch (Exception $e) {
            return false;
        }
    }

    //api merchant eventcontroller
    public static function get_event_listing($mer_id, $input)
    {
        try {
            $events = Product::selectRaw('pro_id as id, pro_title_en as event_name, (SELECT CONCAT(?,?,?,?,nm_product.pro_mr_id,?,image) FROM nm_product_image WHERE pro_id = nm_product.pro_id ORDER BY nm_product_image.order ASC, nm_product_image.main ASC LIMIT 1) as image, start_date, end_date, (pro_qty + pro_no_of_purchase) as total_seat, pro_no_of_purchase as purchased', [env('IMAGE_DIR'),'/','product','/','/'])
            ->where('pro_type', 3)
            ->where('pro_status', 1)
            ->where('pro_mr_id', $mer_id);

            if(!empty($input['event_name'])) {
                $events->whereRaw('pro_title_en LIKE ?', ['%'.$input['event_name'].'%']);
            }

            if(!empty($input['sort'])) {
                switch ($input['sort']) {
                    case 'sdate_asc':
                        $events->orderBy('start_date');
                        break;

                    case 'sdate_desc':
                        $events->orderBy('start_date', 'DESC');
                        break;

                    case 'edate_asc':
                        $events->orderBy('end_date');
                        break;

                    case 'edate_desc':
                        $events->orderBy('end_date', 'DESC');
                        break;

                    case 'purchased_asc':
                        $events->orderBy('pro_no_of_purchase');
                        break;

                    case 'purchased_desc':
                        $events->orderBy('pro_no_of_purchase', 'DESC');
                        break;

                }
            } else {
                $events->orderBy('end_date', 'ASC');
            }

            $events = $events->paginate($input['size']);

            return $events;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function get_ticket_listing_($merchant_id = false, $customer_id = false, $product_id = false, $input)
    {
        $tickets = GeneratedCode::selectRaw("generated_codes.serial_number, nm_order.order_attributes, nm_customer.cus_name as customer_name,
            CASE (generated_codes.status) WHEN 1 THEN
                CASE WHEN (generated_codes.valid_to IS NOT NULL) and (CAST(generated_codes.valid_to AS DATETIME) < NOW()) THEN
                'Expired'
                ELSE
                'Open'
                END
                WHEN 2 THEN 'Claimed'
                WHEN 3 THEN 'Cancelled'
            END AS status, nm_product.pro_id as id, nm_product.pro_title_en as event_name, (SELECT CONCAT(?,?,?,?,nm_product.pro_mr_id,?,image) FROM nm_product_image WHERE pro_id = nm_product.pro_id ORDER BY nm_product_image.order ASC, nm_product_image.main ASC LIMIT 1) as image, nm_product.start_date, nm_product.end_date", [env('IMAGE_DIR'),'/','product','/','/']
        )
        ->leftJoin('nm_order', 'nm_order.order_id', '=', 'generated_codes.order_id')
        ->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'generated_codes.customer_id')
        ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
        ->where('generated_codes.type', 3);

        if($merchant_id) {
            $tickets->where('generated_codes.merchant_id', $merchant_id);
        }

        if($product_id) {
            $tickets->where('nm_order.order_pro_id', $product_id);
        }

        if($customer_id) {
            $tickets->where('nm_order.order_cus_id', $customer_id);
        }

        if(!empty($input['customer_name'])) {
            $tickets->whereRaw('nm_customer.cus_name LIKE ?', ['%'.$input['customer_name'].'%']);
        }

        if(!empty($input['ticket_number'])) {
            $tickets->whereRaw('generated_codes.serial_number = ?', [$input['ticket_number']]);
        }

        switch ($input['status']) {
            case 'open':
                $tickets->where('generated_codes.status', 1)
                ->whereRaw("CASE WHEN (generated_codes.valid_to IS NULL) THEN 1 WHEN (CAST(generated_codes.valid_to AS DATETIME) > NOW()) THEN 1 ELSE 0 END", 1);
                break;

            case 'claimed':
                $tickets->where('generated_codes.status', 2);
                break;

            case 'cancelled':
                $tickets->where('generated_codes.status', 3);
                break;

            case 'expired':
                $tickets->where('generated_codes.status', 1)
                ->whereRaw("CASE WHEN (generated_codes.valid_to IS NULL) THEN 0 WHEN (CAST(generated_codes.valid_to AS DATETIME) < NOW()) THEN 1 ELSE 0 END", 1);
                break;
        }

        if(!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'sdate_asc':
                    $tickets->orderBy('nm_product.start_date');
                    break;

                case 'sdate_desc':
                    $tickets->orderBy('nm_product.start_date', 'DESC');
                    break;

                case 'edate_asc':
                    $tickets->orderBy('nm_product.end_date');
                    break;

                case 'edate_desc':
                    $tickets->orderBy('nm_product.end_date', 'DESC');
                    break;

                default:
                    $tickets->orderBy('nm_product.end_date', 'ASC');
                    break;
            }
        } else {
            $tickets->orderBy('nm_product.end_date', 'ASC');
        }

        if(isset($input['size']))
            return $tickets->paginate($input['size']);

        return $tickets->get();
    }

    public static function update_ecard_serial_number($serial_number, $product_id, $merchant_id)
    {
        $product = Product::find($product_id);

        foreach ($serial_number as $serial) {
            GeneratedCode::firstOrCreate([
                'merchant_id' => $merchant_id,
                'product_id' => $product_id,
                'serial_number' => $serial,
                'type' => 4
            ],[
                'order_id' => 0,
                'customer_id' => 0,
                'status' => 0,
                'valid_from' => $product->start_date,
                'valid_to' => $product->end_date,
            ]);
        }

        return true;
    }

    public static function get_ecard_listing($merchant_id, $product_id, $input = null)
    {
        $listing = GeneratedCode::where(function ($q) use($merchant_id, $product_id) {
            $q->where('product_id', $product_id)
            ->where('merchant_id', $merchant_id);
        });

        if(isset($input['serial_number']) && strlen($input['serial_number'])) {
            $listing->where(function ($q) use ($input) {
                $q->where('serial_number', 'LIKE', '%'.$input['serial_number'].'%');
            });
        }

        if(isset($input['status']) && strlen($input['status'])) {
            $listing->where('status', $input['status']);
        }

        if(isset($input['sort']) && !empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $listing->orderBy('created_at', 'desc');
                    break;

                case 'old':
                    $listing->orderBy('created_at', 'asc');
                    break;

                case 'serial_asc':
                    $listing->orderBy('serial_number', 'asc');
                    break;

                case 'serial_desc':
                    $listing->orderBy('serial_number', 'desc');
                    break;
            }
        } else {
            $listing->orderBy('created_at', 'desc');
        }

        if(isset($input['show']) && !empty($input['show'])) {
            return $listing->paginate($input['show']);
        }

        return $listing->get();
    }

    public static function update_ecard_validity($merchant_id, $product_id)
    {
        $product = Product::find($product_id);

        return GeneratedCode::where('product_id', $product_id)->where('merchant_id', $merchant_id)->update([
            'valid_from' => $product->start_date,
            'valid_to' => $product->end_date,
        ]);
    }

    public static function find_code($id)
    {
        return GeneratedCode::find($id);
    }

    public static function delete_ecard($id, $pro_id, $mer_id)
    {
        return GeneratedCode::where('id', $id)->where('product_id', $pro_id)->where('merchant_id', $mer_id)->where('type', 4)->delete();
    }
}