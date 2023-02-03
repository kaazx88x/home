<?php

namespace App\Repositories;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderOffline;
use Carbon\Carbon;


class InvoiceRepo
{
    public static function get_invoices($merchant_id, $input)
    {
        $invoices = Invoice::where('merchant_id', $merchant_id)->orderBy('created_at', 'DESC');

        if (!empty($input['type'])) {
            if ($input['type'] != 'all') {
                $invoices->where('order_type', $input['type']);
            }
        }

        if (!empty($input['start']) && !empty($input['end'])) {
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $invoices->where('created_at', '>=', \Helper::TZtoUTC($input['start']));
            $invoices->where('created_at', '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['invoice_no'])) {
            $invoices->where('invoice_no', 'LIKE', '%'.$input['invoice_no'].'%');
        }

        return $invoices->paginate(25);
    }

    public static function get_invoice_items($merchant_id, $input)
    {
        $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
        $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

        if ($input['type'] == 1) {
            $gst = round(\Config::get('settings.service_charge'));

            $items = Order::selectRaw('
                nm_order.merchant_charge_vtoken as merchant_charge_credit,
                nm_order.order_date as order_date,
                nm_order.merchant_charge_percentage,
                (nm_order.currency_rate * nm_order.merchant_charge_vtoken) as merchant_charge_amount,
                (nm_order.merchant_charge_vtoken * (?/100)) as gst_credit,
                ((nm_order.currency_rate * nm_order.merchant_charge_vtoken) * (?/100)) as gst_amount,
                nm_order.transaction_id as invoice_no
            ', [$gst, $gst])
            ->leftjoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
            ->where('nm_product.pro_mr_id', $merchant_id)
            ->where('nm_order.order_status', 4)
            ->where('nm_order.order_date', '>=', \Helper::TZtoUTC($input['start']))
            ->where('nm_order.order_date', '<=', \Helper::TZtoUTC($input['end']))
            ->get();
        }
        else {
            $gst = round(\Config::get('settings.service_charge'));

            $items = OrderOffline::selectRaw('
                merchant_charge_token as merchant_charge_credit,
                created_at as order_date,
                merchant_charge_percentage,
                (currency_rate * merchant_charge_token) as merchant_charge_amount,
                (merchant_charge_token * (?/100)) as gst_credit,
                ((currency_rate * merchant_charge_token) * (?/100)) as gst_amount,
                inv_no as invoice_no
            ', [$gst, $gst])
            ->where('mer_id', $merchant_id)
            ->where('status', 1)
            ->where('created_at', '>=', \Helper::TZtoUTC($input['start']))
            ->where('created_at', '<=', \Helper::TZtoUTC($input['end']))
            ->get();
        }

        return $items;
    }

    public static function create_invoice_items($merchant_id, $input, $invoices)
    {
        $count = Invoice::whereDate('created_at', \DB::raw('CURDATE()'))->get()->count();

        try {
            $create_invoice = Invoice::create([
                'merchant_id' => $merchant_id,
                'order_type' => $input['type'],
                'start_date' => Carbon::createFromFormat('d/m/Y', $input['start'])->toDateString(),
                'end_date' => Carbon::createFromFormat('d/m/Y', $input['end'])->toDateString(),
                'invoice_no' => date('Ymd') . '-' . ($count+=1),
            ]);

            foreach ($invoices as $key => $inv) {
                InvoiceItem::create([
                    'invoice_id' => $create_invoice->id,
                    'order_date' => $inv->order_date,
                    'order_invoice_no' => $inv->invoice_no,
                    'merchant_charge_credit' => $inv->merchant_charge_credit,
                    'merchant_charge_amount' => $inv->merchant_charge_amount,
                    'merchant_charge_percentage' => $inv->merchant_charge_percentage,
                    'gst_credit' => $inv->gst_credit,
                    'gst_amount' => $inv->gst_amount,
                ]);
            }

            return $create_invoice;
        }
        catch (Exception $e) {
            return false;
        }


        return false;
    }

    public static function get_invoice_items_details($merchant_id, $invoice_id)
    {
        $invoices = Invoice::selectRaw('
            invoices.*,
            concat_ws(" ", nm_merchant.mer_fname, nm_merchant.mer_lname) as merchant_name,
            nm_merchant.mer_id,
            nm_merchant.mer_address1,
            nm_merchant.mer_address2,
            nm_merchant.zipcode,
            nm_merchant.mer_city_name as mer_city,
            nm_state.name as mer_state,
            nm_country.co_name as mer_country,
            nm_merchant.bank_gst,
            nm_merchant.mer_office_number
        ')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'invoices.merchant_id')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_merchant.mer_state')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id')
        ->where('invoices.id', $invoice_id)
        ->where('invoices.merchant_id', $merchant_id)
        ->with(['items'])
        ->first();

        return $invoices;
    }
}