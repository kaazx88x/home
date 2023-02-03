<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepo;
use App\Repositories\CourierRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\StoreRepo;
use App\Repositories\CityRepo;
use App\Repositories\OrderOfflineRepo;
use App\Repositories\VcoinLogRepo;
use App\Models\Merchant;
use App\Models\StoreUser;
use App\Repositories\MerchantRepo;
use App\Repositories\StateRepo;
use App\Repositories\ProductRepo;
use App\Repositories\ProductImageRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\CategoryRepo;
use App\Repositories\ProductPricingRepo;
use App\Repositories\OfflineCategoryRepo;
use App\Repositories\SmsRepo;
use Carbon\Carbon;
use App\Repositories\FilterRepo;
use App\Repositories\AttributeRepo;
use App\Models\Customer;
use Validator;
use Request;
use App\Repositories\CountryRepo;
use App\Models\Country;
use App\Repositories\CustomerRepo;
use App\Repositories\GeneratedCodeRepo;
use App\Models\Product;
use App\Repositories\FundRepo;
use PDF;

class AjaxController extends Controller
{

    public function __construct(OrderRepo $orderrepo, CourierRepo $courierrepo, Mailer $mailer, StoreRepo $storerepo, CityRepo $cityrepo, OrderOfflineRepo $orderofflinerepo, VcoinLogRepo $VcoinLogRepo, MerchantRepo $merchantrepo, StateRepo $staterepo, ProductRepo $productrepo, ProductImageRepo $productimagerepo, ProductPricingRepo $productpricingrepo )
    {
        $this->order = $orderrepo;
        $this->courier = $courierrepo;
        $this->mailer = $mailer;
        $this->store = $storerepo;
        $this->city = $cityrepo;
        $this->orderOffline = $orderofflinerepo;
        $this->merchant = $merchantrepo;
        $this->vcoin = $VcoinLogRepo;
        $this->state = $staterepo;
        $this->product = $productrepo;
        $this->image = $productimagerepo;
        $this->pricing = $productpricingrepo;

    }

    public function load_size()
    {
        $input = \Request::only('size_id', 'data');
        $data = explode(',', $input['data']);

        if (in_array($input['size_id'], $data))
            return \Response::json([
                'status' => 0,
                'error' => trans('localize.sizealreadyselected')
            ]);

        $get_size = $this->size->get_size_by_id($input['size_id']);

        if (!$get_size)
            return \Response::json([
                'status' => 0,
                'error' => trans('localize.sizenotavailable')
            ]);

        return \Response::json([
            'status' => 1,
            'size_id' => $get_size->si_id,
            'size_name' => $get_size->si_name
        ]);
    }

    public function load_color()
    {
        $input = \Request::only('color_id', 'data');
        $data = explode(',', $input['data']);

        if (in_array($input['color_id'], $data))
            return \Response::json([
                'status' => 0,
                'error' => trans('localize.coloralreadyselected')
            ]);

        $get_color = $this->color->get_color_by_id($input['color_id']);

        if (!$get_color)
            return \Response::json([
                'status' => 0,
                'error' => trans('localize.colorselectednotavailable')
            ]);

        return \Response::json([
            'status' => 1,
            'color_id' => $get_color->co_id,
            'color_code' => $get_color->co_code,
            'color_name' => $get_color->co_name
        ]);
    }

    public function order_detail($id) {

        $type = (\Auth::guard('admins')->check())? 'admin' : 'merchant';
        $shipping = $this->order->get_order_details($id);
        $address = $this->order->get_shipping_address($id);

        return view('modals.order_details', compact('shipping', 'address', 'type'))->render();
    }

    public function print_invoice($id) {

        $type = (\Auth::guard('admins')->check())? 'admin' : '';
        $shipping = $this->order->get_order_details($id);
        $address = $this->order->get_shipping_address($id);

        return view('modals.order_details_print', compact('shipping', 'address','type'))->render();
    }

    public function view_shipment($id)
    {
        $courier = $this->order->get_shipment_detail($id);
        return view('modals.view_shipment', compact('courier'))->render();
    }

    public function accept_order($id, $status)
    {
        $update = $this->order->update_order_status($id, $status);
        if ($update) {
            // if($response['notify'] == 1) {
            //     $merchant = $this->merchant->find_merchant($response['mer_id']);

            //     $data = array(
            //         'product_title' => $response['product']->pro_title_en,
            //     );

            //     $this->mailer->send('front.emails.merchant_notify_product_quantity', $data, function (Message $m) use ($merchant) {
            //         $m->to($merchant->email, $merchant->mer_fname)->subject('Product out of stock!');
            //     });

            // }
            return back()->with('success', trans('localize.orderaccepted'));
        } else {
            return back()->with('error', trans('localize.ordererror'));
        }
    }

    public function update_shipment($order_id)
    {
        $order = OrderRepo::get_order_by_id($order_id);
        $couriers = $this->courier->get_couriers();
        return view('modals.update_shipment', compact('order_id', 'couriers', 'order'))->render();
    }

    public function update_shipment_submit($order_id)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'courier' => 'required_if:shipment,courier',
                'trackno' => 'required',
	        ],[
                'trackno.required' => ($data['courier'] == 'courier')? trans('localize.Enter_Tracking_No') : trans('localize.Please_fill_remarks_field'),
            ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            if($data['shipment'] == 'cod')
                $data['courier'] = 0;

            // $order_id = $data['id'];
            $data = array(
                'order_status' => 3,
                'order_courier_id' => $data['courier'],
                'order_tracking_no' => $data['trackno'],
                'order_shipment_date' => date('Y-m-d H:i:s'),
            );
            $update = $this->order->update_order_shipment($data,$order_id);

            if ($update) {
                $order_detail = $this->order->get_order_details($order_id);
                $address = $this->order->get_shipping_address($order_id);

                $checkout_data[] = array(
                    'name' => $order_detail->pro_title_en,
                    'quantity' => $order_detail->order_qty,
                    'vtoken' => number_format(($order_detail->order_credit / $order_detail->order_qty),4),
                    'total' => ($order_detail->order_credit),
                    'address' => $address->ship_address1.', '.$address->ship_address2.', '.$address->ship_postalcode.' '.$address->ship_city_name.', '.(!empty($address->ship_state_id) ? $address->name : $address->ci_name).', '.$address->co_name,
                );

                $data = array(
                    'deliveryBy' => $order_detail->order_courier_id,
                    'courierName' => $order_detail->name,
                    'courierLink' => $order_detail->link,
                    'trackingNo' => $order_detail->order_tracking_no,
                    'checkouts' => $checkout_data,
                );

                if ($order_detail->email_verified) {
                    $this->mailer->send('front.emails.order_shipment', $data, function (Message $m) use ($order_detail) {
                        $m->to($order_detail->email, $order_detail->cus_name)->subject('Order is Shipped!');
                    });
                }

                return back()->with('success', trans('localize.shipmentdetailupdated'));
            } else {
                return back()->with('error', trans('localize.shipmentupdateerror'));
            }
        }
    }

    public function load_merchant_store()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $input = \Request::only('mer_id','store_id');
        $stores = $this->store->get_online_store_by_merchant_id_and_country($input['mer_id'], $admin_country_id_list);

        if ($stores) {
            $storeresult = "";
            $storeresult .="<option value=''>-- ".trans('localize.select_store')." --</option>";
            foreach ($stores as $store) {
                if($store->stor_id == $input['store_id']) {
                    $storeresult .= "<option value='" . $store->stor_id . "' selected> " . $store->stor_name . " </option>";
                } else {
                    $storeresult .= "<option value='" . $store->stor_id . "'> " . $store->stor_name . " </option>";
                }
            }
            echo $storeresult;
        } else {
            echo $storeresult = "<option value=''>".trans('localize.nostoreavailable')." </option>";
        }
    }

    public function load_city()
    {
        $input = \Request::only('country_id', 'city_id');
        $results = $this->city->get_cities_by_country_id($input['country_id']);
        if($results)
        {
            $return  = "";
            foreach($results as $result)
            {
                if ($result->ci_id == $input['city_id']) {
                    $return .= "<option value='" . $result->ci_id . "' selected> " . $result->ci_name . " </option>";
                } else {
                $return .= "<option value='".$result->ci_id."'> ".$result->ci_name." </option>";
                }
            }
            echo $return;
        }
        else
        {
            echo $return = "<option value=''> ".trans('localize.nodata')." </option>";
        }
    }

    public function order_offline_detail($id,$type)
    {
        $order = $this->orderOffline->get_order_offline_details($id);
        $date = Carbon::create('2017','10','1','0','0','0');

        return view('modals.order_offline_details', compact('order', 'type', 'date'))->render();
    }

    public function get_batch_inv($type_inv, $id, $type, $add=null)
    {
        $id = explode(',',$id);

        foreach ($id as $key => $value) {
           $orders[] = $this->orderOffline->get_order_offline_details($value);
        }

        $url = url('/backend/js/company.json');
        $data = json_decode(file_get_contents($url),true);
        foreach($data as $key => $val){
            if($val['name'] === $add){
                $address = $val;
            }
        }

        $date = Carbon::create('2017','10','1','0','0','0');

        return view('modals.order_offline_details_batch', compact('orders','type','date','type_inv','address' ))->render();
    }

    public function order_offline_trans_print($type_inv, $id, $type, $add = null)
    {
        $id = explode(',',$id);
        foreach ($id as $key => $value) {
           $orders[] = $this->orderOffline->get_order_offline_details($value);
        }

        $url = url('/backend/js/company.json');
        $data = json_decode(file_get_contents($url),true);
        foreach($data as $key => $val){
            if($val['name'] === $add){
                $address = $val;
            }
        }

        $date = Carbon::create('2017','10','1','0','0','0');

        return view('modals.order_offline_trans_print', compact('orders','date','type','type_inv','address'))->render();
    }

    public function order_offline_trans_pdf($type_inv, $id,$type, $add=null)
    {
        $id = explode(',',$id);
        foreach ($id as $key => $value) {
           $orders[] = $this->orderOffline->get_order_offline_details($value);
        }

        $url = url('/backend/js/company.json');
        $data = json_decode(file_get_contents($url),true);
        foreach($data as $key => $val){
            if($val['name'] === $add){
                $address = $val;
            }
        }

        $date = Carbon::create('2017','10','1','0','0','0');

        $pdf = PDF::loadView('modals.order_offline_trans_print',  compact('orders','date','type','type_inv','address'));

        if($type_inv == 'trans_ref'){
            return $pdf->download('transaction_reference.pdf');
        }elseif($type_inv == 'mer_inv'){
            return $pdf->download('merchant_invoice.pdf');
        }else{
            return $pdf->download('tax_invoice.pdf');
        }

    }

    public static function get_inv_pdf($id, $type,$type_inv,$add = null)
    {
        $order = OrderOfflineRepo::get_order_offline_details($id);
        $date = Carbon::create('2017','10','1','0','0','0');

        $address = "";

        $url = url('/backend/js/company.json');
        $data = json_decode(file_get_contents($url),true);
        foreach($data as $key => $val){
            if($val['name'] === $add){
                $address = $val;
            }
        }

        $pdf = PDF::loadView('modals.order_offline_details_print', compact('order', 'type','type_inv', 'date','address'));

        if($type_inv == 'trans'){
            return $pdf->download('transaction_reference.pdf');
        }elseif($type_inv == 'tax_inv'){
            return $pdf->download('tax_invoice.pdf');
        }else{
            return $pdf->download('merchant_tax_invoice.pdf');
        }


    }

    public function order_offline_detail_print($id,$type,$type_inv,$add = null) {
        $order = $this->orderOffline->get_order_offline_details($id);
        $date = Carbon::create('2017','10','1','0','0','0');
        $address = "";

        $url = url('/backend/js/company.json');
        $data = json_decode(file_get_contents($url),true);
        foreach($data as $key => $val){
            if($val['name'] === $add){
                $address = $val;
            }
        }

        return view('modals.order_offline_details_print', compact('order','type','type_inv','date', 'address'))->render();
    }

    public function get_merchant_bank_info($mer_id)
    {
        $merchant = $this->merchant->get_merchant_bank_info($mer_id);
        return view('modals.merchant_bank_info', compact('merchant'))->render();
    }

    public function vcoinlog($id)
    {
        $vcoin = $this->vcoin->get_vcoin_log($id);
        $customer = $this->vcoin->get_customer_detail($id);
        return view('modals.vcoinlog', compact('vcoin','customer'))->render();

    }

    public function gplog($id)
    {
        $gp = $this->vcoin->gamepoint_log_details($id);
        $customer = $this->vcoin->get_customer_detail($id);
        return view('modals.gplog', compact('customer','gp'))->render();
    }

    public function load_state()
    {
        $input = \Request::only('country_id', 'state_id');
        $results = $this->state->get_states_by_country_id($input['country_id']);
        if($results)
        {
            $return  = "";
            $return .="<option value=''>".trans('localize.selectState')."</option>";
            foreach($results as $result)
            {
                if ($result->id == $input['state_id'] || $result->name == $input['state_id']) {
                    $return .= "<option value='" . $result->id . "' selected> " . $result->name . " </option>";
                } else {
                $return .= "<option value='".$result->id."'> ".$result->name." </option>";
                }
            }
            echo $return;
        }
        else
        {
            echo $return = "<option value=''> ".trans('localize.nodata')." </option>";
        }
    }

    public function edit_product_image($mer_id, $id)
    {
        $image = $this->image->get_product_image_by_id($id);
        return view('modals.edit_product_image', compact('image','mer_id'))->render();
    }

    public function edit_product_image_submit()
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $id = $data['id'];
            $mer_id = $data['mer_id'];

            $v = \Validator::make($data, [
                'file' => 'image|mimes:jpeg,jpg,png|max:1000',
                ],[
                'file.required' => 'Image File is required',
                'file.mimes' => 'Only jpeg,jpg and png file is allowed',
                'file.max' => 'Image size is too big, maximum size is 1mb',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            if (!empty($data['file'])) {
                $file = $data['file'];
                $upload_file = $file->getClientOriginalName();
                $file_detail = explode('.', $upload_file);
                $new_file_name = date('Ymd').'_'.str_random(4).'.'.$file_detail[1];

                $path = 'product/'.$mer_id;
                if(@file_get_contents($file) && !S3ClientRepo::IsExisted($path, $new_file_name))
                    S3ClientRepo::Upload($path, $file, $new_file_name);

                $data['new_file_name'] = $new_file_name;
                S3ClientRepo::Delete($path, $data['old_image']);

            } else {
                $data['new_file_name'] = $data['old_image'];
            }

            $image = $this->image->edit_product_image($id,$data);
            return back()->with('success','Successfully edit image');
        }
    }

    public static function product_category($parent_id = 0)
    {
        $ticket = current(\Request::only('ticket'));
        return CategoryRepo::json_get_category_listing_by_id($parent_id, $ticket);
    }

    public function edit_product_pricing($mer_id,$id)
    {
        $price = ProductPricingRepo::get_product_pricing_by_id($id);

        $daterange = '';
        if(($price->discounted_from && $price->discounted_to) != null) {
            $countrycode = strtoupper($price->co_code);
            $timezone = current(\DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countrycode));
            $from = date('d-m-Y H:i:s', strtotime($price->discounted_from));
            $to = date('d-m-Y H:i:s', strtotime($price->discounted_to));

            $parseFrom =  Carbon::createFromFormat('d-m-Y H:i:s',$from, 'UTC');
            $parseFrom->setTimezone($timezone);
            $price->discounted_from = $parseFrom;

            $parseTo =  Carbon::createFromFormat('d-m-Y H:i:s',$to, 'UTC');
            $parseTo->setTimezone($timezone);
            $price->discounted_to = $parseTo;

            $daterange = $price->discounted_from->format('d-m-Y H:i:s').' - '.$price->discounted_to->format('d-m-Y H:i:s');
        }


        return view('modals.edit_product_pricing', compact('price','mer_id','daterange'))->render();
    }

    public function edit_product_pricing_submit()
    {
         if (\Request::isMethod('post')) {
            $data = \Request::all();
            $id = $data['id'];
            $from = null;
            $to = null;

            \Validator::make($data, [
                'pro_price' => 'required|numeric|min:0',
                'pro_dprice' => 'nullable|numeric|min:0',
                'shipping_fees_type' => 'required',
                'shipping_fees' => 'required_unless:shipping_fees_type,0,shipping_fees_type,3',
                'coupon_value' => 'sometimes|required|min:0',
	        ])->validate();

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

            $update_entry = [
                'currency_rate' => $data['currency_rate'],
                'price' => $data['pro_price'],
                'shipping_fees' => ($data['shipping_fees_type'] == 0 || $data['shipping_fees_type'] == 3) ? 0 : $data['shipping_fees'],
                'shipping_fees_type' => $data['shipping_fees_type'],
                'discounted_price' => $data['pro_dprice'],
                'discounted_rate' => !empty($data['pro_drate'])? $data['pro_drate'] : null,
                'discounted_from' => $from,
                'discounted_to' => $to,
                'delivery_days' => $data['pro_delivery'],
                'coupon_value' => isset($data['coupon_value'])? $data['coupon_value'] : null,
            ];

            $pricing = ProductPricingRepo::update_pricing($id,$update_entry);
            return redirect()->back()->with('success', trans('localize.success_edit_product_pricing'));
         }
    }

    public static function offline_category($parent_id = 0)
    {
        return OfflineCategoryRepo::json_get_category_listing_by_id($parent_id);
    }

    public function add_category_filter()
    {
        $input = \Request::only('filter_id', 'category_id');
        return FilterRepo::add_category_filter($input['filter_id'], $input['category_id']);
    }

    public function remove_category_filter()
    {
        $input = \Request::only('filter_id', 'category_id');
        $remove = FilterRepo::remove_category_filter($input['filter_id'], $input['category_id']);
        return $remove;
    }

    public function edit_pricing_attribute_quantity($mer_id, $pro_id, $pricing_id)
    {
        $price = ProductPricingRepo::get_product_pricing_by_id(json_decode($pricing_id, true)[0]);
        $quantity = $price->quantity;

        $lists = AttributeRepo::get_product_attribute_for_pricing($price->pro_id);
        if(!$lists->isEmpty()){
            foreach ($lists as $attribute) {
                foreach ($attribute as $item) {
                    $item->selected = AttributeRepo::check_pricing_attribute_selected($price->id, $item->id);
                }
            }
        }

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'quantity' => 'required|numeric|min:0',
                'operation' => 'required|in:override,add,deduct',
                ],[
                'quantity.required' => 'Quantity is required',
            ])->validate();

            $pro_id = $price->pro_id;
            $country_id = $price->country_id;
            $attribute_status = $price->attribute_status;

            switch ($data['operation']) {
                case 'override':
                    $data['quantity'] = $data['quantity'];
                    break;

                case 'add':
                    $data['quantity'] = $quantity + $data['quantity'];
                    break;

                case 'deduct':
                    $data['quantity'] = $quantity - $data['quantity'];
                    if($data['quantity'] < 0)
                        return back()->with('error', trans('localize.max_quantity_deduct', ['quantity' => $quantity]));
                    break;
            }

            try {

                if(isset($data['attribute'])){
                    $check_attribute = AttributeRepo::check_set_pricing_attribute_exist($pro_id, $country_id, $data['attribute']);
                    if($check_attribute)
                        AttributeRepo::update_product_pricing_attribute($pro_id, $pricing_id, $data['attribute']);

                    if($data['quantity'] == $quantity && !$check_attribute)
                        return back()->with('error', 'Product options has exist, please select different options');
                }

                if($data['quantity'] != $quantity)
                    ProductPricingRepo::update_related_pricing_quantity($pro_id, $price->id, 'exist_item', $data['quantity']);

                return redirect()->back()->with('success', trans('localize.success_edit_product_pricing'));
            } catch (\Exception $e) {
                return back()->with('error', 'Unable to edit product options');
            }
        }

        return view('modals.edit_product_pricing_attribute_quantity',compact('lists','mer_id','pro_id','pricing_id', 'quantity'))->render();
    }

    public function check_attribute_exist($mer_id, $pro_id, $attribute_id)
    {
        $check = AttributeRepo::check_single_pricing_attribute_exist($pro_id,$attribute_id);

        return $check;
    }

    public function get_attribute_selection($pro_id)
    {
        $input = \Request::only('attributes');

        $pricing = AttributeRepo::get_pricing_by_selected_attributes($pro_id, $input['attributes']);

        $response = [];
        if(!is_null($pricing))
        {
            $response['purchase_price'] = $pricing->purchase_price;
            $response['price'] = $pricing->price;
            $response['discounted_rate'] = $pricing->discounted_rate;
            $response['price_id'] = $pricing->id;
            $response['quantity'] = $pricing->quantity;
            return $response;
        }

        return 'empty';
    }

    public function edit_product_attribute($attribute_id, $pro_id, $mer_id)
    {
        $lists = AttributeRepo::get_attribute_item_by($attribute_id, $pro_id, $mer_id);

        return view('modals.edit_product_attribute',compact('lists','mer_id','pro_id'))->render();
    }

    public function parent_check_attribute_exist($mer_id, $pro_id, $attribute_id)
    {
        $check = AttributeRepo::check_parent_pricing_attribute_exist($pro_id,$attribute_id);

        return $check;
    }

    public function update_attribute_parent_submit($pro_id)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $data['attribute'] = trim($data['attribute']);
            $data['old_attribute'] = trim($data['old_attribute']);

            $data['attribute_cn'] = ($data['attribute_cn'] == '') ? $data['old_attribute_cn'] : $data['attribute_cn'];
            $data['attribute_cnt'] = ($data['attribute_cnt'] == '') ? $data['old_attribute_cnt'] : $data['attribute_cnt'];

            $v = \Validator::make($data, [
                'attribute' => 'required',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $update = AttributeRepo::update_attribute_parent($data, $pro_id);

            if(!$update)
                return back()->with('error','Attribute already exist');

            return back()->with('success', 'Successfully update attribute');
        }
    }

    public function add_attribute_item_submit()
    {
        $data = \Request::only('item','attribute','mer_id','pro_id','itemcn','itemcnt','attribute_cn','attribute_cnt');
        $check = ProductRepo::check_product_is_belongs_to_merchant($data['pro_id'], $data['mer_id']);
        if(!$check)
            return [
                'status' => false,
                'message' => trans('localize.product_notBelongs'),
            ];

        $data['item'] = trim($data['item']);
        $v = \Validator::make($data, [
            'item' => 'required|unique:nm_product_attributes,attribute_item,NULL,id,pro_id,'.$data['pro_id'].',attribute,'.$data['attribute'],
        ],[
            'item.required' => trans('localize.fieldrequired'),
            'item.unique' => trans('localize.attribute_exist'),
        ]);

        if ($v->fails())
            return [
                'status' => false,
                'message' => collect($v->errors()->all())->implode(','),
            ];

        $attrb = AttributeRepo::add_attribute_item($data, $data['pro_id']);
        return [
            'status' => true,
            'id' => $attrb->id,
            'item' => $attrb->attribute_item,
            'item_cn' => $attrb->attribute_item_cn,
            'item_cnt' => $attrb->attribute_item_cnt,
        ];
    }

    public function store_usernamecheck() {
        $input = Request::only('username', 'id');
        $checkusername = StoreUser::where('username', '=', $input['username']);

        if (isset($input['id']))
            $checkusername->where('id', '!=', $input['id']);

        return $checkusername->count();
    }

    public function store_emailcheck() {
        $input = Request::only('email', 'id');
        $checkemail = StoreUser::where('email', '=', $input['email']);

        if (isset($input['id']))
            $checkemail->where('id', '!=', $input['id']);

        return $checkemail->count();
    }

    public function member_usernamecheck() {
        $input = Request::only('username', 'id');
        $checkusername = Customer::where('username', '=', $input['username']);

        if (isset($input['id']))
            $checkusername->where('cus_id', '!=', $input['id']);

        return $checkusername->count();
    }

    public function member_emailcheck() {
        $input = Request::only('email', 'id');
        $checkemail = Customer::where('email', '=', $input['email']);

        if (isset($input['id']))
            $checkemail->where('cus_id', '!=', $input['id']);

        return $checkemail->count();
    }

    public function member_phone_check() {
        $input = Request::only('phone', 'id');
        $checkPhone = Customer::where('cus_phone', '=', $input['phone']);

        if (isset($input['id']))
            $checkPhone->where('cus_id', '!=', $input['id']);

        return $checkPhone->count();
    }

    public function send_tac() {
        $input = Request::only('phone', 'action', 'email', 'type');
        $cus_id = (\Auth::user()) ? \Auth::user()->cus_id : 0;

        $phone = '';
        $email = '';

        if ($cus_id ) {
            $customer = CustomerRepo::get_customer_by_id($cus_id);
            $phone = $customer->phone_area_code . $customer->cus_phone;
            $email = $customer->email;
        }

        $type = ($input['type']) ? $input['type'] : 'sms';
        $phone = ($input['phone']) ? $input['phone'] : $phone;
        $email = ($input['email']) ? $input['email'] : $email;

        $tac = SmsRepo::create_tac_log($input['action'], $cus_id, $type, $phone, $email);

        return $tac;
    }

    public function check_tac() {
        $input = Request::only('phone', 'action', 'tac', 'email');
        $cus_id = (\Auth::user()) ? \Auth::user()->cus_id : 0;

        $phone = '';
        $email = '';

        if ($cus_id ) {
            $customer = CustomerRepo::get_customer_by_id($cus_id);
            $phone = $customer->phone_area_code . $customer->cus_phone;
            $email = $customer->email;
        }

        $phone = ($input['phone']) ? $input['phone'] : $phone;
        $email = ($input['email']) ? $input['email'] : $email;

        $tac = SmsRepo::check_tac($cus_id, $input['tac'], $input['action'], 0, $phone, $email);

        if ($tac)
            return 1;

        return 0;
    }

    public function check_member_verification($operation) {

        $input = Request::only('area', 'phone', 'email');

        $v = Validator::make($input, [
            'email' => (isset($input['email']))? 'required|email' : '',
            'area' => (!isset($input['email']))? 'required|numeric' : '',
            'phone' => (!isset($input['email']))? 'required|numeric' : '',
        ]);

        if ($v->fails())
           return 3;

        switch ($operation) {
            case 'by_phone':
                $area = $input['area'];
                $phone = $input['phone'];

                if($area && $phone) {
                    $check = Customer::where('phone_area_code', $area)
                    ->where('cus_phone', $phone)
                    ->where('cellphone_verified', 1)
                    ->where('cus_status', 1)
                    ->first();

                    if($check)
                        return 1;
                }

                return 0;
                break;

            case 'by_email':
                $email = $input['email'];
                if($email) {
                    $check = Customer::where('email', $email)
                    ->where('email_verified', 1)
                    ->where('cus_status', 1)
                    ->first();

                    if($check)
                        return 1;
                }

                return 0;
                break;

            default:
                return 3;
                break;
        }

        return 3;
    }

    public function get_code_number_listing($order_id, $by, $type)
    {
        $order = OrderRepo::get_order_details($order_id);

        if(!$order)
            return 0;

        switch ($type) {
            case 'coupons':
                $listing = $order->coupons;
                break;

            case 'tickets':
                $listing = $order->tickets;
                break;

            case 'ecard':
                $listing = $order->ecards;
                break;

            default:
                return 0;
                break;
        }

        if($listing->isEmpty())
            return 0;

        switch ($by) {
            case 'admin':
                if(!\Auth::guard('admins')->check())
                    return 0;
                break;

            case 'merchant':
                if(!\Auth::guard('merchants')->check() || $order->pro_mr_id != \Auth::guard('merchants')->user()->mer_id )
                    return 0;
                break;

            case 'member':
                if(!\Auth::check() || $order->order_cus_id != \Auth::user()->cus_id)
                    return 0;
                break;

            default:
                return 0;
                break;
        }

        return view('modals.code_listing', compact('order', 'listing', 'by'))->render();
    }

    public function saveImage()
    {
        $file = Request::file('file');
        $data = \Request::only('mer_id');
        $mer2 = json_decode($data['mer_id']);
        $main_image = 1;
        $mer_id = $mer2;
        $image = \Helper::upload_image($file, $main_image, $mer_id);
        $image = env('IMAGE_DIR').'/product/'.$mer_id.'/'.$image;
        return $image;
    }

    public static function load_name()
    {
        $products = Product::select('nm_product.pro_title_en as name');

        if(\Auth::guard('merchants')->check()){
            $mer_id = \Auth::guard('merchants')->user()->mer_id;
            $products = $products->where('nm_product.pro_mr_id', '=', $mer_id);
        }

        $products = $products->groupby('nm_product.pro_title_en')->get();
        return $products;
    }

    public static function load_store_name()
    {
        $products = Product::leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->groupby('nm_store.stor_name')
        ->select('nm_store.stor_name as name')->get();
        return $products;
    }

    public static function load_merchant_name()
    {
        $products = Product::leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');

        if(\Auth::guard('merchants')->check()){
            $mer_id = \Auth::guard('merchants')->user()->mer_id;
            $products = $products->where('nm_merchant.mer_id', '=', $mer_id);
        }

        $products = $products->groupby('nm_merchant.mer_fname')->select('nm_merchant.mer_fname as name')->get();
        return $products;
    }

    public static function get_fund_withdraw_statement()
    {
        $request = \Request::only('mer_id', 'fund_id');
        $v = Validator::make($request, [
            'mer_id' => 'required|integer',
            'fund_id' => 'required|integer',
        ]);

        if ($v->fails())
            return \Response::json(0);

        $fund = FundRepo::get_fund($request['fund_id']);
        if(!$fund || !$fund->wd_statement)
            return \Response::json(0);

        if($request['mer_id'] == 0) {
            if(!\Auth::guard('admins')->check())
                return \Response::json(0);
        } else {
            $mer_id = (\Auth::guard('merchants')->check())? \Auth::guard('merchants')->user()->mer_id : 0;
            if($fund->wd_mer_id != $mer_id || $request['mer_id'] != $mer_id)
                return \Response::json(0);
        }

        $file = env('IMAGE_DIR')."/fund/statement/$fund->wd_mer_id/$fund->wd_statement";
        $type = explode(".", $fund->wd_statement)[1];

        return view('modals.fund_statement_file', compact('file', 'type'))->render();
    }


    public function download_ecard_template()
    {
        $path = public_path().'/web/file/ecardtemplate.xlsx';
        $templateName = 'E-Card Serial Number Template.xlsx';
        return response()->download($path, $templateName);
    }

    public function delete_product_attribute($attribute_id, $option)
    {
        $request = \Request::only('product_id', 'merchant_id');
        if(!$request['product_id'] || !$request['merchant_id'])
            return back()->with('error', trans('localize.invalid_operation'));

        $mer_id = $request['merchant_id'];
        $pro_id = $request['product_id'];
        $merchant_id = null;

        if(\Auth::guard('merchants')->check()) {
            $merchant_id = \Auth::guard('merchants')->user()->mer_id;
        }

        if(\Auth::guard('storeusers')->check()) {
            $merchant_id = \Auth::guard('storeusers')->user()->mer_id;
        }

        if($merchant_id && $mer_id != $merchant_id && !\Auth::guard('admins')->check())
            return back()->with('error', trans('localize.invalid_operation'));

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return back()->with('error', trans('localize.invalid_operation'));

        switch ($option) {
            case 'normal':
                AttributeRepo::delete_product_attribute($attribute_id);
                break;

            case 'force':
                AttributeRepo::delete_product_attribute($attribute_id);
                AttributeRepo::update_attribute_status_flag_to_inactive($pro_id, $attribute_id);
                break;
        }

        return back()->with('success', trans('localize.successfully_delete_attribute'));
    }

    public function parent_delete_product_attribute($attribute_id, $option)
    {
        $request = \Request::only('product_id', 'merchant_id');
        if(!$request['product_id'] || !$request['merchant_id'])
            return back()->with('error', trans('localize.invalid_operation'));

        $mer_id = $request['merchant_id'];
        $pro_id = $request['product_id'];
        $merchant_id = null;

        if(\Auth::guard('merchants')->check()) {
            $merchant_id = \Auth::guard('merchants')->user()->mer_id;
        }

        if(\Auth::guard('storeusers')->check()) {
            $merchant_id = \Auth::guard('storeusers')->user()->mer_id;
        }

        if($merchant_id && $mer_id != $merchant_id && !\Auth::guard('admins')->check())
            return back()->with('error', trans('localize.invalid_operation'));

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return back()->with('error', trans('localize.invalid_operation'));

        AttributeRepo::delete_product_attribute_parent($pro_id, $attribute_id, $option);
        return back()->with('success', trans('localize.successfully_delete_attribute'));
    }
}
