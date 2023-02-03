<?php
namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\ProductRepo;
use App\Repositories\StoreRepo;
use App\Repositories\ProductImageRepo;
use App\Repositories\ProductPricingRepo;
use App\Repositories\CountryRepo;
use App\Repositories\AttributeRepo;
use App\Repositories\FilterRepo;
use App\Repositories\GeneratedCodeRepo;
use App\Repositories\LimitRepo;
use Carbon\Carbon;
use Validator;
use Helper;
use Excel;

class ProductController extends Controller
{
    private $mer_id;
    private $route;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(\Auth::guard('merchants')->check()) {
                $this->mer_id = \Auth::guard('merchants')->user()->mer_id;
                $this->logintype = 'merchants';
                $this->route = 'merchant';
            }

            if(\Auth::guard('storeusers')->check()) {
                $this->mer_id = \Auth::guard('storeusers')->user()->mer_id;
                $this->logintype = 'storeusers';
                $this->route = 'store';
            }

            return $next($request);
        });
    }

    public function add_product()
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $stores = StoreRepo::get_online_store_by_merchant_id($mer_id);
        if(count($stores) == 0)
        {
            if($this->logintype == 'storeusers')
                return redirect('/'.$this->route.'/product/manage')->with('error', trans('localize.your_account_not_assigned_to_any_store'));

            return redirect('merchant/product/manage')->with('error',trans('localize.add_product_error'));
        }

        $limit_types = LimitRepo::getProductLimitTypes();

        return view('merchant.product.add',compact('stores', 'limit_types'));
    }

    public function add_product_submit()
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            Validator::make($data, [
                'pro_type' => 'required|integer|in:1,4',
                'pro_title_en' => 'required',
                'stor_id' => 'required',
                'file' => 'required|mimes:jpeg,jpg,png|max:1000',
                'stor_id' => 'required',
                'start_date' => 'required_if:pro_type,3',
                'end_date' => 'required_if:pro_type,3',
                // 'limit_enabled' => 'required|boolean',
                // 'limit_quantity' => $data['limit_enabled']? 'integer|min:1' : 'integer',
                // 'limit_type' => 'integer|in:0,1,2,3',
	        ])->validate();

            if(!empty($data['start_date']) && !empty($data['end_date']) && ($data['pro_type'] == 3 || $data['pro_type'] == 4)) {
                $data['start_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['start_date'])));
                $data['end_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['end_date'])));
            } else {
                $data['start_date'] = null;
                $data['end_date'] = null;
            }

            if(empty(json_decode($data['selected_cats'])))
                return back()->withInput()->withError('Please select categories');

            $product = ProductRepo::add_product($data, $mer_id);
            if(!$product)
                return back()->withInput()->withError('Failed to add product');

            // Upload image
            if (!empty($data['file'])) {

                $file = $data['file'];
                $main_image = 1;

                $image = Helper::upload_image($file, $main_image, $mer_id);

                $data['new_file_name'] = $image;
                $save_image = ProductImageRepo::add_product_main_image($product->pro_id,$data);
            }

            if (!empty($data['selected_cats'])) {
                $cats = json_decode($data['selected_cats']);
                foreach ($cats as $key => $cat) {
                    if ($cat) {
                        $pro_category = array(
                            'product_id' => $product->pro_id,
                            'category_id' => $cat,
                        );

                        ProductRepo::insert_product_category_details($pro_category);
                    }
                }
            }

            return redirect('merchant/product/edit/'.$product->pro_id)->with('success', trans('localize.add_product_success'));
        }
    }

    public function edit_product($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $stores = StoreRepo::get_online_store_by_merchant_id($mer_id);
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $main_category = ProductRepo::get_product_parent_category($pro_id);

        if(empty($product))
            return redirect( $route . '/product/manage')->with('error', trans('localize.product_not_found'));

        $product_category_list = [];
        foreach ($product['category'] as $key => $category) {
            $product_category_list[] = strval($category['details']->category_id);
        }

        $limit_types = LimitRepo::getProductLimitTypes();

        return view('merchant.product.edit',compact('stores', 'product', 'product_category_list', 'route', 'main_category', 'limit_types'));
    }

    public function edit_product_submit($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        if(!$product)
            return back()->with('error', trans('localize.invalid_request'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            Validator::make($data, [
                'pro_title_en' => 'required',
                'stor_id' => 'required',
                'start_date' => ($product['details']->pro_type == 3)? 'sometimes|required' : '',
                'end_date' =>($product['details']->pro_type == 3)? 'sometimes|required' : '',
                // 'limit_enabled' => 'required|boolean',
                // 'limit_quantity' => $data['limit_enabled']? 'integer|min:1' : 'integer',
                // 'limit_type' => 'integer|in:0,1,2,3',
	        ])->validate();

            if(isset($data['start_date']) && isset($data['end_date']) && !empty($data['start_date']) && !empty($data['end_date'])) {
                $data['start_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['start_date'])));
                $data['end_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['end_date'])));
            } else {
                $data['start_date'] = null;
                $data['end_date'] = null;
            }

            if(empty(json_decode($data['selected_cats'])))
                return back()->withInput()->withError('Please select categories');

            try {
                $product = ProductRepo::edit_product($data, $mer_id, $pro_id);
                // $main_category = ProductRepo::get_product_parent_category($pro_id);

                if ($product) {
                    // $review = false;
                    if (!empty($data['selected_cats'])) {
                        $cats = json_decode($data['selected_cats']);
                        $returncolor = ProductRepo::delete_product_category($pro_id);
                        foreach ($cats as $key => $cat) {
                            // if($main_category && $key == 0 && $cat != $main_category && $product->pro_status == 1)
                            //     $review = true;
                            if ($cat) {
                                $pro_category = array(
                                    'product_id' => $product->pro_id,
                                    'category_id' => $cat,
                                );

                                try {
                                    ProductRepo::insert_product_category_details($pro_category);
                                } catch (\Exception $e) {
                                    return redirect($route . '/product/manage')->with('error', trans('localize.successfully_add_product_failed_add_product_category'));
                                }
                            }
                        }
                    }

                    // if($review) {
                    //     ProductRepo::update_product_status($pro_id, 3);
                    // }

                    if($product->pro_type == 4) {
                        GeneratedCodeRepo::update_ecard_validity($mer_id, $pro_id);
                    }
                }

            } catch (\Exception $e) {
                return back()->withErrors('Unable to edit product. Please try again.');
            }

            if($product->pro_status == 1)
                return back()->with('success', trans('localize.edit_product_success'));

            $image = ProductImageRepo::get_product_image_by_pro_id($product->pro_id);
            $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($product->pro_id);

            if(!$pricing->isEmpty() && !$image->isEmpty())
            {
                ProductRepo::update_product_status($product->pro_id, 0);
            }

            return back()->with('success', trans('localize.edit_product_success'));
        }
    }

    public function manage_product()
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $input = \Request::only('id', 'name', 'status', 'sort','search_type');

        $products = ProductRepo::get_merchant_products($mer_id, $input);
        foreach ($products as $key => $product) {
            $product->image = ProductImageRepo::get_product_main_image($product->pro_id);
            $product->category_name = ProductRepo::get_product_main_category($product->pro_id);
        }

        return view('merchant.product.manage', compact('products', 'mer_id', 'input', 'route'));
    }

    public function change_product_status($pro_id,$type)
    {
        $product = ProductRepo::update_product_status($pro_id,$type);

        return back()->with('status','Product '.$type);
    }

    public function sold_product()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $input = \Request::only('id', 'name', 'sort');

        $products = ProductRepo::get_merchant_sold_products($mer_id, $input);

        foreach ($products as $key => $product) {
            $product = $product;
            $product->image = ProductImageRepo::get_product_main_image($product->pro_id);
        }

        return view('merchant.product.sold', compact('products', 'input'));
    }

    public function manage_product_shipping_details()
	{
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $input = \Request::only('id', 'name', 'status', 'sort');
        $status_list = array(
            '' => trans('localize.all'),
            '3' => trans('localize.shipped'),
            '4' => trans('localize.completed'),
            '5' => trans('localize.canceled'),
        );

        $shippings = ProductRepo::get_shipping_details($mer_id, $input);

        return view('merchant.product.shipping' , compact('status_list', 'shippings', 'input'));
	}

    public function view_product($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $images = ProductImageRepo::get_product_image_by_pro_id($pro_id);
        // $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id);
        $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id)->groupBy('attributes_name');

        if(empty($product))
            return redirect($route . '/product/manage')->with('error', trans('localize.product_not_found'));

        return view('merchant.product.view', compact('product','images','pricing','route'));
    }

    public function product_pricing($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $product = ProductRepo::get_merchant_product_details($mer_id,$pro_id);
        $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id)->groupBy('attributes_name');
        $countries = CountryRepo::get_all_countries();
        $attributes = AttributeRepo::get_product_attribute_for_pricing($pro_id);

        $count = 0;
        $items = [];
        foreach ($pricing as $attr => $price_group) {

            foreach ($price_group as $key => $price) {
                $string = $price->attributes;
                $attr_names = json_decode($string);

                $attribute_string = "";
                if(!empty($attr_names)){
                    foreach ($attr_names as $attr_name) {
                        $attribute_string .= AttributeRepo::get_pricing_attributes($attr_name);
                    }
                }

                if($key == 0)
                    $quantity = $price->quantity;

                if($price->discounted_from != null && $price->discounted_to != null){
                    $countrycode = strtoupper($price->co_code);
                    $timezone = current(\DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countrycode));

                    $from = date('Y-m-d H:i:s', strtotime($price->discounted_from));
                    $to = date('Y-m-d H:i:s', strtotime($price->discounted_to));

                    $parseFrom =  Carbon::createFromFormat('Y-m-d H:i:s',$from, 'UTC');
                    $parseFrom->setTimezone($timezone);
                    $from = $parseFrom;

                    $parseTo =  Carbon::createFromFormat('Y-m-d H:i:s',$to, 'UTC');
                    $parseTo->setTimezone($timezone);
                    $to = $parseTo;

                    $price->discounted_from = $parseFrom;
                    $price->discounted_to = $parseTo;
                }
            }

            $items[$count] = [
                'attributes' => $attr,
                'quantity' => $quantity,
                'price_id' => $price_group->pluck('id')->toJson(),
                'pricing' => $price_group,
                'attributes_list' => $attribute_string,
            ];

            $count++;
        }

        return view('merchant.product.pricing',compact('product','countries','items','pro_id','mer_id','attributes','route'));
    }

    public function product_pricing_submit($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'country_id' => 'required|integer|not_in:0',
                'pro_price' => 'required|numeric|min:0',
                'quantity' => 'sometimes|required|integer|min:0',
                'shipping_fees_type' => 'required',
                'shipping_fees' => 'required_unless:shipping_fees_type,0,shipping_fees_type,3',
                'coupon_value' => 'sometimes|required',
	        ],[
                'country_id.required' => trans('localize.Country_Selection_is_required'),
                'pro_price.required' => trans('localize.product_price_required'),
                'quantity.required' => trans('localize.product_quantity_required'),
            ])->validate();

            $with_attributes = false;
            if(isset($data['attribute']))
                $with_attributes = true;

            if($with_attributes) {
                $check = AttributeRepo::check_set_pricing_attribute_exist($pro_id, $data['country_id'], $data['attribute']);
                if(!$check)
                    return back()->with('error', trans('localize.price_with_selected_attributes_already_exist'));
            } else {
                $check = ProductPricingRepo::check_set_pricing_exist($pro_id, $data['country_id']);
                if(!$check)
                    return back()->with('error', trans('localize.Price_with_selected_country_already_exist'));
            }

            $pricing = ProductPricingRepo::add_product_pricing($pro_id, $data);
            if($with_attributes) {
                $attribute = AttributeRepo::update_product_pricing_attribute($pro_id, json_encode([$pricing->id]), $data['attribute'], $data['country_id']);
                $check_pricing_attribute_set = AttributeRepo::check_pricing_attribute_set($pro_id, $data['country_id'], count($data['attribute']));
            }

            $update_related_quantity = ProductPricingRepo::update_related_pricing_quantity($pro_id, $pricing->id, 'new_item');

            if($data['status'] != 1) {
                $product_completed_status = ProductImageRepo::get_product_image_by_pro_id($pro_id);
                if(!$product_completed_status->isEmpty()) {
                    ProductRepo::update_product_status($pro_id, 1);
                }
            }

            if($with_attributes == true && $check_pricing_attribute_set == 'force') {
                return redirect()->back()->with('success', 'Successfully add product pricing, some of product pricing forced to disabled regarding missmatch attribute set. Please update affected pricing attribute to re enable pricing.');
            }

            return back()->with('success', trans('localize.Successfully_add_new_pricing'));
        }
    }

    public function update_pricing_status($id)
    {
        $price = ProductPricingRepo::update_pricing_status($id);
        return back()->with('success', trans('localize.successfully_update_pricing_status'));
    }

    public function view_product_quantity_log($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $logs = ProductRepo::get_product_quantity_log($pro_id);

        return view('merchant.product.quantity_log', compact('product','logs','mer_id','pro_id','route'));
    }

    public function manage_attribute($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $lists = AttributeRepo::get_product_attribute_simplified($pro_id);

        return view('merchant.product.attribute', compact('product','lists','mer_id','pro_id','route'));
    }

    public function delete_product_pricing($id)
    {
        ProductPricingRepo::delete_product_pricing($id);
        AttributeRepo::delete_pricing_attribute($id);

        return back()->with('success', trans('localize.successfully_delete_pricing'));
    }

    public function manage_filter($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $lists = FilterRepo::get_product_filter_by_selected_category($pro_id);

        return view('merchant.product.filter', compact('product','lists','mer_id','pro_id','route'));
    }

    public function update_filter($pro_id)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();

            if(!empty($data['filter'])) {
                $update = FilterRepo::update_product_filter($pro_id, $data);
                if($update == true) {
                    return back()->with('success', trans('localize.successfully_update_filter'));
                }

                return back()->with('error', trans('localize.error_while_update_filter'));
            } else {
                $update = FilterRepo::delete_product_filter($pro_id);
                if($update == true) {
                    return back()->with('success', trans('localize.successfully_update_filter'));
                }
            }

            return back()->with('error', trans('localize.error_while_update_filter'));
        }
    }

    public function add_attribute_submit($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'attribute' => 'required|max:255',
                'attribute_item' => 'required|max:255',
	        ],[
                'attribute.required' => 'Attribute name is required',
                'attribute_item.required' => 'Attribute item name is required',
            ]);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $insert = AttributeRepo::add_product_attribute($pro_id, $data);

            if($insert == 'success')
                return back()->with('success', trans('localize.success_create_attribute'));

            if($insert === 'existed')
                return back()->with('error','Attribute already exsist');

            return back()->with('error','Failed to create new attribute');
        }
    }

    public function update_pricing_status_batch($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        if (\Request::isMethod('post')) {

            $data = \Request::all();
            Validator::make($data, [
                'status' => 'required',
	        ],[
                'status.required' => trans('localize.select_status'),
            ])->validate();

            if(isset($data['pricing_id'])) {
                ProductPricingRepo::update_pricing_status_batch($pro_id, $data['pricing_id'], $data['status']);
                return back()->with('success', trans('localize.successfully_update_pricing_status'));
            } else {
                return back()->with('error', 'Tick checkbox before proceed');
            }
        }
    }

    public function product_description($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);

        if(empty($product))
            return redirect("$route/product/manage")->with('error', trans('localize.product_not_found'));

        return view('merchant.product.description',compact('product'));
    }

    public function product_description_submit($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            Validator::make($data, [
                'pro_desc_en' => 'required',
	        ])->validate();

            try {
                $product = ProductRepo::product_description($data, $mer_id, $pro_id);
            } catch (\Exception $e) {
                return back()->withErrors('Unable to edit product. Please try again.');
            }

            return back()->with('success', trans('localize.edit_product_success'));
        }
    }

    public function ecard_listing($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $input = \Request::only('serial_number', 'sort', 'status', 'show');
        $input['sort'] = $input['sort']? $input['sort'] : 'new';
        $input['status'] = strlen($input['status'])? $input['status'] : '';
        $input['show'] = $input['show']? $input['show'] : 20;

        $filters = json_decode(json_encode([
            'sort' => [
                'new' => trans('localize.Newest'),
                'old' => trans('localize.Oldest'),
                'serial_asc' => trans('localize.e-card.serial_number')." : &#xf15d;",
                'serial_desc' => trans('localize.e-card.serial_number')." : &#xf15e;",
            ],
            'status' => [
                '' => trans('localize.All'),
                '0' => trans('localize.open'),
                '1' => trans('localize.purchased'),
                '2' => trans('localize.redeemed'),
            ],
            'show' => [
                '20' => 20,
                '50' => 50,
                '100' => 100,
            ],
        ]));

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        $price = ProductPricingRepo::product_first_price($pro_id);
        if(!$price)
            return redirect("/$route/product/pricing/$pro_id")->with('error', trans('localize.available.after_add_pricing'));

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $listings = GeneratedCodeRepo::get_ecard_listing($mer_id, $pro_id, $input);

        return view('merchant.product.code', compact('product', 'listings', 'input', 'filters'));
    }

    public function ecard_upload($pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        $available = GeneratedCodeRepo::get_ecard_listing($mer_id, $pro_id)->pluck('serial_number')->toArray();

        $data = \Request::all();
        $v = Validator::make($data, [
            'file' => 'required|mimes:xlsx',
        ])->validate();

        if(isset($data['file'])) {

            $extract = array_filter(Excel::load($data['file'])->get()->pluck('serial_number')->toArray(), 'strlen');
            if(empty($extract)) {
                return back()->with('error', trans('localize.file.import.empty'));
            }

            $uploads = array_count_values($extract);

            //check duplicates serial number within file uploaded and exist serial number
            $errors = 0;
            $listings = [];
            foreach ($uploads as $serial => $duplicate) {

                $duplicate = $duplicate - 1;
                $exist = in_array($serial, $available);
                $validate = preg_match('/^[A-Za-z0-9_]+$/', $serial)? true : false;
                if($duplicate > 0 || $exist || !$validate)
                    $errors++;

                $listings[] = [
                    'status' => ($duplicate > 0 || $exist || !$validate)? false : true,
                    'serial_number' => $serial,
                    'duplicate' => $duplicate,
                    'exist' => $exist,
                    'validate' => $validate
                ];
            }

            $pass = ($errors == 0)? true : false;
            $total = count($extract);

            $listings = json_decode(json_encode($listings));
            if(!$pass )
                return back()->with('error', trans('localize.file.upload.failed_ecard', ['failed' => $errors, 'total' => $total]))->with('duplicates', $listings);

            GeneratedCodeRepo::update_ecard_serial_number($extract, $pro_id, $mer_id);
            ProductPricingRepo::update_related_pricing_quantity($pro_id, null, 'exist_item', $total);

            return back()->with('success', trans('localize.Successfully_updated_product'));
        }
    }

    public function ecard_redeem($id, $pro_id)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return redirect($route)->with('error', trans('localize.invalid_operation'));

        $ecard = GeneratedCodeRepo::find_code($id);
        if(!$ecard || $ecard->status <> 1 || $ecard->product_id <> $pro_id || $ecard->merchant_id <> $mer_id)
            return back()->with('error', trans('localize.invalid_request'));

        $status = GeneratedCodeRepo::redeem_code('ecard', $ecard->serial_number, null, $pro_id, $mer_id);
        if($status)
            return back()->with('success', trans('localize.e-card.redeem.success'));

        return back()->with('success', trans('localize.e-card.redeem.failed'));
    }
}
