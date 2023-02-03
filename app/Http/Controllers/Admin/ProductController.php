<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\MerchantRepo;
use App\Repositories\ProductRepo;
use App\Repositories\StoreRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductImageRepo;
use App\Repositories\CountryRepo;
use App\Repositories\ProductPricingRepo;
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
    public function view_product($mer_id,$pro_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);



        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);

        $store_country_id = isset($product['store']['stor_country']) ?  $product['store']['stor_country'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($store_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        $images = ProductImageRepo::get_product_image_by_pro_id($pro_id);
        // $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id);
        $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id)->groupBy('attributes_name');

        return view('admin.product.view', compact('product', 'images', 'pricing'));
    }


    public function manage_product()
    {

        $input = \Request::only('id', 'name', 'status', 'sort','sid','mid','search_type','countries');

		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);

		if(in_array('productmanagelist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        $products = ProductRepo::all($input);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $countries = CountryRepo::get_countries($admin_country_id_list);



        foreach ($products as $key => $product) {
            $product = $product;
            $product->image = ProductImageRepo::get_product_main_image($product->pro_id);
            $product->category = ProductRepo::get_product_main_category($product->pro_id);
        }

        return view('admin.product.manage', compact('countries','products','input','edit_permission','view_sold_products','view_sold_products'));

    }

    public function detail($id)
    {
        return view('modals.product_detail', ['product'=>ProductRepo::detail($id)]);
    }

    public function sold_product()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $input = \Request::only('id', 'name', 'sort');
        $input['admin_country_id_list'] = $admin_country_id_list;

        $solds = ProductRepo::sold($input);
        foreach ($solds as $key => $sold) {
            $sold = $sold;
            $sold->image = ProductImageRepo::get_product_main_image($sold->pro_id);
        }

        return view('admin.product.sold', compact('solds','input'));
    }

    public function update_product_status($pro_id, $status)
    {
        ProductRepo::update_product_status($pro_id, $status);

        if ($status == 1) {
            return back()->with('success', trans('localize.Succesfully_update_product_status_to_Active'));

        }else if ($status == 0) {
            return back()->with('success', trans('localize.Succesfully_update_product_status_to_Inactive'));
        }
    }

    public function manage_product_shipping_details()
	{
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $input = \Request::only('id', 'name', 'status', 'sort');
        $input['admin_country_id_list'] = $admin_country_id_list;

        $status_list = array(
            '' => trans('localize.all') ,
            '3' => trans('localize.shipped'),
            '4' => trans('localize.completed'),
            '5' => trans('localize.Canceled'),
        );
        $mer_id = 'all';
        $shippings = ProductRepo::get_shipping_details($mer_id, $input);

        return view('admin.product.shipping', compact('status_list', 'shippings', 'input'));
	}

    public function add_product()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $input['admin_country_id_list'] = $admin_country_id_list;

        $merchants = MerchantRepo::get_online_merchants($input);

        $limit_types = LimitRepo::getProductLimitTypes();

        return view('admin.product.add',compact('merchants', 'limit_types'));
    }

    public function add_product_submit()
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $mer_id = $data['mer_id'];

            Validator::make($data, [
                'pro_type' => 'required|integer|in:1,4',
                'pro_title_en' => 'required',
                'file' => 'required',
                'stor_id' => 'required|not_in:0',
                'file' => 'required|mimes:jpeg,jpg,png|max:1000',
                'mer_id' => 'required|integer',
                'start_date' => 'required_if:pro_type,3',
                'end_date' => 'required_if:pro_type,3',
                'limit_enabled' => 'required|boolean',
                'limit_quantity' => $data['limit_enabled']? 'integer|min:1' : 'integer',
                'limit_type' => 'integer|in:0,1,2,3',
	        ],[
                'file.required' => 'Image file is required',
            ])->validate();

            if(empty(json_decode($data['selected_cats'])))
                return back()->withInput()->withError('Please select categories');

            if(!empty($data['start_date']) && !empty($data['end_date']) && ($data['pro_type'] == 3 || $data['pro_type'] == 4)) {
                $data['start_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['start_date'])));
                $data['end_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['end_date'])));
            } else {
                $data['start_date'] = null;
                $data['end_date'] = null;
            }

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

                        try {
                            ProductRepo::insert_product_category_details($pro_category);
                        } catch (\Exception $e) {
                            return redirect('admin/product/manage')->with('error', trans('localize.successfully_add_product_failed_add_product_category'));
                        }
                    }
                }
            }

            return redirect('admin/product/edit/'.$product->pro_mr_id.'/'.$product->pro_id)->with('success', trans('localize.add_product_success'));
        }
    }

    public function edit_product($mer_id, $pro_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'productmanageedit');
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $stores = StoreRepo::get_online_store_by_merchant_id_and_country($mer_id, $admin_country_id_list);
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);

        $store_country_id = isset($product['store']['stor_country']) ?  $product['store']['stor_country'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($store_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        $product_quantity_sold = OrderRepo::get_product_quantity_sold($pro_id);
        $main_category = ProductRepo::get_product_parent_category($pro_id);
        $product_category_list = [];
        foreach ($product['category'] as $key => $category) {
            $product_category_list[] = strval($category['details']->category_id);
        }

        $limit_types = LimitRepo::getProductLimitTypes();

        return view('admin.product.edit',compact('stores', 'product', 'product_quantity_sold', 'product_category_list', 'main_category', 'edit_permission', 'limit_types'));
    }

    public function edit_product_submit($mer_id, $pro_id)
    {
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        if(!$product)
            return back()->with('error', trans('localize.invalid_request'));

        if (\Request::isMethod('post')) {

            $data = \Request::all();

            $v = Validator::make($data, [
                'pro_title_en' => 'required',
                'stor_id' => 'required',
                'start_date' => ($product['details']->pro_type == 3)? 'sometimes|required' : '',
                'end_date' =>($product['details']->pro_type == 3)? 'sometimes|required' : '',
                'limit_enabled' => 'required|boolean',
                'limit_quantity' => $data['limit_enabled']? 'integer|min:1' : 'integer',
                'limit_type' => 'integer|in:0,1,2,3',
            ])->validate();

            if(isset($data['start_date']) && isset($data['end_date']) && !empty($data['start_date']) && !empty($data['end_date'])) {
                $data['start_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['start_date'])));
                $data['end_date'] = Helper::TZtoUTC(date('Y-m-d H:i:s', strtotime($data['end_date'])));
            } else {
                $data['start_date'] = null;
                $data['end_date'] = null;
            }

            try {
                $product = ProductRepo::edit_product($data, $mer_id, $pro_id);
                // $main_category = ProductRepo::get_product_parent_category($pro_id);
                if ($product) {
                    // $review = false;
                    if (!empty($data['selected_cats'])) {
                        $cats = json_decode($data['selected_cats']);
                        $returncolor = ProductRepo::delete_product_category($pro_id);
                        foreach ($cats as $key => $cat) {
                            if ($cat) {
                                // if($main_category && $key == 0 && $cat != $main_category && $product->pro_status == 1)
                                //     $review = true;

                                $pro_category = array(
                                    'product_id' => $product->pro_id,
                                    'category_id' => $cat,
                                );

                                try {
                                    ProductRepo::insert_product_category_details($pro_category);
                                } catch (\Exception $e) {
                                    return redirect('admin/product/manage')->with('error', trans('localize.successfully_add_product_failed_add_product_category'));
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
                return back()->withErrors(trans('localize.Unable_to_edit_product'));
            }
            if($product->pro_status == 1)
                return back()->with('success', trans('localize.Successfully_updated_product'));

            $image = ProductImageRepo::get_product_image_by_pro_id($product->pro_id);
            $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($product->pro_id);

            if(!$pricing->isEmpty() && !$image->isEmpty())
            {
                ProductRepo::update_product_status($product->pro_id, 1);
            }

            return back()->with('success', trans('localize.Successfully_updated_product'));
        }
    }

    public function product_pricing($mer_id, $pro_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $edit_permission = Controller::checkAdminPermission($adm_id, 'productmanageedit');

        $countries = CountryRepo::get_countries($admin_country_id_list);
        $product = ProductRepo::get_merchant_product_details($mer_id,$pro_id);
        $pricing = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id)->groupBy('attributes_name');
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

        return view('admin.product.pricing',compact('product','countries','items','pro_id','mer_id','attributes','edit_permission'));
    }

    public function product_pricing_submit($mer_id, $pro_id)
    {
        $check = ProductRepo::check_product_is_belongs_to_merchant($pro_id, $mer_id);
        if(!$check)
            return back()->with('error', trans('localize.invalid_operation'));

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

    public function update_pricing_status($price_id, $mer_id)
    {
        $price = ProductPricingRepo::update_pricing_status($price_id);
        return back()->with('success', trans('localize.successfully_update_pricing_status'));
    }

    public function view_product_quantity_log($mer_id, $pro_id)
    {
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $logs = ProductRepo::get_product_quantity_log($pro_id);

        return view('admin.product.quantity_log', compact('product','logs','mer_id','pro_id'));
    }

    public function delete_product_pricing($id)
    {
        ProductPricingRepo::delete_product_pricing($id);
        AttributeRepo::delete_pricing_attribute($id);

        return back()->with('success', trans('localize.successfully_delete_pricing'));
    }

    public function manage_filter($mer_id, $pro_id)
    {
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $lists = FilterRepo::get_product_filter_by_selected_category($pro_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'productmanageedit');

        return view('admin.product.filter', compact('product','lists','mer_id','pro_id','edit_permission'));
    }

    public function update_filter($mer_id, $pro_id)
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

    public function manage_attribute($mer_id, $pro_id)
    {
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $lists = AttributeRepo::get_product_attribute_simplified($pro_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'productmanageedit');


        return view('admin.product.attribute', compact('product','lists','mer_id','pro_id','edit_permission'));
    }

    public function add_attribute_submit($mer_id,$pro_id)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'attribute' => 'required|max:255',
                'attribute_item' => 'required|max:255',
	        ],[
                'attribute.required' => trans('localize.Attribute_name_is_required'),
                'attribute_item.required' => trans('localize.Attribute_item_name_is_required'),
            ]);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $insert = AttributeRepo::add_product_attribute($pro_id, $data);

            if($insert == 'success')
                return back()->with('success', trans('localize.success_create_attribute'));

            if($insert === 'existed')
                return back()->with('error',trans('localize.Attribute_already_exists'));

            return back()->with('error', trans('localize.Failed_to_create_new_attribute'));
        }
    }

    public function delete_product_attribute($mer_id, $pro_id, $attribute_id, $option)
    {

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

    public function update_pricing_status_batch($pro_id)
    {
        if (\Request::isMethod('post')) {

            $data = \Request::all();
            $v = Validator::make($data, [
                'status' => 'required',
	        ],[
                'status.required' => trans('localize.select_status'),
            ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            if(isset($data['pricing_id'])) {
                ProductPricingRepo::update_pricing_status_batch($pro_id, $data['pricing_id'], $data['status']);
                return back()->with('success', trans('localize.successfully_update_pricing_status'));
            } else {
                return back()->with('error', trans('localize.Tick_checkbox_before_proceed'));
            }
        }
    }

    public function product_description($mer_id, $pro_id)
    {
        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);

        return view('admin.product.description',compact('product'));
    }

    public function product_description_submit()
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $mer_id = $data['mer_id'];
            $pro_id = $data['pro_id'];

            $v = Validator::make($data, [
                'pro_desc_en' => 'required',
            ])->validate();

            try {
                $product = ProductRepo::product_description($data, $mer_id, $pro_id);
            } catch (\Exception $e) {
                return back()->withErrors(trans('localize.Unable_to_edit_product'));
            }

            return back()->with('success', trans('localize.Successfully_updated_product'));

        }
    }

    public function ecard_listing($mer_id, $pro_id)
    {
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

        $price = ProductPricingRepo::product_first_price($pro_id);
        if(!$price)
            return redirect("/admin/product/pricing/$mer_id/$pro_id")->with('error', trans('localize.available.after_add_pricing'));

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $listings = GeneratedCodeRepo::get_ecard_listing($mer_id, $pro_id, $input);

        return view('admin.product.code', compact('product', 'listings', 'input', 'filters'));
    }

    public function ecard_upload($mer_id, $pro_id)
    {
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

    public function ecard_redeem($id, $pro_id, $mer_id)
    {
        $ecard = GeneratedCodeRepo::find_code($id);
        if(!$ecard || $ecard->status <> 1 || $ecard->product_id <> $pro_id || $ecard->merchant_id <> $mer_id)
            return back()->with('error', trans('localize.invalid_request'));

        $status = GeneratedCodeRepo::redeem_code('ecard', $ecard->serial_number, null, $pro_id, $mer_id);
        if($status)
            return back()->with('success', trans('localize.e-card.redeem.success'));

        return back()->with('success', trans('localize.e-card.redeem.failed'));
    }

    public function ecard_delete($id, $pro_id, $mer_id)
    {
        $ecard = GeneratedCodeRepo::find_code($id);
        if(!$ecard || $ecard->status <> 0 || $ecard->product_id <> $pro_id || $ecard->merchant_id <> $mer_id)
            return back()->with('error', trans('localize.invalid_request'));

        $status = GeneratedCodeRepo::delete_ecard($ecard->id, $pro_id, $mer_id);
        if($status) {
            ProductPricingRepo::update_related_pricing_quantity($pro_id, null, 'exist_item', -1);
            return back()->with('success', trans('localize.e-card.delete.success'));
        }

        return back()->with('success', trans('localize.e-card.delete.failed'));
    }
}