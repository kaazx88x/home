<?php

namespace App\Repositories;
use App\Models\ProductPricing;
use App\Models\Cart;
use Carbon\Carbon;
use App\Models\ProductQuantityLog;
use App\Models\Product;
use App\Models\PricingAttributeMapping;
use App\Models\ProductAttribute;
use App\Models\Order;
use App\Repositories\AttributeRepo;
use App\Models\GeneratedCode;

class ProductPricingRepo
{
    public static function get_product_pricing_by_pro_id($pro_id)
    {
        $pricing = ProductPricing::select('nm_product_pricing.*','nm_country.co_id','nm_country.co_id','nm_country.co_code','nm_country.co_name','nm_country.co_curcode','nm_country.co_status','nm_country.co_rate','nm_country.co_cursymbol')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_product_pricing.country_id')
        ->where('pro_id', $pro_id)->orderBy('nm_product_pricing.country_id','asc')->orderBy('nm_product_pricing.created_at','desc')->get();

        return $pricing;
    }

    public static function get_product_pricing_by_id($id)
    {
        return ProductPricing::select('nm_product_pricing.*', 'nm_country.*', 'nm_product.pro_type')
        ->leftJoin('nm_product', 'nm_product.pro_id', 'nm_product_pricing.pro_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_product_pricing.country_id')
        ->where('nm_product_pricing.id',$id)
        ->first();
    }

    public static function add_product_pricing($pro_id, $data)
    {
        $product = Product::find($pro_id);

        $from = null;
        $to = null;
        if(!empty($data['start']) && !empty($data['end']))
        {
            $countrycode = strtoupper($data['co_code']);
            $timezone = current(\DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countrycode));

            $from = date('Y-m-d H:i:s', strtotime($data['start']));
            $to = date('Y-m-d H:i:s', strtotime($data['end']));

            $parseFrom =  Carbon::createFromFormat('Y-m-d H:i:s',$from, $timezone);
            $parseFrom->setTimezone('UTC');
            $from = $parseFrom;

            $parseTo =  Carbon::createFromFormat('Y-m-d H:i:s',$to, $timezone);
            $parseTo->addSeconds(59);
            $parseTo->setTimezone('UTC');
            $to = $parseTo;

        }

        $price = ProductPricing::create([
            'pro_id' => $pro_id,
            'country_id' => $data['country_id'],
            'quantity' => isset($data['quantity'])? $data['quantity'] : 0,
            'currency_rate' => $data['currency_rate'],
            'price' => $data['pro_price'],
            'shipping_fees' => ($data['shipping_fees_type'] == 0 || $data['shipping_fees_type'] == 3) ? 0 : $data['shipping_fees'],
            'shipping_fees_type' => $data['shipping_fees_type'],
            'discounted_price' => $data['pro_dprice'],
            'discounted_rate' => (!empty($data['discounted_rate'])? $data['discounted_rate'] : null),
            'discounted_from' => $from,
            'discounted_to' => $to,
            'delivery_days' => $data['pro_delivery'],
            'coupon_value' => isset($data['coupon_value'])? $data['coupon_value'] : null,
            'status' => ($product->pro_type == 4)? 0 : 1,
        ]);

        return $price;
    }

    public static function update_pricing($id, $entry)
    {
        $pricing = ProductPricing::find($id);
        $pricing->update($entry);

        return $pricing;
    }

    public static function get_exsisting_country_id($id)
    {
        $pricing = ProductPricing::where('pro_id',$id)->get();
        $country_id = collect($pricing)->keyBy('country_id')->pluck('country_id');
        return $country_id;
    }

    public static function update_pricing_status($id)
    {
        $pricing = ProductPricing::find($id);
        $pricing->update([
            'status' => ($pricing->status == 0)? 1 : 0,
        ]);

        //remove item in cart
        if($pricing->status == 0)
            Cart::where('pricing_id','=',$pricing->id)->delete();

        self::update_product_quantity($pricing->pro_id);

        return $pricing;
    }

    public static function update_pricing_status_batch($pro_id, $pricing_id, $status)
    {
        $pricing_id = array_values(array_filter($pricing_id));
        ProductPricing::whereIn('id', $pricing_id)->where('attribute_status', 1)->update([
            'status' => $status,
        ]);

        if($status == 0)
            Cart::whereIn('pricing_id', $pricing_id)->delete();

        self::update_product_quantity($pro_id);

        return true;
    }

    public static function delete_product_pricing($id)
    {
        ProductPricing::find($id)->delete();
    }

    public static function update_related_pricing_quantity($pro_id, $price_id = null, $operation, $update_quantity = null)
    {
        $old_quantity = 0;

        $product = Product::find($pro_id);
        if($product->pro_type == 4) {
            $pricing_details = self::product_first_price($pro_id);
            $quantity = $pricing_details->quantity;
            if($operation == 'new_item')
                $quantity = GeneratedCode::where('product_id', $product->pro_id)->where('merchant_id', $product->pro_mr_id)->where('type', 4)->where('status', 0)->count();
            else
                $update_quantity += $quantity;
        } else {
            $pricing_details = ProductPricing::find($price_id);
            $quantity = $pricing_details->quantity;
        }

        $remarks = '';
        if ($operation == 'exist_item' && (!empty($update_quantity) || $update_quantity == 0)) {
            $old_quantity = $quantity;
            $remarks = 'Update product quantity. From: ' . $quantity . ' To: ' . $update_quantity;
            $quantity = $update_quantity;
        } elseif ($operation == 'new_item') {
            $remarks = 'Initialize product quantity';
            $other_pricing = ProductPricing::select('quantity')->where('pro_id', $pro_id)->where('id', '!=', $price_id)->where('attributes','=', $pricing_details->attributes)->first();
            if($other_pricing) {
                $old_quantity = $other_pricing->quantity;
                $remarks = 'Update product quantity. From: ' . $old_quantity . ' To: ' . $quantity;
            }
        }

        // Update all pricing with same attribute set
        $update_related_pricing = ProductPricing::where('pro_id', $pro_id)
        ->where('attributes', $pricing_details->attributes)
        ->where('quantity', '<>', $quantity)
        ->update([
            'quantity' => $quantity
        ]);

        // Update product total quantity
        $total = \DB::select("select sum(quantity) as quantity from (select quantity from nm_product_pricing where pro_id = " . $pro_id . " and status = 1 group by attributes) as a");
        $total_product = Product::where('pro_id', $pro_id)->update([
            'pro_qty' => $total[0]->quantity
        ]);

        // log
        $log_data = [
            'pro_id' => $pro_id,
            'attributes' => $pricing_details->attributes_name,
            'remarks' => $remarks,
            'current_quantity'=> $quantity,
        ];

        if ($old_quantity > $quantity) {
            $log_data['debit'] = $old_quantity - $quantity;
        } else {
            $log_data['credit'] = $quantity - $old_quantity;
        }

        $log = ProductQuantityLog::create($log_data);
    }

    public static function update_product_quantity($pro_id)
    {
        $related_price = ProductPricing::select('*','attributes as attributes_id')->where('pro_id', $pro_id)->where('status', 1)->get()->groupBy('attributes_id');
        $total_qty = 0;
        foreach ($related_price as $related) {
            $total_qty += $related[0]->quantity;
        }

        return Product::where('pro_id', $pro_id)->update([
            'pro_qty' => $total_qty,
        ]);
    }

    public static function refund_pricing_quantity($order_id, $operation, $order_status)
    {
        try {
            $order = Order::find($order_id);
            $product = Product::find($order->order_pro_id);
            // $product->pro_no_of_purchase = $product->pro_no_of_purchase - $order->order_qty; // disabled; already deduct on SP
            $product->pro_qty = $product->pro_qty + $order->order_qty;
            $product->save();

            $price_id = $order->order_pricing_id;
            if($price_id > 0) {
                $price = ProductPricing::find($price_id);
                $current_quantity = $price->quantity + $order->order_qty;

                // $response = [
                //     'notify' => 0,
                //     'mer_id' => $product->pro_mr_id,
                //     'product' => $product,
                // ];

                switch ($operation) {
                    case 'refund':
                        $remarks = 'Customer Refund, Transaction ID : '.$order->transaction_id;
                        break;

                    case 'cancel':
                        $remarks = 'Order Canceled, Transaction ID : '.$order->transaction_id;
                        break;
                }

                ProductQuantityLog::create([
                    'pro_id' => $product->pro_id,
                    'attributes' => $order->order_attributes,
                    'credit' => $order->order_qty,
                    'remarks' => $remarks,
                    'current_quantity'=> $current_quantity,
                ]);

                ProductPricing::where('pro_id', $product->pro_id)->where('attributes','=', $price->attributes)->update([
                    'quantity' => $current_quantity,
                ]);
            }

            return true;

        } catch(Exception $e) {
            return false;
        }
    }

    public static function check_set_pricing_exist($pro_id, $country_id)
    {
        $check = ProductPricing::where('pro_id',$pro_id)
        ->where('country_id', $country_id)
        ->first();

        if($check)
            return false;

        return true;
    }

    public static function product_first_price($pro_id)
    {
        return ProductPricing::where('pro_id', $pro_id)->orderBy('id', 'asc')->first();
    }
}
